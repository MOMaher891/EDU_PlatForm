@extends('layouts.app')

@section('title', 'إدارة المدفوعات')

@section('content')
<div class="admin-payments-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="page-title">
                        <i class="fas fa-credit-card me-3"></i>
                        إدارة المدفوعات
                    </h1>
                    <p class="page-subtitle">إدارة وتتبع جميع المدفوعات في المنصة</p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="fas fa-download me-2"></i>
                            تصدير البيانات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-primary" data-aos="fade-up">
                    <div class="stats-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $payments->total() }}</h3>
                        <p class="stats-label">إجمالي المدفوعات</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-success" data-aos="fade-up" data-aos-delay="100">
                    <div class="stats-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $payments->where('status', 'completed')->count() }}</h3>
                        <p class="stats-label">المدفوعات المكتملة</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-warning" data-aos="fade-up" data-aos-delay="200">
                    <div class="stats-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $payments->where('status', 'pending')->count() }}</h3>
                        <p class="stats-label">المدفوعات المعلقة</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-info" data-aos="fade-up" data-aos-delay="300">
                    <div class="stats-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">${{ number_format($payments->where('status', 'completed')->sum('amount'), 2) }}</h3>
                        <p class="stats-label">إجمالي الإيرادات</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="filters-section mb-4" data-aos="fade-up">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">حالة الدفع</label>
                            <select class="form-select" name="status">
                                <option value="">جميع الحالات</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="fas fa-search me-2"></i>
                                    بحث
                                </button>
                                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>
                                    مسح
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="payments-table-section" data-aos="fade-up">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-list me-2"></i>
                            قائمة المدفوعات
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted">إجمالي النتائج: {{ $payments->total() }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">#</th>
                                    <th class="border-0">المستخدم</th>
                                    <th class="border-0">الكورس</th>
                                    <th class="border-0">المبلغ</th>
                                    <th class="border-0">طريقة الدفع</th>
                                    <th class="border-0">الحالة</th>
                                    <th class="border-0">التاريخ</th>
                                    <th class="border-0">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-primary bg-opacity-10 rounded-circle">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $payment->user->name ?? 'غير محدد' }}</h6>
                                                <small class="text-muted">{{ $payment->user->email ?? 'غير محدد' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-success bg-opacity-10 rounded-circle">
                                                    <i class="fas fa-book text-success"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $payment->course->title ?? 'غير محدد' }}</h6>
                                                <small class="text-muted">{{ $payment->course->category->name ?? 'غير محدد' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">${{ number_format($payment->amount, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-credit-card me-1"></i>
                                            {{ $payment->payment_method ?? 'غير محدد' }}
                                        </span>
                                    </td>
                                    <td>
                                        @switch($payment->status)
                                            @case('completed')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    مكتمل
                                                </span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>
                                                    معلق
                                                </span>
                                                @break
                                            @case('failed')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>
                                                    فشل
                                                </span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-ban me-1"></i>
                                                    ملغي
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">
                                                    <i class="fas fa-question-circle me-1"></i>
                                                    غير محدد
                                                </span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">{{ $payment->created_at->format('Y/m/d') }}</div>
                                            <small class="text-muted">{{ $payment->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.payments.show', $payment) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">لا توجد مدفوعات</h5>
                                            <p class="text-muted">لم يتم العثور على أي مدفوعات تطابق معايير البحث</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
        <div class="pagination-section mt-4" data-aos="fade-up">
            <div class="d-flex justify-content-center">
                {{ $payments->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">
                    <i class="fas fa-download me-2"></i>
                    تصدير البيانات
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="GET" action="{{ route('admin.payments.index') }}">
                    <div class="mb-3">
                        <label class="form-label">نوع التصدير</label>
                        <select class="form-select" name="export_type">
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الفترة الزمنية</label>
                        <select class="form-select" name="export_period">
                            <option value="all">جميع البيانات</option>
                            <option value="today">اليوم</option>
                            <option value="week">هذا الأسبوع</option>
                            <option value="month">هذا الشهر</option>
                            <option value="year">هذا العام</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>
                    تصدير
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.admin-payments-page {
    background-color: #f8f9fa;
    min-height: 100vh;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    opacity: 0.9;
    margin-bottom: 0;
}

.stats-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: transform 0.2s ease-in-out;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}

.stats-primary .stats-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stats-success .stats-icon {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.stats-warning .stats-icon {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: white;
}

.stats-info .stats-icon {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
    color: white;
}

.stats-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stats-label {
    color: #6c757d;
    margin-bottom: 0;
}

.filters-section {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.payments-table-section {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.avatar-sm {
    width: 40px;
    height: 40px;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-state i {
    opacity: 0.5;
}

.table th {
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
}

.btn-group .btn {
    border-radius: 0.375rem;
}

.pagination-section {
    background: white;
    border-radius: 1rem;
    padding: 1rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style>
@endsection
