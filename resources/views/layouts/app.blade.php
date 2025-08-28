<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'منصة التعلم الإلكتروني')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Global Security Measures -->
    <script>
        // Global Security System
        (function() {
            'use strict';

            // Prevent developer tools access
            function detectDevTools() {
                const threshold = 160;
                const widthThreshold = window.outerWidth - window.innerWidth > threshold;
                const heightThreshold = window.outerHeight - window.innerHeight > threshold;

                if (widthThreshold || heightThreshold) {
                    // Developer tools detected
                    document.body.innerHTML = `
                        <div style="
                            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                            background: #000; color: #fff; display: flex;
                            align-items: center; justify-content: center;
                            font-family: Arial, sans-serif; text-align: center; z-index: 999999;
                        ">
                            <div>
                                <h1 style="color: #ff0000; font-size: 2em;">⚠️ تحذير أمني ⚠️</h1>
                                <p style="font-size: 1.2em; margin: 20px 0;">
                                    تم اكتشاف محاولة فتح أدوات المطور!<br>
                                    يرجى إغلاق أدوات المطور والضغط على F5 لتحديث الصفحة.
                                </p>
                                <button onclick="location.reload()" style="
                                    background: #ff0000; color: #fff; border: none;
                                    padding: 15px 30px; font-size: 1.1em;
                                    border-radius: 8px; cursor: pointer;
                                ">
                                    تحديث الصفحة
                                </button>
                            </div>
                        </div>
                    `;
                }
            }

            // Check every 100ms
            setInterval(detectDevTools, 100);

            // Block common developer tools shortcuts
            document.addEventListener('keydown', function(e) {
                // F12
                if (e.key === 'F12') {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                // Ctrl+Shift+I (Chrome DevTools)
                if (e.ctrlKey && e.shiftKey && e.key === 'I') {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                // Ctrl+Shift+J (Chrome Console)
                if (e.ctrlKey && e.shiftKey && e.key === 'J') {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                // Ctrl+Shift+C (Chrome Element Inspector)
                if (e.ctrlKey && e.shiftKey && e.key === 'C') {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                // Ctrl+U (View Source)
                if (e.ctrlKey && e.key === 'u') {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                // Ctrl+S (Save Page)
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                // Ctrl+P (Print)
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });

            // Disable right-click context menu
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                return false;
            });

            // Disable text selection
            document.addEventListener('selectstart', function(e) {
                e.preventDefault();
                return false;
            });

            // Disable copy/paste
            document.addEventListener('copy', function(e) {
                e.preventDefault();
                return false;
            });

            document.addEventListener('cut', function(e) {
                e.preventDefault();
                return false;
            });

            document.addEventListener('paste', function(e) {
                e.preventDefault();
                return false;
            });

            // Disable drag and drop
            document.addEventListener('dragstart', function(e) {
                e.preventDefault();
                return false;
            });

            document.addEventListener('drop', function(e) {
                e.preventDefault();
                return false;
            });

            // Prevent iframe embedding
            if (window.top !== window.self) {
                window.top.location.href = window.location.href;
            }

            // Console warning
            console.log('%c⚠️ تحذير أمني ⚠️', 'color: #ff0000; font-size: 20px; font-weight: bold;');
            console.log('%cهذا الموقع محمي من محاولات التلاعب. يرجى عدم استخدام أدوات المطور.', 'color: #ff0000; font-size: 14px;');

        })();
    </script>

    <style>
        /* Global Security CSS */
        * {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            -webkit-touch-callout: none !important;
            -webkit-user-drag: none !important;
        }

        /* Allow text selection for specific elements */
        .learning-interface,
        .content-section,
        .lesson-content,
        .notes-content,
        .security-alert,
        .danger-content {
            -webkit-user-select: text !important;
            -moz-user-select: text !important;
            -ms-user-select: text !important;
            user-select: text !important;
        }

        /* Disable text selection for video and sensitive areas */
        video, .video-container, .enhanced-video-player {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
        }

        :root {
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --secondary-color: #f1f5f9;
            --accent-color: #06b6d4;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --border-radius: 12px;
            --box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --box-shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            font-family: 'Cairo', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        .main-content {
            background: var(--light-color);
            min-height: calc(100vh - 80px);
            margin-top: 80px;
        }

        /* Modern Navbar */
        .navbar {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--box-shadow);
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-link {
            font-weight: 500;
            color: var(--dark-color) !important;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
            transform: translateY(-2px);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 50%;
            background: var(--primary-color);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Modern Cards */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow-lg);
        }

        .course-card {
            position: relative;
            overflow: hidden;
        }

        .course-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(6, 182, 212, 0.1));
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .course-card:hover::before {
            opacity: 1;
        }

        .course-card .card-body {
            position: relative;
            z-index: 2;
        }

        /* Modern Buttons */
        .btn {
            border-radius: var(--border-radius);
            font-weight: 500;
            padding: 12px 24px;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
        }

        /* Modern Form Controls */
        .form-control, .form-select {
            border-radius: var(--border-radius);
            border: 2px solid #e2e8f0;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        /* Modern Badges */
        .badge {
            border-radius: 20px;
            padding: 6px 12px;
            font-weight: 500;
        }

        /* Progress Bars */
        .progress {
            height: 8px;
            border-radius: 10px;
            background: #e2e8f0;
        }

        .progress-bar {
            border-radius: 10px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide-up {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Glass Effect */
        .glass {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Modern Footer */
        .footer {
            background: linear-gradient(135deg, #1e293b, #334155);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-top: 70px;
            }

            .navbar-brand {
                font-size: 1.2rem;
            }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .navbar {
                background: rgba(30, 41, 59, 0.95) !important;
            }

            .nav-link {
                color: white !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Modern Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-graduation-cap me-2"></i>
                EduPlatform
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('student.courses.index') }}">
                            <i class="fas fa-book me-1"></i>
                            الكورسات
                        </a>
                    </li>
                    @auth
                        @if(auth()->user()->isStudent())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('student.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-1"></i>
                                    لوحة التحكم
                                </a>
                            </li>
                        @elseif(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-cog me-1"></i>
                                    إدارة النظام
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-primary" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                تسجيل الدخول
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i>
                                إنشاء حساب
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="avatar me-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=6366f1&color=fff"
                                         class="rounded-circle" width="32" height="32" alt="Avatar">
                                </div>
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>الملف الشخصي</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>الإعدادات</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        تسجيل الخروج
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Alert Messages -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050; margin-top: 80px;">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Modern Footer -->
    <footer class="footer py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-graduation-cap me-2"></i>
                        EduPlatform
                    </h5>
                    <p class="text-light opacity-75">
                        منصة التعلم الإلكتروني الرائدة في المنطقة. نوفر أفضل الكورسات التعليمية مع خبراء متخصصين.
                    </p>
                    <div class="social-links">
                        <a href="#" class="btn btn-outline-light btn-sm me-2">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm me-2">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm me-2">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm">
                            <i class="fab fa-linkedin"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold mb-3">روابط سريعة</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">الكورسات</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">المدربين</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">الشهادات</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">المدونة</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold mb-3">الدعم</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">مركز المساعدة</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">تواصل معنا</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">الأسئلة الشائعة</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">سياسة الخصوصية</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="fw-bold mb-3">اشترك في النشرة الإخبارية</h6>
                    <p class="text-light opacity-75 mb-3">احصل على آخر الأخبار والعروض الخاصة</p>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="البريد الإلكتروني">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
            <hr class="my-4 opacity-25">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-light opacity-75">
                        &copy; {{ date('Y') }} EduPlatform. جميع الحقوق محفوظة.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-light opacity-75">
                        صنع بـ <i class="fas fa-heart text-danger"></i> بواسطة م.محمد ماهر
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Error Handler Script -->
    <script src="{{ asset('js/error-handler.js') }}"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
