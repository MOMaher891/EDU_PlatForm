@extends('layouts.app')

@section('title', 'تفاصيل الدفع')

@section('content')
<div class="admin-payment-details-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="page-title">
                        <i class="fas fa-credit-card me-3"></i>
                        تفاصيل الدفع
                    </h1>
                    <p class="page-subtitle">عرض تفاصيل الدفع رقم #{{ $payment->id }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-2"></i>
                            العودة للقائمة
                        </a>
                        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>
                            طباعة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Payment Details -->
            <div class="col-lg-8">
                <div class="payment-details-card" data-aos="fade-up">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                تفاصيل الدفع
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <label class="detail-label">رقم الدفع</label>
                                        <div class="detail-value">#{{ $payment->id }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <label class="detail-label">حالة الدفع</label>
                                        <div class="detail-value">
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
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <label class="detail-label">المبلغ</label>
                                        <div class="detail-value">
                                            <span class="fw-bold text-success fs-4">${{ number_format($payment->amount, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <label class="detail-label">العملة</label>
                                        <div class="detail-value">{{ $payment->currency ?? 'USD' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <label class="detail-label">طريقة الدفع</label>
                                        <div class="detail-value">
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-credit-card me-1"></i>
                                                {{ $payment->payment_method ?? 'غير محدد' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <label class="detail-label">رقم المعاملة</label>
                                        <div class="detail-value">{{ $payment->transaction_id ?? 'غير محدد' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <label class="detail-label">تاريخ الإنشاء</label>
                                        <div class="detail-value">{{ $payment->created_at->format('Y/m/d H:i:s') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <label class="detail-label">آخر تحديث</label>
                                        <div class="detail-value">{{ $payment->updated_at->format('Y/m/d H:i:s') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Data -->
                @if($payment->transaction_data)
                <div class="payment-data-card mt-4" data-aos="fade-up">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-database me-2"></i>
                                بيانات الدفع الإضافية
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <pre class="bg-light p-3 rounded"><code>{{ json_encode($payment->transaction_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- User Information -->
                <div class="user-info-card mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-user me-2"></i>
                                معلومات المستخدم
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @if($payment->user)
                            <div class="user-avatar text-center mb-3">
                                <div class="avatar-lg mx-auto">
                                    <div class="avatar-title bg-primary bg-opacity-10 rounded-circle">
                                        <i class="fas fa-user fa-2x text-primary"></i>
                                    </div>
                                </div>
                                <h6 class="mt-2 mb-1">{{ $payment->user->name }}</h6>
                                <small class="text-muted">{{ $payment->user->email }}</small>
                            </div>
                            <div class="user-details">
                                <div class="detail-item">
                                    <label class="detail-label">نوع المستخدم</label>
                                    <div class="detail-value">
                                        @if($payment->user->isAdmin())
                                            <span class="badge bg-danger">مدير</span>
                                        @elseif($payment->user->isInstructor())
                                            <span class="badge bg-warning">مدرس</span>
                                        @else
                                            <span class="badge bg-info">طالب</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <label class="detail-label">تاريخ التسجيل</label>
                                    <div class="detail-value">{{ $payment->user->created_at->format('Y/m/d') }}</div>
                                </div>
                            </div>
                            @else
                            <div class="text-center text-muted">
                                <i class="fas fa-user-slash fa-3x mb-3"></i>
                                <p>المستخدم غير موجود</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Course Information -->
                <div class="course-info-card mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-book me-2"></i>
                                معلومات الكورس
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @if($payment->course)
                            <div class="course-avatar text-center mb-3">
                                <div class="avatar-lg mx-auto">
                                    <div class="avatar-title bg-success bg-opacity-10 rounded-circle">
                                        <i class="fas fa-book fa-2x text-success"></i>
                                    </div>
                                </div>
                                <h6 class="mt-2 mb-1">{{ $payment->course->title }}</h6>
                                <small class="text-muted">{{ $payment->course->category->name ?? 'غير محدد' }}</small>
                            </div>
                            <div class="course-details">
                                <div class="detail-item">
                                    <label class="detail-label">السعر</label>
                                    <div class="detail-value">${{ number_format($payment->course->price ?? 0, 2) }}</div>
                                </div>
                                <div class="detail-item">
                                    <label class="detail-label">الحالة</label>
                                    <div class="detail-value">
                                        @if($payment->course->is_published)
                                            <span class="badge bg-success">منشور</span>
                                        @else
                                            <span class="badge bg-warning">مسودة</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <label class="detail-label">تاريخ الإنشاء</label>
                                    <div class="detail-value">{{ $payment->course->created_at->format('Y/m/d') }}</div>
                                </div>
                            </div>
                            @else
                            <div class="text-center text-muted">
                                <i class="fas fa-book fa-3x mb-3"></i>
                                <p>الكورس غير موجود</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-bolt me-2"></i>
                                إجراءات سريعة
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-grid gap-2">
                                @if($payment->user)
                                <a href="{{ route('admin.users.show', $payment->user) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-user me-2"></i>
                                    عرض ملف المستخدم
                                </a>
                                @endif
                                @if($payment->course)
                                <a href="{{ route('admin.courses.show', $payment->course) }}" class="btn btn-outline-success">
                                    <i class="fas fa-book me-2"></i>
                                    عرض الكورس
                                </a>
                                @endif
                                <button type="button" class="btn btn-outline-info" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>
                                    طباعة الفاتورة
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-payment-details-page {
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

.payment-details-card,
.user-info-card,
.course-info-card,
.quick-actions-card,
.payment-data-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.detail-item {
    margin-bottom: 1.5rem;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.detail-label {
    display: block;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.detail-value {
    font-size: 1rem;
    color: #212529;
}

.avatar-lg {
    width: 80px;
    height: 80px;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
}

.user-details,
.course-details {
    border-top: 1px solid #e9ecef;
    padding-top: 1rem;
}

pre {
    font-size: 0.875rem;
    margin-bottom: 0;
}

@media print {
    .page-header,
    .quick-actions-card,
    .btn {
        display: none !important;
    }

    .admin-payment-details-page {
        background-color: white !important;
    }

    .card {
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
    }
}
</style>
@endsection
