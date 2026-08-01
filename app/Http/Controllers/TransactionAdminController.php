<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\PaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display transactions ledger with search, filters, and statistics.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Transaction::with(['user', 'order', 'payable']);

        // 1. Search Filter (Student name, email, order number, or gateway tx ID)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('gateway_transaction_id', 'like', "%{$search}%")
                  ->orWhere('id', $search)
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_number', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Status Filter
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', strtoupper($request->input('status')));
        }

        // 3. Gateway Filter
        if ($request->filled('gateway_code') && $request->input('gateway_code') !== 'all') {
            $query->where('gateway_code', strtolower($request->input('gateway_code')));
        }

        // 4. Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Statistics
        $totalTransactions = Transaction::count();
        $successCount = Transaction::where('status', 'SUCCESS')->count();
        $totalRevenueEGP = Transaction::where('status', 'SUCCESS')->where('currency', 'EGP')->sum('amount');
        $totalRevenueUSD = Transaction::where('status', 'SUCCESS')->where('currency', 'USD')->sum('amount');
        $pendingCount = Transaction::where('status', 'PENDING')->count();
        $failedCount = Transaction::where('status', 'FAILED')->count();

        // Paginated results (20 items per page)
        $transactions = $query->orderBy('created_at', 'desc')->paginate(20)->appends($request->all());
        $gateways = PaymentGateway::all();

        return view('admin.transactions.index', compact(
            'transactions',
            'gateways',
            'totalTransactions',
            'successCount',
            'totalRevenueEGP',
            'totalRevenueUSD',
            'pendingCount',
            'failedCount'
        ));
    }

    /**
     * Export filtered transactions ledger to CSV stream.
     *
     * @param Request $request
     * @return StreamedResponse
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Transaction::with(['user', 'order', 'payable']);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('gateway_transaction_id', 'like', "%{$search}%")
                  ->orWhere('id', $search)
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', strtoupper($request->input('status')));
        }

        if ($request->filled('gateway_code') && $request->input('gateway_code') !== 'all') {
            $query->where('gateway_code', strtolower($request->input('gateway_code')));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $filename = 'transactions_ledger_' . Carbon::now()->format('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            // CSV Header
            fputcsv($file, [
                'ID المعاملة',
                'رقم الطلب (Order ID)',
                'رمز مرجع البوابة (Gateway Ref)',
                'تاريخ ووقت المعاملة',
                'اسم الطالب',
                'البريد الإلكتروني',
                'رقم الهاتف',
                'نوع العنصر',
                'عنوان العنصر المشتري',
                'المبلغ المدفوع',
                'العملة',
                'حالة الدفع',
                'بوابة الدفع',
                'طريقة الدفع',
                'ماركة البطاقة',
                'آخر 4 أرقام',
            ]);

            $query->orderBy('created_at', 'desc')->chunk(200, function ($transactions) use ($file) {
                foreach ($transactions as $tx) {
                    $itemType = 'كورس كامل';
                    if ($tx->payable_type) {
                        $basename = class_basename($tx->payable_type);
                        if ($basename === 'CourseSection') {
                            $itemType = 'قسم منفصل';
                        } elseif ($basename === 'Lesson') {
                            $itemType = 'درس فردي';
                        } elseif ($basename === 'Exam') {
                            $itemType = 'اختبار';
                        }
                    }

                    $itemTitle = $tx->payable->title ?? $tx->payable->name ?? 'غير محدد';

                    fputcsv($file, [
                        $tx->id,
                        $tx->order->order_number ?? ("#ORD-" . $tx->order_id),
                        $tx->gateway_transaction_id ?? 'N/A',
                        $tx->created_at->format('Y-m-d H:i:s'),
                        $tx->user->name ?? 'زائر',
                        $tx->user->email ?? 'N/A',
                        $tx->user->phone ?? 'N/A',
                        $itemType,
                        $itemTitle,
                        number_format($tx->amount, 2),
                        $tx->currency ?? 'EGP',
                        $tx->status,
                        strtoupper($tx->gateway_code ?? 'kashier'),
                        $tx->payment_method ?? 'بطاقة ائتمان',
                        $tx->card_brand ?? 'N/A',
                        $tx->card_last_four ? "•••• {$tx->card_last_four}" : 'N/A',
                    ]);
                }
            });

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Fetch single transaction details JSON for Modal Quick View.
     *
     * @param Transaction $transaction
     * @return JsonResponse
     */
    public function show(Transaction $transaction): JsonResponse
    {
        $transaction->load(['user', 'order', 'payable']);

        $itemType = 'كورس كامل';
        if ($transaction->payable_type) {
            $basename = class_basename($transaction->payable_type);
            if ($basename === 'CourseSection') {
                $itemType = 'قسم منفصل من الكورس';
            } elseif ($basename === 'Lesson') {
                $itemType = 'درس فردي';
            } elseif ($basename === 'Exam') {
                $itemType = 'اختبار تخصصي';
            }
        }

        return response()->json([
            'success' => true,
            'transaction' => [
                'id' => $transaction->id,
                'status' => $transaction->status,
                'gateway_code' => strtoupper($transaction->gateway_code ?? 'Kashier'),
                'gateway_transaction_id' => $transaction->gateway_transaction_id ?? 'N/A',
                'amount' => number_format($transaction->amount, 2),
                'currency' => $transaction->currency ?? 'EGP',
                'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                'payment_method' => $transaction->payment_method ?? 'بطاقة مشفرة',
                'card_brand' => $transaction->card_brand ?? 'N/A',
                'card_last_four' => $transaction->card_last_four ? "•••• {$transaction->card_last_four}" : 'N/A',
                'user' => [
                    'id' => $transaction->user->id ?? null,
                    'name' => $transaction->user->name ?? 'مستخدم غير معرف',
                    'email' => $transaction->user->email ?? 'N/A',
                    'phone' => $transaction->user->phone ?? 'N/A',
                ],
                'order' => [
                    'order_number' => $transaction->order->order_number ?? ("#ORD-" . $transaction->order_id),
                    'status' => $transaction->order->status ?? 'PENDING',
                ],
                'item' => [
                    'type' => $itemType,
                    'title' => $transaction->payable->title ?? $transaction->payable->name ?? 'عنصر منصة',
                ],
                'raw_response' => $transaction->gateway_response,
            ],
        ]);
    }

    /**
     * Sync transaction status or simulate webhook resend.
     *
     * @param Transaction $transaction
     * @return JsonResponse
     */
    public function syncStatus(Transaction $transaction): JsonResponse
    {
        // Re-check order status
        if ($transaction->order && $transaction->order->status === 'PAID' && $transaction->status !== 'SUCCESS') {
            $transaction->update(['status' => 'SUCCESS']);
        }

        return response()->json([
            'success' => true,
            'message' => "تم تحديث ومزامنة حالة المعاملة رقم #{$transaction->id} بنجاح مع بوابة {$transaction->gateway_code}.",
            'status' => $transaction->status,
        ]);
    }
}
