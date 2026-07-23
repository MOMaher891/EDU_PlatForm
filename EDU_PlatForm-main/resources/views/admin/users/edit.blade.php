@extends('layouts.app')

@section('title', 'تعديل المستخدم - ' . $user->name)

@section('content')
<div class="admin-user-edit-page">
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
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.users.show', $user) }}">
                                    <i class="fas fa-user"></i>
                                    {{ $user->name }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                تعديل المستخدم
                            </li>
                        </ol>
                    </nav>
                    <h1 class="page-title">
                        <i class="fas fa-edit me-3"></i>
                        تعديل المستخدم
                    </h1>
                </div>
                <div class="col-md-6 text-end">
                    <div class="header-actions">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-right me-1"></i>
                            العودة للتفاصيل
                        </a>
                        <button type="submit" form="editUserForm" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            حفظ التغييرات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Edit Form -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" data-aos="fade-up">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-edit me-2"></i>
                            معلومات المستخدم
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="editUserForm" method="POST" action="{{ route('admin.users.update', $user) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user me-1"></i>
                                        الاسم الكامل
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>
                                        البريد الإلكتروني
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label">
                                        <i class="fas fa-user-tag me-1"></i>
                                        الدور
                                    </label>
                                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                        <option value="">اختر الدور</option>
                                        <option value="student" {{ old('role', $user->role) === 'student' ? 'selected' : '' }}>
                                            طالب
                                        </option>
                                        <option value="instructor" {{ old('role', $user->role) === 'instructor' ? 'selected' : '' }}>
                                            مدرب
                                        </option>
                                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                                            مدير
                                        </option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email_verified_at" class="form-label">
                                        <i class="fas fa-check-circle me-1"></i>
                                        حالة تأكيد البريد الإلكتروني
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="email_verified_at"
                                               name="email_verified_at" value="1"
                                               {{ $user->email_verified_at ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_verified_at">
                                            مؤكد
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Password Section -->
                            <div class="password-section">
                                <h6 class="section-title mb-3">
                                    <i class="fas fa-lock me-2"></i>
                                    تغيير كلمة المرور
                                </h6>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    اترك حقول كلمة المرور فارغة إذا كنت لا تريد تغييرها
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-key me-1"></i>
                                            كلمة المرور الجديدة
                                        </label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                               id="password" name="password" minlength="8">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">
                                            <i class="fas fa-key me-1"></i>
                                            تأكيد كلمة المرور
                                        </label>
                                        <input type="password" class="form-control"
                                               id="password_confirmation" name="password_confirmation" minlength="8">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Form Actions -->
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i>
                                            إلغاء
                                        </a>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            حفظ التغييرات
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- User Info Sidebar -->
            <div class="col-lg-4">
                <!-- Current User Info -->
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            معلومات المستخدم الحالية
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="user-current-info">
                            <div class="info-item mb-3">
                                <div class="info-label">الاسم:</div>
                                <div class="info-value">{{ $user->name }}</div>
                            </div>
                            <div class="info-item mb-3">
                                <div class="info-label">البريد الإلكتروني:</div>
                                <div class="info-value">{{ $user->email }}</div>
                            </div>
                            <div class="info-item mb-3">
                                <div class="info-label">الدور:</div>
                                <div class="info-value">
                                    @if($user->role === 'admin')
                                        <span class="badge bg-danger">مدير</span>
                                    @elseif($user->role === 'instructor')
                                        <span class="badge bg-warning">مدرب</span>
                                    @else
                                        <span class="badge bg-success">طالب</span>
                                    @endif
                                </div>
                            </div>
                            <div class="info-item mb-3">
                                <div class="info-label">تاريخ التسجيل:</div>
                                <div class="info-value">{{ $user->created_at->format('Y/m/d H:i') }}</div>
                            </div>
                            <div class="info-item mb-3">
                                <div class="info-label">آخر تحديث:</div>
                                <div class="info-value">{{ $user->updated_at->format('Y/m/d H:i') }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">حالة البريد الإلكتروني:</div>
                                <div class="info-value">
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success">مؤكد</span>
                                    @else
                                        <span class="badge bg-warning">غير مؤكد</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Statistics -->
                <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            إحصائيات المستخدم
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="user-stats">
                            <div class="stat-item mb-3">
                                <div class="stat-icon bg-primary">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number">{{ $user->enrollments_count ?? 0 }}</div>
                                    <div class="stat-label">الكورسات المسجلة</div>
                                </div>
                            </div>
                            <div class="stat-item mb-3">
                                <div class="stat-icon bg-warning">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number">{{ $user->instructed_courses_count ?? 0 }}</div>
                                    <div class="stat-label">الكورسات المدربة</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon bg-success">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number">{{ $user->payments_count ?? 0 }}</div>
                                    <div class="stat-label">المدفوعات</div>
                                </div>
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

.form-label {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.section-title {
    color: var(--dark-color);
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
}

.alert-info {
    background-color: rgba(99, 102, 241, 0.1);
    border-color: rgba(99, 102, 241, 0.2);
    color: var(--primary-color);
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: var(--dark-color);
}

.info-value {
    color: #6c757d;
}

.stat-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.stat-item:last-child {
    margin-bottom: 0;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-left: 1rem;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark-color);
    margin: 0;
}

.stat-label {
    font-size: 0.8rem;
    color: #6c757d;
    margin: 0;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}

.form-actions {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .header-actions {
        margin-top: 1rem;
        text-align: center !important;
    }

    .form-actions .col-md-6 {
        margin-bottom: 1rem;
    }

    .form-actions .col-md-6:last-child {
        margin-bottom: 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password confirmation validation
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');

    function validatePassword() {
        if (password.value !== passwordConfirmation.value) {
            passwordConfirmation.setCustomValidity('كلمات المرور غير متطابقة');
        } else {
            passwordConfirmation.setCustomValidity('');
        }
    }

    password.addEventListener('change', validatePassword);
    passwordConfirmation.addEventListener('keyup', validatePassword);

    // Form submission confirmation
    const form = document.getElementById('editUserForm');
    form.addEventListener('submit', function(e) {
        if (password.value && !passwordConfirmation.value) {
            e.preventDefault();
            alert('يرجى تأكيد كلمة المرور الجديدة');
            passwordConfirmation.focus();
        }
    });
});
</script>
@endsection
