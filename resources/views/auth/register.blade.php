@extends('layouts.app')

@section('title', 'إنشاء حساب جديد')

@section('content')
<div class="auth-page min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="auth-card card border-0 shadow-lg overflow-hidden">
                    <div class="row g-0">
                        <!-- Left Side - Form -->
                        <div class="col-lg-6">
                            <div class="auth-form p-5">
                                <div class="text-center mb-4" data-aos="fade-up">
                                    <div class="auth-logo mb-3">
                                        <i class="fas fa-graduation-cap fa-3x text-primary"></i>
                                    </div>
                                    <h2 class="fw-bold mb-2">إنشاء حساب جديد</h2>
                                    <p class="text-muted">انضم إلى منصة التعلم الرائدة</p>
                                </div>

                                <form method="POST" action="{{ route('register') }}" data-aos="fade-up" data-aos-delay="100">
                                    @csrf
                                    
                                    <!-- Name Field -->
                                    <div class="form-floating mb-3">
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name') }}" 
                                               placeholder="الاسم الكامل"
                                               required>
                                        <label for="name">
                                            <i class="fas fa-user me-2"></i>
                                            الاسم الكامل
                                        </label>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Email Field -->
                                    <div class="form-floating mb-3">
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email') }}" 
                                               placeholder="البريد الإلكتروني"
                                               required>
                                        <label for="email">
                                            <i class="fas fa-envelope me-2"></i>
                                            البريد الإلكتروني
                                        </label>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Role Selection -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-user-tag me-2"></i>
                                            نوع الحساب
                                        </label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <input type="radio" class="btn-check" name="role" id="student" value="student" checked>
                                                <label class="btn btn-outline-primary w-100 py-3" for="student">
                                                    <i class="fas fa-user-graduate d-block mb-2 fa-2x"></i>
                                                    <span class="fw-semibold">طالب</span>
                                                    <small class="d-block text-muted">للتعلم والدراسة</small>
                                                </label>
                                            </div>
                                            <div class="col-6">
                                                <input type="radio" class="btn-check" name="role" id="instructor" value="instructor">
                                                <label class="btn btn-outline-success w-100 py-3" for="instructor">
                                                    <i class="fas fa-chalkboard-teacher d-block mb-2 fa-2x"></i>
                                                    <span class="fw-semibold">مدرب</span>
                                                    <small class="d-block text-muted">لتقديم الكورسات</small>
                                                </label>
                                            </div>
                                        </div>
                                        @error('role')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Password Field -->
                                    <div class="form-floating mb-3">
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               placeholder="كلمة المرور"
                                               required>
                                        <label for="password">
                                            <i class="fas fa-lock me-2"></i>
                                            كلمة المرور
                                        </label>
                                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Confirm Password Field -->
                                    <div class="form-floating mb-3">
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirmation" 
                                               name="password_confirmation" 
                                               placeholder="تأكيد كلمة المرور"
                                               required>
                                        <label for="password_confirmation">
                                            <i class="fas fa-lock me-2"></i>
                                            تأكيد كلمة المرور
                                        </label>
                                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>

                                    <!-- Terms and Conditions -->
                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                        <label class="form-check-label" for="terms">
                                            أوافق على 
                                            <a href="#" class="text-primary text-decoration-none">الشروط والأحكام</a>
                                            و
                                            <a href="#" class="text-primary text-decoration-none">سياسة الخصوصية</a>
                                        </label>
                                        @error('terms')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="d-grid mb-4">
                                        <button type="submit" class="btn btn-primary btn-lg py-3">
                                            <i class="fas fa-user-plus me-2"></i>
                                            إنشاء الحساب
                                        </button>
                                    </div>

                                    <!-- Social Login -->
                                    <div class="text-center mb-4">
                                        <p class="text-muted mb-3">أو سجل باستخدام</p>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button" class="btn btn-outline-danger">
                                                <i class="fab fa-google"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-primary">
                                                <i class="fab fa-facebook"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info">
                                                <i class="fab fa-twitter"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Login Link -->
                                    <div class="text-center">
                                        <p class="text-muted">
                                            لديك حساب بالفعل؟ 
                                            <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-semibold">
                                                سجل دخولك هنا
                                            </a>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Right Side - Image/Info -->
                        <div class="col-lg-6 d-none d-lg-block">
                            <div class="auth-image h-100 position-relative">
                                <div class="auth-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                    <div class="text-center text-white p-5" data-aos="fade-left">
                                        <h3 class="fw-bold mb-4">ابدأ رحلة التعلم معنا</h3>
                                        <div class="features-list">
                                            <div class="feature-item d-flex align-items-center mb-3">
                                                <i class="fas fa-check-circle fa-2x me-3"></i>
                                                <div class="text-start">
                                                    <h6 class="fw-bold mb-1">كورسات متنوعة</h6>
                                                    <small class="opacity-75">أكثر من 1000 كورس في مختلف المجالات</small>
                                                </div>
                                            </div>
                                            <div class="feature-item d-flex align-items-center mb-3">
                                                <i class="fas fa-certificate fa-2x me-3"></i>
                                                <div class="text-start">
                                                    <h6 class="fw-bold mb-1">شهادات معتمدة</h6>
                                                    <small class="opacity-75">احصل على شهادات معترف بها دولياً</small>
                                                </div>
                                            </div>
                                            <div class="feature-item d-flex align-items-center mb-3">
                                                <i class="fas fa-users fa-2x me-3"></i>
                                                <div class="text-start">
                                                    <h6 class="fw-bold mb-1">مجتمع تعليمي</h6>
                                                    <small class="opacity-75">انضم لآلاف الطلاب والمدربين</small>
                                                </div>
                                            </div>
                                            <div class="feature-item d-flex align-items-center">
                                                <i class="fas fa-headset fa-2x me-3"></i>
                                                <div class="text-start">
                                                    <h6 class="fw-bold mb-1">دعم مستمر</h6>
                                                    <small class="opacity-75">فريق دعم متاح 24/7</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .auth-page {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
    }

    .auth-page::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .auth-card {
        border-radius: 20px;
        backdrop-filter: blur(20px);
        background: rgba(255, 255, 255, 0.95);
        max-width: 1000px;
        margin: 2rem auto;
    }

    .auth-form {
        background: white;
        border-radius: 20px 0 0 20px;
    }

    .auth-image {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.9), rgba(139, 69, 19, 0.9)), 
                    url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80');
        background-size: cover;
        background-position: center;
        border-radius: 0 20px 20px 0;
    }

    .form-floating {
        position: relative;
    }

    .form-floating .form-control {
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        padding: 1rem 1rem 1rem 3rem;
        height: auto;
        transition: all 0.3s ease;
    }

    .form-floating .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .form-floating label {
        padding: 1rem 1rem 1rem 3rem;
        color: #6b7280;
        font-weight: 500;
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        z-index: 10;
        transition: color 0.3s ease;
    }

    .password-toggle:hover {
        color: var(--primary-color);
    }

    .btn-check:checked + .btn-outline-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        border-color: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
    }

    .btn-check:checked + .btn-outline-success {
        background: linear-gradient(135deg, var(--success-color), #059669);
        border-color: var(--success-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
    }

    .feature-item {
        animation: slideInRight 0.6s ease-out;
        animation-fill-mode: both;
    }

    .feature-item:nth-child(1) { animation-delay: 0.1s; }
    .feature-item:nth-child(2) { animation-delay: 0.2s; }
    .feature-item:nth-child(3) { animation-delay: 0.3s; }
    .feature-item:nth-child(4) { animation-delay: 0.4s; }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @media (max-width: 992px) {
        .auth-card {
            margin: 1rem;
        }
        
        .auth-form {
            border-radius: 20px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const toggle = field.nextElementSibling.nextElementSibling;
        const icon = toggle.querySelector('i');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('كلمات المرور غير متطابقة');
            return false;
        }
        
        if (password.length < 8) {
            e.preventDefault();
            alert('كلمة المرور يجب أن تكون 8 أحرف على الأقل');
            return false;
        }
    });
</script>
@endpush
@endsection
