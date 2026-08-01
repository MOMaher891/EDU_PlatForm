@extends('layouts.app')

@section('title', 'سجل المدفوعات والمعاملات المالية')

@section('content')
<div class="admin-transactions-page">
    <!-- Page Header -->
    <div class="page-header mb-4" data-aos="fade-down">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h1 class="page-title">
                        <i class="fas fa-file-invoice-dollar text-emerald-400 me-2"></i>
                        سجل المعاملات المالية (Transactions Ledger)
                    </h1>
                    <p class="page-subtitle">متابعة كافة عمليات الدفع الإلكترونية، تفاصيل البطاقات، وحالات المعاملات لحظياً</p>
                </div>
                <div class="col-md-5 text-end mt-3 mt-md-0">
                    <a href="{{ route('admin.transactions.export', request()->all()) }}" class="btn btn-emerald rounded-3 shadow-sm px-4">
                        <i class="fas fa-file-csv me-2"></i>
                        تصدير التقرير (CSV)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Quick Financial Stats -->
        <div class="row g-4 mb-4" data-aos="fade-up">
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="stat-icon-wrap bg-emerald-50 text-emerald-600 rounded-3 p-3 me-3">
                            <i class="fas fa-hand-holding-usd fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small fw-bold mb-1">إجمالي الإيرادات (EGP)</h6>
                            <h3 class="fw-bold mb-0 text-emerald-600">{{ number_format($totalRevenueEGP, 2) }} ج.م</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="stat-icon-wrap bg-indigo-50 text-indigo-600 rounded-3 p-3 me-3">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small fw-bold mb-1">إجمالي الإيرادات (USD)</h6>
                            <h3 class="fw-bold mb-0 text-indigo-600">${{ number_format($totalRevenueUSD, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="stat-icon-wrap bg-emerald-100 text-emerald-700 rounded-3 p-3 me-3">
                            <i class="fas fa-check-double fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small fw-bold mb-1">المعاملات الناجحة</h6>
                            <h3 class="fw-bold mb-0 text-slate-800">{{ number_format($successCount) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="stat-icon-wrap bg-amber-50 text-amber-600 rounded-3 p-3 me-3">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small fw-bold mb-1">المعاملات المعلقة / الفاشلة</h6>
                            <h3 class="fw-bold mb-0 text-amber-600">{{ $pendingCount }} معلقة / {{ $failedCount }} فاشلة</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-up">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.transactions.index') }}" class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small fw-bold text-slate-700">بحث عام (اسم، بريد، طلب، مرجع البوابة):</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-slate-400 border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-start-0" name="search"
                                   value="{{ request('search') }}" placeholder="أدخل اسم الطالب، الإيميل، رقم الطلب...">
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="form-label small fw-bold text-slate-700">حالة المعاملة:</label>
                        <select class="form-select" name="status">
                            <option value="all">كل الحالات</option>
                            <option value="SUCCESS" {{ request('status') === 'SUCCESS' ? 'selected' : '' }}>ناجحة (SUCCESS)</option>
                            <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>معلقة (PENDING)</option>
                            <option value="FAILED" {{ request('status') === 'FAILED' ? 'selected' : '' }}>فاشلة (FAILED)</option>
                            <option value="REFUNDED" {{ request('status') === 'REFUNDED' ? 'selected' : '' }}>مسترجعة (REFUNDED)</option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="form-label small fw-bold text-slate-700">بوابة الدفع:</label>
                        <select class="form-select" name="gateway_code">
                            <option value="all">كافة البوابات</option>
                            @foreach($gateways as $gw)
                                <option value="{{ $gw->code }}" {{ request('gateway_code') === $gw->code ? 'selected' : '' }}>{{ $gw->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="form-label small fw-bold text-slate-700">من تاريخ:</label>
                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="form-label small fw-bold text-slate-700">إلى تاريخ:</label>
                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-lg-1 col-md-4 col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-indigo w-100 py-2" title="تطبيق الفلتر">
                            <i class="fas fa-filter"></i>
                        </button>
                        <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary py-2" title="إعادة تعيين">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- High-Density Transactions Data Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" data-aos="fade-up">
            <div class="card-header bg-transparent border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-slate-900">
                    <i class="fas fa-list me-2 text-indigo-600"></i>
                    سجل عمليات الدفع الإلكتروني ({{ $transactions->total() }} معاملة)
                </h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 custom-ledger-table">
                        <thead class="bg-slate-100 text-slate-700 small text-uppercase">
                            <tr>
                                <th class="ps-4"># المعاملة / المرور</th>
                                <th>التاريخ والوقت</th>
                                <th>تفاصيل الطالب</th>
                                <th>العنصر المشتري</th>
                                <th>المبلغ النهائي</th>
                                <th>وسيلة الدفع والبطاقة</th>
                                <th>الحالة</th>
                                <th class="pe-4 text-end">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $tx)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-slate-900">#{{ $tx->id }}</div>
                                    <div class="small text-slate-500 font-monospace">
                                        <i class="fas fa-receipt text-indigo-400 me-1"></i>
                                        {{ $tx->order->order_number ?? ("ORD-" . $tx->order_id) }}
                                    </div>
                                    @if($tx->gateway_transaction_id)
                                        <span class="badge bg-slate-100 text-slate-600 font-monospace border mt-1">
                                            Ref: {{ Str::limit($tx->gateway_transaction_id, 14) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-slate-800">{{ $tx->created_at->format('Y-m-d') }}</div>
                                    <div class="small text-slate-500"><i class="far fa-clock me-1"></i>{{ $tx->created_at->format('H:i A') }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="student-avatar bg-indigo-50 text-indigo-600 rounded-circle fw-bold d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                            {{ mb_substr($tx->user->name ?? 'ز', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-slate-900">{{ $tx->user->name ?? 'مستخدم غير معرف' }}</div>
                                            <div class="small text-slate-500">{{ $tx->user->email ?? 'N/A' }}</div>
                                            @if($tx->user->phone)
                                                <div class="small text-slate-400" dir="ltr"><i class="fas fa-phone-alt me-1"></i>{{ $tx->user->phone }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $itemTypeLabel = 'كورس كامل';
                                        $itemBadgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                        if ($tx->payable_type) {
                                            $basename = class_basename($tx->payable_type);
                                            if ($basename === 'CourseSection') {
                                                $itemTypeLabel = 'قسم منفصل';
                                                $itemBadgeClass = 'bg-violet-50 text-violet-700 border-violet-200';
                                            } elseif ($basename === 'Lesson') {
                                                $itemTypeLabel = 'درس فردي';
                                                $itemBadgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                            } elseif ($basename === 'Exam') {
                                                $itemTypeLabel = 'اختبار';
                                                $itemBadgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                            }
                                        }
                                    @endphp
                                    <span class="badge border {{ $itemBadgeClass }} mb-1">
                                        {{ $itemTypeLabel }}
                                    </span>
                                    <div class="fw-semibold text-slate-800 small text-truncate" style="max-width: 200px;" title="{{ $tx->payable->title ?? $tx->payable->name ?? 'غير محدد' }}">
                                        {{ $tx->payable->title ?? $tx->payable->name ?? 'عنصر منصة' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-slate-900 fs-6">
                                        {{ number_format($tx->amount, 2) }} <span class="small text-indigo-600">{{ $tx->currency ?? 'EGP' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-slate-900 text-white rounded-2 px-2 py-1 small">
                                            {{ strtoupper($tx->gateway_code ?? 'Kashier') }}
                                        </span>
                                        <span class="small text-slate-600 fw-semibold">
                                            {{ $tx->payment_method ?? 'بطاقة ائتمان' }}
                                        </span>
                                    </div>
                                    @if($tx->card_brand || $tx->card_last_four)
                                        <div class="small font-monospace text-slate-500">
                                            <i class="far fa-credit-card me-1 text-slate-400"></i>
                                            {{ strtoupper($tx->card_brand ?? 'Card') }}
                                            @if($tx->card_last_four)
                                                <span class="fw-bold text-slate-700">•••• {{ $tx->card_last_four }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($tx->status === 'SUCCESS')
                                        <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-300 py-1.5 px-3 rounded-pill fw-bold">
                                            <i class="fas fa-check-circle me-1"></i> مكتملة (SUCCESS)
                                        </span>
                                    @elseif($tx->status === 'PENDING')
                                        <span class="badge bg-amber-100 text-amber-800 border border-amber-300 py-1.5 px-3 rounded-pill fw-bold">
                                            <i class="fas fa-spinner fa-spin me-1"></i> قيد الانتظار (PENDING)
                                        </span>
                                    @elseif($tx->status === 'FAILED')
                                        <span class="badge bg-rose-100 text-rose-800 border border-rose-300 py-1.5 px-3 rounded-pill fw-bold">
                                            <i class="fas fa-times-circle me-1"></i> فاشلة (FAILED)
                                        </span>
                                    @else
                                        <span class="badge bg-purple-100 text-purple-800 border border-purple-300 py-1.5 px-3 rounded-pill fw-bold">
                                            <i class="fas fa-undo me-1"></i> مسترجعة (REFUNDED)
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-indigo rounded-3 view-details-btn"
                                                data-id="{{ $tx->id }}" title="عرض التفاصيل والـ Payload">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 sync-status-btn"
                                                data-id="{{ $tx->id }}" title="مزامنة وتحديث الحالة">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-slate-400 mb-2"><i class="fas fa-inbox fa-3x"></i></div>
                                    <h6 class="fw-bold text-slate-600">لا توجد معاملات عملية متاحة في السجل</h6>
                                    <p class="small text-slate-400 mb-0">جرّب تغيير خيارات البحث أو الفلتر أعلاه</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($transactions->hasPages())
                <div class="card-footer bg-white border-0 p-4">
                    <div class="d-flex justify-content-center">
                        {{ $transactions->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Quick View Transaction Details -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-slate-900 text-white p-4">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <i class="fas fa-receipt text-emerald-400"></i>
                    تفاصيل المعاملة المالية رقم <span id="modalTxId">#--</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalTxBody">
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-indigo-600"></i>
                    <p class="mt-2 text-slate-500">جاري تحميل بيانات المعاملة والـ Payload...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.admin-transactions-page {
    background-color: #f8fafc;
    min-height: 100vh;
    direction: rtl;
    text-align: right;
    font-family: 'Cairo', system-ui, -apple-system, sans-serif;
}

.page-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: white;
    padding: 2.2rem 0;
    border-radius: 0 0 24px 24px;
}

.page-title {
    font-size: 1.85rem;
    font-weight: 800;
}

.page-subtitle {
    opacity: 0.85;
    font-size: 0.95rem;
}

.btn-emerald {
    background-color: #10b981;
    color: white;
    font-weight: 700;
    border: none;
    transition: all 0.2s ease;
}

.btn-emerald:hover {
    background-color: #059669;
    color: white;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
}

.btn-indigo {
    background-color: #4f46e5;
    color: white;
    font-weight: 700;
    border: none;
}

.btn-indigo:hover {
    background-color: #4338ca;
    color: white;
}

.btn-outline-indigo {
    border-color: #6366f1;
    color: #4f46e5;
}

.btn-outline-indigo:hover {
    background-color: #4f46e5;
    color: white;
}

.custom-ledger-table th {
    font-weight: 700;
    letter-spacing: 0.5px;
    padding: 1rem 0.75rem;
}

.custom-ledger-table td {
    padding: 1rem 0.75rem;
}

.bg-emerald-50 { background-color: #ecfdf5; }
.text-emerald-600 { color: #059669; }
.text-emerald-400 { color: #34d399; }
.bg-emerald-100 { background-color: #d1fae5; }
.text-emerald-800 { color: #065f46; }

.bg-indigo-50 { background-color: #eef2ff; }
.text-indigo-600 { color: #4f46e5; }

.bg-violet-50 { background-color: #f5f3ff; }
.text-violet-700 { color: #6d28d9; }

.bg-amber-50 { background-color: #fffbeb; }
.text-amber-600 { color: #d97706; }
.bg-amber-100 { background-color: #fef3c7; }
.text-amber-800 { color: #92400e; }

.bg-rose-100 { background-color: #ffe4e6; }
.text-rose-800 { color: #9f1239; }

.bg-purple-100 { background-color: #f3e8ff; }
.text-purple-800 { color: #6b21a8; }

.bg-slate-100 { background-color: #f1f5f9; }
.bg-slate-900 { background-color: #0f172a; }
.text-slate-900 { color: #0f172a; }
.text-slate-800 { color: #1e293b; }
.text-slate-700 { color: #334155; }
.text-slate-600 { color: #475569; }
.text-slate-500 { color: #64748b; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Quick View Details Modal Handler
    document.querySelectorAll('.view-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const txId = this.dataset.id;
            const modalElement = document.getElementById('transactionDetailsModal');
            const bsModal = new bootstrap.Modal(modalElement);
            
            document.getElementById('modalTxId').textContent = `#${txId}`;
            const bodyEl = document.getElementById('modalTxBody');
            
            bodyEl.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-indigo-600"></i>
                    <p class="mt-2 text-slate-500">جاري تحميل بيانات المعاملة والـ Payload...</p>
                </div>
            `;
            
            bsModal.show();

            fetch(`/admin/transactions/${txId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const tx = data.transaction;
                    const jsonFormatted = tx.raw_response ? JSON.stringify(tx.raw_response, null, 2) : 'لا يوجد استجابة خام مسجلة';
                    
                    bodyEl.innerHTML = `
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-slate-50 rounded-3 border">
                                    <h6 class="fw-bold text-slate-800 mb-2"><i class="fas fa-user text-indigo-600 me-1"></i> الطالب المشترِ:</h6>
                                    <div><strong>الاسم:</strong> ${tx.user.name}</div>
                                    <div><strong>الإيميل:</strong> ${tx.user.email}</div>
                                    <div><strong>رقم الهاتف:</strong> ${tx.user.phone}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-slate-50 rounded-3 border">
                                    <h6 class="fw-bold text-slate-800 mb-2"><i class="fas fa-file-invoice text-indigo-600 me-1"></i> بيانات الطلب والمعاملة:</h6>
                                    <div><strong>رقم الطلب:</strong> ${tx.order.order_number}</div>
                                    <div><strong>بوابة الدفع:</strong> <span class="badge bg-slate-900">${tx.gateway_code}</span></div>
                                    <div><strong>مرجع البوابة:</strong> ${tx.gateway_transaction_id}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-slate-50 rounded-3 border">
                                    <h6 class="fw-bold text-slate-800 mb-2"><i class="fas fa-credit-card text-indigo-600 me-1"></i> بيانات البطاقة الوسيلة:</h6>
                                    <div><strong>الوسيلة:</strong> ${tx.payment_method}</div>
                                    <div><strong>الشركة/النوع:</strong> ${tx.card_brand}</div>
                                    <div><strong>آخر 4 أرقام:</strong> ${tx.card_last_four}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-slate-50 rounded-3 border">
                                    <h6 class="fw-bold text-slate-800 mb-2"><i class="fas fa-shopping-bag text-indigo-600 me-1"></i> العنصر والمبلغ:</h6>
                                    <div><strong>نوع العنصر:</strong> ${tx.item.type}</div>
                                    <div><strong>عنوان العنصر:</strong> ${tx.item.title}</div>
                                    <div><strong>المبلغ المدفوع:</strong> <span class="fw-bold text-emerald-600 fs-5">${tx.amount} ${tx.currency}</span></div>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold text-slate-800 mb-2">
                            <i class="fas fa-code text-indigo-600 me-1"></i>
                            استجابة البوابة الخام (Raw Gateway Response Payload):
                        </h6>
                        <pre class="bg-slate-900 text-emerald-400 p-3 rounded-3 font-monospace small" style="max-height: 250px; overflow-y: auto;"><code>${jsonFormatted}</code></pre>
                    `;
                } else {
                    bodyEl.innerHTML = `<div class="alert alert-danger">فشل في تحميل بيانات المعاملة.</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                bodyEl.innerHTML = `<div class="alert alert-danger">حدث خطأ أثناء الاتصال بالخادوم.</div>`;
            });
        });
    });

    // Sync Status Button Handler
    document.querySelectorAll('.sync-status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const txId = this.dataset.id;
            const icon = this.querySelector('i');
            icon.classList.add('fa-spin');

            fetch(`/admin/transactions/${txId}/sync`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                icon.classList.remove('fa-spin');
                if (data.success) {
                    showToast('مزامنة بنجاح', data.message, 'success');
                } else {
                    showToast('خطأ', 'فشل في مزامنة المعاملة', 'error');
                }
            })
            .catch(err => {
                icon.classList.remove('fa-spin');
                console.error(err);
                showToast('خطأ', 'حدث خطأ أثناء مزامنة المعاملة', 'error');
            });
        });
    });
});

function showToast(title, message, type = 'success') {
    const bgClass = type === 'success' ? 'bg-emerald-600' : 'bg-danger';
    const toastDiv = document.createElement('div');
    toastDiv.className = `toast align-items-center text-white ${bgClass} border-0 position-fixed top-0 end-0 m-4 show`;
    toastDiv.style.zIndex = '9999';
    toastDiv.innerHTML = `
        <div class="d-flex">
            <div class="toast-body font-bold">
                <strong>${title}:</strong> ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    document.body.appendChild(toastDiv);
    setTimeout(() => {
        if (document.body.contains(toastDiv)) toastDiv.remove();
    }, 4000);
}
</script>
@endpush
