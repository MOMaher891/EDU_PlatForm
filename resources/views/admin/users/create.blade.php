@extends('layouts.app')

@section('title', 'إضافة مستخدم جديد')

@section('content')
<div class="admin-user-create-page">
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
                                إضافة مستخدم جديد
                            </li>
                        </ol>
                    </nav>
                    <h1 class="page-title">
                        <i class="fas fa-user-plus me-3"></i>
                        إضافة مستخدم جديد
                    </h1>
                </div>
                <div class="col-md-6 text-end">
                    <div class="header-actions">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-right me-1"></i>
                            العودة للقائمة
                        </a>
                        <button type="submit" form="createUserForm" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            إنشاء المستخدم
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Create Form -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" data-aos="fade-up">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-plus me-2"></i>
                            معلومات المستخدم الجديد
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="createUserForm" method="POST" action="{{ route('admin.users.store') }}">
                            @csrf

                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user me-1"></i>
                                        الاسم الكامل
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>
                                        البريد الإلكتروني
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label">
                                        <i class="fas fa-user-tag me-1"></i>
                                        الدور
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                        <option value="">اختر الدور</option>
                                        <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>
                                            طالب
                                        </option>
                                        <option value="instructor" {{ old('role') === 'instructor' ? 'selected' : '' }}>
                                            مدرب
                                        </option>
                                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
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
                                               name="email_verified_at" value="1" {{ old('email_verified_at') ? 'checked' : '' }}>
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
                                    كلمة المرور
                                </h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-key me-1"></i>
                                            كلمة المرور
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                               id="password" name="password" minlength="8" required>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            يجب أن تكون كلمة المرور 8 أحرف على الأقل
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">
                                            <i class="fas fa-key me-1"></i>
                                            تأكيد كلمة المرور
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" class="form-control"
                                               id="password_confirmation" name="password_confirmation" minlength="8" required>
                                    </div>
                                </div>

                                <!-- Password Strength Indicator -->
                                <div class="password-strength mt-3">
                                    <label class="form-label">قوة كلمة المرور:</label>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar" id="passwordStrength" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted" id="passwordStrengthText">ابدأ بكتابة كلمة المرور</small>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Form Actions -->
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i>
                                            إلغاء
                                        </a>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            إنشاء المستخدم
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Help Sidebar -->
            <div class="col-lg-4">
                <!-- Form Guidelines -->
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            إرشادات إنشاء المستخدم
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="guidelines-list">
                            <div class="guideline-item mb-3">
                                <div class="guideline-icon bg-primary">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="guideline-content">
                                    <h6 class="guideline-title">الاسم الكامل</h6>
                                    <p class="guideline-text">أدخل الاسم الكامل للمستخدم كما سيظهر في النظام</p>
                                </div>
                            </div>
                            <div class="guideline-item mb-3">
                                <div class="guideline-icon bg-success">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="guideline-content">
                                    <h6 class="guideline-title">البريد الإلكتروني</h6>
                                    <p class="guideline-text">يجب أن يكون بريد إلكتروني صحيح وفريد</p>
                                </div>
                            </div>
                            <div class="guideline-item mb-3">
                                <div class="guideline-icon bg-warning">
                                    <i class="fas fa-user-tag"></i>
                                </div>
                                <div class="guideline-content">
                                    <h6 class="guideline-title">الدور</h6>
                                    <p class="guideline-text">اختر الدور المناسب للمستخدم في النظام</p>
                                </div>
                            </div>
                            <div class="guideline-item">
                                <div class="guideline-icon bg-info">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <div class="guideline-content">
                                    <h6 class="guideline-title">كلمة المرور</h6>
                                    <p class="guideline-text">يجب أن تكون قوية وآمنة (8 أحرف على الأقل)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role Information -->
                <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            معلومات الأدوار
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="role-info">
                            <div class="role-item mb-3">
                                <div class="role-badge bg-success">
                                    <i class="fas fa-user-graduate"></i>
                                    طالب
                                </div>
                                <p class="role-description">يمكنه التسجيل في الكورسات ومتابعة التعلم</p>
                            </div>
                            <div class="role-item mb-3">
                                <div class="role-badge bg-warning">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    مدرب
                                </div>
                                <p class="role-description">يمكنه إنشاء وإدارة الكورسات الخاصة به</p>
                            </div>
                            <div class="role-item">
                                <div class="role-badge bg-danger">
                                    <i class="fas fa-user-shield"></i>
                                    مدير
                                </div>
                                <p class="role-description">صلاحيات كاملة لإدارة النظام بالكامل</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-user-create-page {
    padding: 2rem 0;
}

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

