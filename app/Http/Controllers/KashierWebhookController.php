<?php

namespace App\Http\Controllers;

use App\Events\StudentEnrolled;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseSection;
use App\Models\Transaction;
use App\Services\Payment\Drivers\KashierDriver;
use App\Services\SectionAccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KashierWebhookController extends Controller
{
    protected KashierDriver $kashierDriver;
    protected SectionAccessService $sectionAccessService;

    public function __construct(KashierDriver $kashierDriver, SectionAccessService $sectionAccessService)
    {
        $this->kashierDriver = $kashierDriver;
        $this->sectionAccessService = $sectionAccessService;
    }

    public function handle(Request $request): JsonResponse
    {
        // 0. GET Health Check for Browser Tests
        if ($request->isMethod('get') && !$request->hasHeader('x-kashier-signature')) {
            return response()->json([
                'status' => 'active',
                'message' => 'Kashier Webhook Endpoint is running smoothly.'
            ], 200);
        }

        $payload = $request->all();
        $signatureHeader = $request->header('x-kashier-signature');

        Log::info('Kashier Webhook Payload Received', [
            'payload' => $payload,
            'headers' => $request->headers->all(),
        ]);

        // 1. Signature Verification
        $isValid = $this->kashierDriver->verifySignature($payload, $signatureHeader);

        if (!$isValid) {
            Log::warning('Kashier webhook rejected: Signature mismatch.');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $data = $payload['data'] ?? $payload;
        $event = $payload['event'] ?? 'pay';
        $paymentStatus = $data['status'] ?? null;

        // Skip non-successful notifications
        if ($paymentStatus !== 'SUCCESS') {
            Log::info("Kashier webhook ignored status: {$paymentStatus}");
            return response()->json(['message' => 'Status ignored'], 200);
        }

        // Extract merchantOrderId
        $orderNumber = $data['merchantOrderId'] ?? $data['orderId'] ?? null;
        $kashierTxId = $data['transactionId'] ?? $data['kashierOrderId'] ?? null;

        $cardBrand = $data['card']['cardInfo']['cardBrand'] ?? $data['method'] ?? null;
        $maskedCard = $data['card']['cardInfo']['maskedCard'] ?? null;
        $cardLastFour = $maskedCard ? substr($maskedCard, -4) : null;

        if (!$orderNumber) {
            Log::error('Kashier webhook missing merchantOrderId', $payload);
            return response()->json(['error' => 'Missing merchantOrderId'], 400);
        }

        // Extract base order number (e.g., ORD-102-178560000 -> ORD-102)
        $baseOrderNumber = $orderNumber;
        if (preg_match('/^(ORD-[A-Z0-9]+)-\d+$/i', $orderNumber, $matches)) {
            $baseOrderNumber = $matches[1];
        }

        // 2. Find associated Transaction record
        $transaction = Transaction::where('gateway_code', 'kashier')
            ->where(function ($query) use ($baseOrderNumber, $orderNumber) {
                $query->whereHas('order', function ($orderQuery) use ($baseOrderNumber) {
                    $orderQuery->where('order_number', $baseOrderNumber);
                })
                ->orWhere('order_id', $baseOrderNumber)
                ->orWhere('id', $baseOrderNumber)
                ->orWhere('order_id', $orderNumber);
            })->first();

        // Fallback: If not found by order_number, query latest pending transaction
        if (!$transaction) {
            $transaction = Transaction::where('gateway_code', 'kashier')
                ->where('status', 'PENDING')
                ->latest()
                ->first();
        }

        if (!$transaction) {
            Log::warning("Kashier webhook: Transaction record not found for [{$orderNumber}]");
            return response()->json(['error' => 'Transaction record not found'], 404);
        }

        // 3. Idempotency Check
        if ($transaction->status === 'SUCCESS') {
            Log::info("Kashier webhook idempotency: Transaction [{$transaction->id}] already SUCCESS.");
            return response()->json([
                'message' => 'Transaction already processed',
                'status' => 'SUCCESS',
                'transaction_id' => $transaction->id,
            ], 200);
        }

        // 4. Update Database & Unlock Course
        try {
            DB::transaction(function () use ($transaction, $kashierTxId, $cardBrand, $cardLastFour, $payload) {
                $transaction->update([
                    'status' => 'SUCCESS',
                    'gateway_transaction_id' => $kashierTxId,
                    'card_brand' => $cardBrand,
                    'card_last_four' => $cardLastFour,
                    'gateway_response' => $payload,
                ]);

                if ($transaction->order) {
                    $transaction->order->update(['status' => 'PAID']);
                }

                $user = $transaction->user;
                $payable = $transaction->payable ?? ($transaction->order ? $transaction->order->payable : null);

                if ($user && $payable) {
                    $this->enrollStudent($user, $payable, $transaction);
                }
            });

            Log::info("Kashier webhook successfully processed. Transaction ID [{$transaction->id}].");

            return response()->json([
                'message' => 'Webhook processed successfully',
                'status' => 'SUCCESS',
                'transaction_id' => $transaction->id,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Kashier webhook processing error: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Failed to process transaction'], 500);
        }
    }

    protected function enrollStudent($user, $payable, Transaction $transaction): void
    {
        if ($payable instanceof Course) {
            CourseEnrollment::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $payable->id,
                ],
                [
                    'enrolled_at' => now(),
                    'progress' => 0,
                ]
            );

            Log::info("Student [{$user->id}] enrolled in Course [{$payable->id}] via Kashier.");
        } elseif ($payable instanceof CourseSection) {
            $this->sectionAccessService->grantAccess($user, $payable, $transaction->id, $transaction->amount);
            Log::info("Student [{$user->id}] granted access to CourseSection [{$payable->id}] via Kashier.");
        }

        event(new StudentEnrolled($user, $payable));
    }
}