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

    /**
     * Handle incoming Kashier webhook (POST /api/webhooks/kashier).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        $signatureHeader = $request->header('x-kashier-signature');

        Log::info('Kashier webhook endpoint hit', [
            'payload' => $payload,
            'headers' => $request->headers->all(),
        ]);

        // 1. Signature Validation against calculated HMAC using secret key
        $isValid = $this->kashierDriver->verifySignature($payload, $signatureHeader);

        if (!$isValid) {
            Log::warning('Kashier webhook rejected: Invalid x-kashier-signature header or payload hash mismatch.');
            return response()->json([
                'error' => 'Invalid signature'
            ], 400);
        }

        // Extract transaction reference fields
        $orderNumber = $payload['data']['merchantOrderId']
            ?? $payload['merchantOrderId']
            ?? $payload['orderId']
            ?? null;

        $kashierTxId = $payload['data']['kashierOrderNumber']
            ?? $payload['kashierOrderNumber']
            ?? $payload['transactionId']
            ?? null;

        $cardBrand = $payload['data']['cardBrand']
            ?? $payload['cardBrand']
            ?? $payload['source_data']['sub_type']
            ?? null;

        $cardLastFourRaw = $payload['data']['cardLastFour']
            ?? $payload['cardLastFour']
            ?? $payload['source_data']['pan']
            ?? null;

        $cardLastFour = $cardLastFourRaw ? substr((string) $cardLastFourRaw, -4) : null;

        if (!$orderNumber) {
            Log::error('Kashier webhook missing order reference in payload', $payload);
            return response()->json([
                'error' => 'Missing merchantOrderId in webhook payload'
            ], 400);
        }

        // Find associated Transaction record
        $transaction = Transaction::where('gateway_code', 'kashier')
            ->where(function ($query) use ($orderNumber) {
                $query->whereHas('order', function ($orderQuery) use ($orderNumber) {
                    $orderQuery->where('order_number', $orderNumber);
                })->orWhere('order_id', $orderNumber)
                  ->orWhere('id', $orderNumber);
            })->first();

        if (!$transaction) {
            Log::warning("Kashier webhook: Transaction record not found for order number [{$orderNumber}]");
            return response()->json([
                'error' => 'Transaction record not found'
            ], 404);
        }

        // 2. Idempotency Check: Check if transaction status is already SUCCESS before processing
        if ($transaction->status === 'SUCCESS') {
            Log::info("Kashier webhook idempotency triggered: Transaction [{$transaction->id}] already marked SUCCESS.");
            return response()->json([
                'message' => 'Transaction already processed',
                'status' => 'SUCCESS',
                'transaction_id' => $transaction->id,
            ], 200);
        }

        // 3. Process Successful Payment & Student Enrollment
        try {
            DB::transaction(function () use ($transaction, $kashierTxId, $cardBrand, $cardLastFour, $payload) {
                // Update transaction status, card details & log full payload into gateway_response
                $transaction->update([
                    'status' => 'SUCCESS',
                    'gateway_transaction_id' => $kashierTxId,
                    'card_brand' => $cardBrand,
                    'card_last_four' => $cardLastFour,
                    'gateway_response' => $payload,
                ]);

                // Update associated order status
                if ($transaction->order) {
                    $transaction->order->update(['status' => 'PAID']);
                }

                // Trigger student enrollment logic
                $user = $transaction->user;
                $payable = $transaction->payable ?? ($transaction->order ? $transaction->order->payable : null);

                if ($user && $payable) {
                    $this->enrollStudent($user, $payable, $transaction);
                }
            });

            Log::info("Kashier webhook processed successfully for transaction [{$transaction->id}].");

            return response()->json([
                'message' => 'Webhook processed successfully',
                'status' => 'SUCCESS',
                'transaction_id' => $transaction->id,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Kashier webhook processing transaction error: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to process webhook transaction'
            ], 500);
        }
    }

    /**
     * Handle student enrollment for purchased payable item and dispatch StudentEnrolled event.
     *
     * @param mixed $user
     * @param mixed $payable
     * @param Transaction $transaction
     * @return void
     */
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

            Log::info("Student [{$user->id}] enrolled in Course [{$payable->id}] via Kashier payment.");
        } elseif ($payable instanceof CourseSection) {
            $this->sectionAccessService->grantAccess($user, $payable, $transaction->id, $transaction->amount);
            Log::info("Student [{$user->id}] granted access to CourseSection [{$payable->id}] via Kashier payment.");
        }

        // Fire StudentEnrolled event
        event(new StudentEnrolled($user, $payable));
    }
}