.form-text {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.section-title {
    color: var(--dark-color);
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
}

.password-strength .progress {
    background-color: #e9ecef;
    border-radius: 10px;
}

.password-strength .progress-bar {
    border-radius: 10px;
    transition: all 0.3s ease;
}

.guideline-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.guideline-item:last-child {
    margin-bottom: 0;
}

.guideline-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-left: 1rem;
    flex-shrink: 0;
}

.guideline-content {
    flex: 1;
}

.guideline-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--dark-color);
    margin: 0 0 0.25rem 0;
}

.guideline-text {
    font-size: 0.8rem;
    color: #6c757d;
    margin: 0;
}

.role-item {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.role-item:last-child {
    margin-bottom: 0;
}

.role-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.role-badge i {
    margin-left: 0.5rem;
}

.role-description {
    font-size: 0.8rem;
    color: #6c757d;
    margin: 0;
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
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    const passwordStrength = document.getElementById('passwordStrength');
    const passwordStrengthText = document.getElementById('passwordStrengthText');

    // Password strength checker
    function checkPasswordStrength(password) {
        let strength = 0;
        let feedback = [];

        if (password.length >= 8) {
            strength += 25;
            feedback.push('8 أحرف على الأقل');
        }
        if (/[a-z]/.test(password)) {
            strength += 25;
            feedback.push('حروف صغيرة');
        }
        if (/[A-Z]/.test(password)) {
            strength += 25;
            feedback.push('حروف كبيرة');
        }
        if (/[0-9]/.test(password)) {
            strength += 25;
            feedback.push('أرقام');
        }

        return { strength, feedback };
    }

    function updatePasswordStrength() {
        const result = checkPasswordStrength(password.value);

        passwordStrength.style.width = result.strength + '%';

        if (result.strength <= 25) {
            passwordStrength.className = 'progress-bar bg-danger';
            passwordStrengthText.textContent = 'ضعيفة جداً';
        } else if (result.strength <= 50) {
            passwordStrength.className = 'progress-bar bg-warning';
            passwordStrengthText.textContent = 'ضعيفة';
        } else if (result.strength <= 75) {
            passwordStrength.className = 'progress-bar bg-info';
            passwordStrengthText.textContent = 'جيدة';
        } else {
            passwordStrength.className = 'progress-bar bg-success';
            passwordStrengthText.textContent = 'قوية';
        }
    }

    password.addEventListener('input', updatePasswordStrength);

    // Password confirmation validation
    function validatePassword() {
        if (password.value !== passwordConfirmation.value) {
            passwordConfirmation.setCustomValidity('كلمات المرور غير متطابقة');
        } else {
            passwordConfirmation.setCustomValidity('');
        }
    }

    password.addEventListener('change', validatePassword);
    passwordConfirmation.addEventListener('keyup', validatePassword);

    // Form submission validation
    const form = document.getElementById('createUserForm');
    form.addEventListener('submit', function(e) {
        if (password.value !== passwordConfirmation.value) {
            e.preventDefault();
            alert('كلمات المرور غير متطابقة');
            passwordConfirmation.focus();
        }
    });
});
</script>
@endsection
