@extends('layouts.app')

@section('title', 'تفاصيل المستخدم - ' . $user->name)

@section('content')
<div class="admin-user-show-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-home"></i>
                                    الرئيسية
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.users.index') }}">
                                    <i class="fas fa-users"></i>
                                    المستخدمين
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                تفاصيل المستخدم
                            </li>
                        </ol>
                    </nav>
                    <h1 class="page-title">
                        <i class="fas fa-user me-3"></i>
                        تفاصيل المستخدم
                    </h1>
                </div>
                <div class="col-md-6 text-end">
                    <div class="header-actions">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-right me-1"></i>
                            العودة للقائمة
                        </a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>
                            تعديل المستخدم
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- User Profile Card -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm" data-aos="fade-up">
                    <div class="card-body text-center p-4">
                        <div class="user-avatar-large mb-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366f1&color=fff&size=120"
                                 alt="{{ $user->name }}" class="rounded-circle border-4 border-white shadow">
                        </div>

                        <h4 class="user-name mb-2">{{ $user->name }}</h4>
                        <p class="user-email text-muted mb-3">{{ $user->email }}</p>

                        <div class="user-role-badge mb-3">
                            @if($user->role === 'admin')
                                <span class="badge bg-danger fs-6 px-3 py-2">
                                    <i class="fas fa-user-shield me-1"></i>
                                    مدير
                                </span>
                            @elseif($user->role === 'instructor')
                                <span class="badge bg-warning fs-6 px-3 py-2">
                                    <i class="fas fa-chalkboard-teacher me-1"></i>
                                    مدرب
                                </span>
                            @else
                                <span class="badge bg-success fs-6 px-3 py-2">
                                    <i class="fas fa-user-graduate me-1"></i>
                                    طالب
                                </span>
                            @endif
                        </div>

                        <div class="user-stats row text-center">
                            <div class="col-6">
                                <div class="stat-item">
                                    <h5 class="stat-number">{{ $user->enrollments_count ?? 0 }}</h5>
                                    <p class="stat-label text-muted">الكورسات المسجلة</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <h5 class="stat-number">{{ $user->instructed_courses_count ?? 0 }}</h5>
                                    <p class="stat-label text-muted">الكورسات المدربة</p>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="user-info-list text-start">
                            <div class="info-item mb-2">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <span class="fw-bold">تاريخ التسجيل:</span>
                                <span class="text-muted">{{ $user->created_at->format('Y/m/d') }}</span>
                            </div>
                            <div class="info-item mb-2">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <span class="fw-bold">آخر تحديث:</span>
                                <span class="text-muted">{{ $user->updated_at->format('Y/m/d H:i') }}</span>
                            </div>
                            @if($user->email_verified_at)
                                <div class="info-item mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span class="fw-bold">البريد الإلكتروني:</span>
                                    <span class="text-success">مؤكد</span>
                                </div>
                            @else
                                <div class="info-item mb-2">
                                    <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                    <span class="fw-bold">البريد الإلكتروني:</span>
                                    <span class="text-warning">غير مؤكد</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Details Tabs -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-white">
                        <ul class="nav nav-tabs card-header-tabs" id="userTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="enrollments-tab" data-bs-toggle="tab"
                                        data-bs-target="#enrollments" type="button" role="tab">
                                    <i class="fas fa-graduation-cap me-1"></i>
                                    الكورسات المسجلة
                                </button>
                            </li>
                            @if($user->role === 'instructor')
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="courses-tab" data-bs-toggle="tab"
                                        data-bs-target="#courses" type="button" role="tab">
                                    <i class="fas fa-book me-1"></i>
                                    الكورسات المدربة
                                </button>
                            </li>
                            @endif
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="payments-tab" data-bs-toggle="tab"
                                        data-bs-target="#payments" type="button" role="tab">
                                    <i class="fas fa-credit-card me-1"></i>
                                    المدفوعات
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="userTabsContent">
                            <!-- Enrollments Tab -->
                            <div class="tab-pane fade show active" id="enrollments" role="tabpanel">
                                @if($user->enrollments->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>الكورس</th>
                                                    <th>المدرب</th>
                                                    <th>تاريخ التسجيل</th>
                                                    <th>الحالة</th>
                                                    <th>التقدم</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($user->enrollments as $enrollment)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ $enrollment->course->thumbnail ? asset('storage/' . $enrollment->course->thumbnail) : 'https://via.placeholder.com/40x40' }}"
                                                                 alt="{{ $enrollment->course->title }}"
                                                                 class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                            <div>
                                                                <h6 class="mb-0">{{ $enrollment->course->title }}</h6>
                                                                <small class="text-muted">{{ $enrollment->course->category->name }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $enrollment->course->instructor->name }}</td>
                                                    <td>{{ $enrollment->created_at->format('Y/m/d') }}</td>
                                                    <td>
                                                        <span class="badge bg-success">مسجل</span>
                                                    </td>
                                                    <td>
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar" style="width: {{ $enrollment->progress_percentage ?? 0 }}%"></div>
                                                        </div>
                                                        <small class="text-muted">{{ $enrollment->progress_percentage ?? 0 }}%</small>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">لا توجد كورسات مسجلة</h5>
                                        <p class="text-muted">لم يسجل هذا المستخدم في أي كورس بعد</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Instructor Courses Tab -->
                            @if($user->role === 'instructor')
                            <div class="tab-pane fade" id="courses" role="tabpanel">
                                @if($user->instructedCourses->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>الكورس</th>
                                                    <th>التصنيف</th>
                                                    <th>السعر</th>
                                                    <th>الطلاب</th>
                                                    <th>الحالة</th>
                                                    <th>تاريخ الإنشاء</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($user->instructedCourses as $course)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://via.placeholder.com/40x40' }}"
                                                                 alt="{{ $course->title }}"
                                                                 class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                            <div>
                                                                <h6 class="mb-0">{{ $course->title }}</h6>
                                                                <small class="text-muted">{{ Str::limit($course->short_description, 50) }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $course->category->name }}</td>
                                                    <td>
                                                        @if($course->discount_price)
                                                            <span class="text-decoration-line-through text-muted">{{ $course->price }} ريال</span>
                                                            <br>
                                                            <span class="text-success fw-bold">{{ $course->discount_price }} ريال</span>
                                                        @else
                                                            <span class="fw-bold">{{ $course->price }} ريال</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $course->enrollments_count ?? 0 }} طالب</span>
                                                    </td>
                                                    <td>
                                                        @if($course->is_published)
                                                            <span class="badge bg-success">منشور</span>
                                                        @else
                                                            <span class="badge bg-warning">مسودة</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $course->created_at->format('Y/m/d') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">لا توجد كورسات مدربة</h5>
                                        <p class="text-muted">لم ينشئ هذا المدرب أي كورس بعد</p>
                                    </div>
                                @endif
                            </div>
                            @endif

                            <!-- Payments Tab -->
                            <div class="tab-pane fade" id="payments" role="tabpanel">
                                @if($user->payments->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>الكورس</th>
                                                    <th>المبلغ</th>
                                                    <th>طريقة الدفع</th>
                                                    <th>الحالة</th>
                                                    <th>تاريخ الدفع</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($user->payments as $payment)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ $payment->course->thumbnail ? asset('storage/' . $payment->course->thumbnail) : 'https://via.placeholder.com/40x40' }}"
                                                                 alt="{{ $payment->course->title }}"
                                                                 class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                            <div>
                                                                <h6 class="mb-0">{{ $payment->course->title }}</h6>
                                                                <small class="text-muted">{{ $payment->course->instructor->name }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold">{{ $payment->amount }} ريال</span>
                                                    </td>
                                                    <td>{{ $payment->payment_method }}</td>
                                                    <td>
                                                        @if($payment->status === 'completed')
                                                            <span class="badge bg-success">مكتمل</span>
                                                        @elseif($payment->status === 'pending')
                                                            <span class="badge bg-warning">في الانتظار</span>
                                                        @else
                                                            <span class="badge bg-danger">فشل</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $payment->created_at->format('Y/m/d H:i') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">لا توجد مدفوعات</h5>
                                        <p class="text-muted">لم يقم هذا المستخدم بأي عملية دفع بعد</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 1rem;
}

.breadcrumb-item a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
}

.breadcrumb-item.active {
    color: white;
}

.user-avatar-large img {
    border: 4px solid white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.user-role-badge .badge {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
}

.stat-item {
    padding: 0.5rem;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin: 0;
}

.stat-label {
    font-size: 0.8rem;
    margin: 0;
}

.info-item {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
}

.nav-tabs .nav-link {
    border: none;
    color: var(--dark-color);
    font-weight: 500;
    padding: 0.75rem 1rem;
}

.nav-tabs .nav-link.active {
    color: var(--primary-color);
    background: transparent;
    border-bottom: 2px solid var(--primary-color);
}

.progress {
    background-color: #e9ecef;
    border-radius: 10px;
}

.progress-bar {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    border-radius: 10px;
}

.table th {
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
