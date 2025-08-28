@extends('layouts.app')

@section('title', 'تسجيل الدخول')

@section('content')
<div class="auth-page min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="auth-card card border-0 shadow-lg overflow-hidden">
                    <div class="row g-0">
                        <!-- Left Side - Image/Info -->
                        <div class="col-lg-6 d-none d-lg-block">
                            <div class="auth-image h-100 position-relative">
                                <div class="auth-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                    <div class="text-center text-white p-5" data-aos="fade-right">
                                        <h3 class="fw-bold mb-4">مرحباً بعودتك!</h3>
                                        <p class="lead mb-4">استمر في رحلة التعلم من حيث توقفت</p>
                                        
                                        <div class="stats-grid row g-3">
                                            <div class="col-6">
                                                <div class="stat-card text-center p-3 rounded-3" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
                                                    <h4 class="fw-bold mb-1">1000+</h4>
                                                    <small>كورس متاح</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="stat-card text-center p-3 rounded-3" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
                                                    <h4 class="fw-bold mb-1">50K+</h4>
                                                    <small>طالب نشط</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="stat-card text-center p-3 rounded-3" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
                                                    <h4 class="fw-bold mb-1">500+</h4>
                                                    <small>مدرب خبير</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="stat-card text-center p-3 rounded-3" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
                                                    <h4 class="fw-bold mb-1">4.9</h4>
                                                    <small>تقييم المنصة</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side - Form -->
                        <div class="col-lg-6">
                            <div class="auth-form p-5">
                                <div class="text-center mb-4" data-aos="fade-up">
                                    <div class="auth-logo mb-3">
                                        <i class="fas fa-graduation-cap fa-3x text-primary"></i>
                                    </div>
                                    <h2 class="fw-bold mb-2">تسجيل الدخول</h2>
                                    <p class="text-muted">ادخل إلى حسابك وتابع التعلم</p>
                                </div>

                                <form method="POST" action="{{ route('login') }}" data-aos="fade-up" data-aos-delay="100">
                                    @csrf
                                    
                                    <!-- Email Field -->
                                    <div class="form-floating mb-3">
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email') }}" 
                                               placeholder="البريد الإلكتروني"
                                               required 
                                               autofocus>
                                        <label for="email">
                                            <i class="fas fa-envelope me-2"></i>
                                            البريد الإلكتروني
                                        </label>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
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

                                    <!-- Remember Me & Forgot Password -->
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">
                                                تذكرني
                                            </label>
                                        </div>
                                        <a href="{{ route('password.request') }}" class="text-primary text-decoration-none">
                                            نسيت كلمة المرور؟
                                        </a>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="d-grid mb-4">
                                        <button type="submit" class="btn btn-primary btn-lg py-3">
                                            <i class="fas fa-sign-in-alt me-2"></i>
                                            تسجيل الدخول
                                        </button>
                                    </div>

                                    <!-- Social Login -->
                                    <div class="text-center mb-4">
                                        <p class="text-muted mb-3">أو سجل دخولك باستخدام</p>
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

                                    <!-- Register Link -->
                                    <div class="text-center">
                                        <p class="text-muted">
                                            ليس لديك حساب؟ 
                                            <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-semibold">
                                                أنشئ حساباً جديداً
                                            </a>
                                        </p>
                                    </div>
                                </form>

                                <!-- Demo Accounts -->
                                <div class="demo-accounts mt-4 p-3 bg-light rounded-3" data-aos="fade-up" data-aos-delay="200">
                                    <h6 class="fw-bold mb-2 text-center">حسابات تجريبية</h6>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="fillDemoAccount('admin')">
                                                <i class="fas fa-user-shield me-1"></i>
                                                أدمن
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button type="button" class="btn btn-outline-success btn-sm w-100" onclick="fillDemoAccount('student')">
                                                <i class="fas fa-user-graduate me-1"></i>
                                                طالب
                                            </button>
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
        border-radius: 0 20px 20px 0;
    }

    .auth-image {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.9), rgba(139, 69, 19, 0.9)), 
                    url('https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80');
        background-size: cover;
        background-position: center;
        border-radius: 20px 0 0 20px;
    }

    .stat-card {
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease-out;
        animation-fill-mode: both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }

    .stat-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.2) !important;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .demo-accounts {
        border: 2px dashed #e2e8f0;
        transition: all 0.3s ease;
    }

    .demo-accounts:hover {
        border-color: var(--primary-color);
        background: rgba(99, 102, 241, 0.05) !important;
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

    function fillDemoAccount(type) {
        const emailField = document.getElementById('email');
        const passwordField = document.getElementById('password');
        
        if (type === 'admin') {
            emailField.value = 'admin@example.com';
            passwordField.value = 'password';
        } else if (type === 'student') {
            emailField.value = 'student@example.com';
            passwordField.value = 'password';
        }
        
        // Add visual feedback
        emailField.classList.add('is-valid');
        passwordField.classList.add('is-valid');
        
        setTimeout(() => {
            emailField.classList.remove('is-valid');
            passwordField.classList.remove('is-valid');
        }, 2000);
    }
</script>
@endpush
@endsection
