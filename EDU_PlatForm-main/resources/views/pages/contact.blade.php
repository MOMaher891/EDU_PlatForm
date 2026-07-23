@extends('layouts.app')

@section('title', 'تواصل معنا - الدعم الفني والاستفسارات')

@section('content')
<!-- Hero Section -->
<section class="contact-hero position-relative overflow-hidden py-5 text-center">
    <div class="container py-5 position-relative" style="z-index: 2;" data-aos="fade-up">
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
            <i class="fas fa-headset me-1"></i>
            خدمة العملاء والدعم الفني
        </span>
        <h1 class="display-4 fw-bold mb-3 text-dark">نحن هنا لمساعدتك دائماً</h1>
        <p class="lead text-muted mx-auto" style="max-width: 600px;">
            هل لديك أي استفسار حول الكورسات أو تواجه مشكلة تقنية؟ لا تتردد في الاتصال بنا. فريق الدعم الفني متواجد لمساعدتك على مدار الساعة.
        </p>
    </div>
    
    <!-- Background Shapes -->
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden" style="z-index: 1;">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
    </div>
</section>

<!-- Contact Section Content -->
<section class="py-5 bg-light">
    <div class="container">
        <!-- Contact Info Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card contact-info-card border-0 shadow-sm h-100 text-center p-3">
                    <div class="card-body">
                        <div class="info-icon bg-success bg-opacity-10 text-success d-inline-flex p-3 rounded-circle mb-3">
                            <i class="fab fa-whatsapp fa-2x"></i>
                        </div>
                        <h5 class="fw-bold text-dark">واتساب الدعم الفني</h5>
                        <p class="text-muted small">للاستفسارات السريعة وحل المشكلات الفورية</p>
                        <div class="d-grid gap-2">
                            @php
                                $supportPhone = $appSettings->support_phone ?? '+966 50 123 4567';
                                $whatsappNumber = preg_replace('/[^0-9]/', '', $supportPhone);
                                if (empty($whatsappNumber)) {
                                    $whatsappNumber = '966501234567';
                                }
                            @endphp
                            <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" class="btn btn-outline-success btn-sm">
                                <i class="fab fa-whatsapp me-1"></i>
                                {{ $supportPhone }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card contact-info-card border-0 shadow-sm h-100 text-center p-3">
                    <div class="card-body">
                        <div class="info-icon bg-primary bg-opacity-10 text-primary d-inline-flex p-3 rounded-circle mb-3">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                        <h5 class="fw-bold text-dark">البريد الإلكتروني</h5>
                        <p class="text-muted small">للاستفسارات العامة والمقترحات والطلبات الخاصة</p>
                        <a href="mailto:{{ $appSettings->support_email ?? 'support@example.com' }}" class="btn btn-outline-primary btn-sm mt-3 w-100">
                            <i class="fas fa-envelope me-1"></i>
                            {{ $appSettings->support_email ?? 'support@example.com' }}
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card contact-info-card border-0 shadow-sm h-100 text-center p-3">
                    <div class="card-body">
                        <div class="info-icon bg-warning bg-opacity-10 text-warning d-inline-flex p-3 rounded-circle mb-3">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                        <h5 class="fw-bold text-dark">الموقع الجغرافي</h5>
                        <p class="text-muted small">مقرنا الرئيسي وخدماتنا المتاحة</p>
                        <button class="btn btn-outline-warning btn-sm mt-3 w-100" onclick="document.getElementById('map-section').scrollIntoView({behavior: 'smooth'})">
                            <i class="fas fa-directions me-1"></i>
                            القاهرة، مصر
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Contact Form -->
            <div class="col-lg-7" data-aos="fade-right">
                <div class="card border-0 shadow-sm p-4">
                    <div class="card-body">
                        <h3 class="fw-bold text-dark mb-4">أرسل لنا رسالة</h3>
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-alert="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('contact.submit') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-bold text-dark">الاسم الكامل <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control border-start-0 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="أدخل اسمك بالكامل" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-bold text-dark">البريد الإلكتروني <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control border-start-0 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}" placeholder="example@domain.com" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label fw-bold text-dark">الموضوع <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-tag"></i></span>
                                    <input type="text" class="form-control border-start-0 @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" placeholder="ما هو موضوع استفسارك؟" required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="message" class="form-label fw-bold text-dark">الرسالة <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" placeholder="اكتب تفاصيل استفسارك أو مشكلتك هنا..." required>{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 submit-btn">
                                <i class="fas fa-paper-plane me-2"></i>
                                إرسال الرسالة الآن
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Social Media & Working Hours -->
            <div class="col-lg-5" data-aos="fade-left" data-aos-delay="200">
                <div class="card border-0 shadow-sm p-4 h-100 justify-content-between">
                    <div class="card-body p-0">
                        <h3 class="fw-bold text-dark mb-4">قنواتنا الاجتماعية</h3>
                        <p class="text-muted leading-relaxed mb-4">
                            تابعنا على منصات التواصل الاجتماعي للحصول على أحدث العروض والكورسات والنصائح البرمجية.
                        </p>
                        
                        <div class="social-channels d-flex flex-column gap-3 mb-5">
                            <a href="https://www.facebook.com/share/1bEryWohy3/" target="_blank" class="social-link-item d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none">
                                <div class="social-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="fab fa-facebook-f fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">صفحتنا على فيسبوك</h6>
                                    <small class="text-muted">انضم لمجتمعنا التفاعلي</small>
                                </div>
                            </a>

                            <a href="https://www.linkedin.com/in/mohamed-maher-5a17341b9?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" target="_blank" class="social-link-item d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none">
                                <div class="social-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #0077b5 !important;">
                                    <i class="fab fa-linkedin-in fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">حسابنا على لينكد إن</h6>
                                    <small class="text-muted">الشبكة المهنية والوظائف</small>
                                </div>
                            </a>

                            <a href="https://www.instagram.com/momaher158?igsh=dG83Z3ltMDZjaHVi" target="_blank" class="social-link-item d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none">
                                <div class="social-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%) !important;">
                                    <i class="fab fa-instagram fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">حسابنا على إنستجرام</h6>
                                    <small class="text-muted">يوميات المنصة ونصائح مصورة</small>
                                </div>
                            </a>

                            <a href="https://x.com/Mohamed99873441" target="_blank" class="social-link-item d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none">
                                <div class="social-icon bg-dark text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="fab fa-x-twitter fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">حسابنا على إكس (تويتر)</h6>
                                    <small class="text-muted">آخر التحديثات والأخبار التقنية</small>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="working-hours bg-light p-3 rounded-3 mt-auto">
                        <div class="d-flex align-items-center gap-3">
                            <i class="far fa-clock fa-2x text-primary"></i>
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">أوقات العمل واستقبال الاستفسارات</h6>
                                <p class="text-muted mb-0 small">يومياً من الساعة 9:00 صباحاً وحتى 11:00 مساءً</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Interactive Map Section -->
<section class="py-5" id="map-section">
    <div class="container" data-aos="fade-up">
        <h4 class="fw-bold text-dark mb-4 text-center text-md-start">المقر والخدمات الإقليمية</h4>
        <div class="ratio ratio-21x9 rounded-4 overflow-hidden shadow-sm" style="min-height: 350px;">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d110502.60389599558!2d31.188423483257005!3d30.05948381025531!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14583fa60b21beeb%3A0x79dfb296ef30e3b5!2sCairo%2C%20Cairo%20Governorate%2C%20Egypt!5e0!3m2!1sen!2s!4v1700000000000!5m2!1sen!2s"
                    style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>

@push('styles')
<style>
    .contact-hero {
        background: linear-gradient(135deg, rgba(248, 250, 252, 0.8) 0%, rgba(255, 255, 255, 0.9) 100%);
    }

    [data-bs-theme="dark"] .contact-hero {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%) !important;
    }

    .shape {
        position: absolute;
        border-radius: 50%;
        opacity: 0.05;
        z-index: 1;
    }
    .shape-1 {
        width: 250px;
        height: 250px;
        background: var(--primary-color);
        top: -80px;
        right: -80px;
    }
    .shape-2 {
        width: 180px;
        height: 180px;
        background: #0ea5e9;
        bottom: -40px;
        left: -40px;
    }

    .leading-relaxed {
        line-height: 1.8;
    }

    .social-link-item {
        background-color: #f8fafc;
        transition: all 0.3s ease;
    }
    
    .social-link-item:hover {
        background-color: #f1f5f9;
        transform: translateX(-5px);
    }

    .input-group-text {
        border-color: #dee2e6;
    }
    .form-control:focus + .input-group-text,
    .form-control:focus {
        border-color: var(--primary-color);
    }

    /* Submit Button Micro-animation */
    .submit-btn {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
    }

    /* Dark theme overrides */
    [data-bs-theme="dark"] .bg-light {
        background-color: #1e293b !important;
    }
    [data-bs-theme="dark"] .card {
        background-color: #1e293b !important;
        border-color: rgba(255, 255, 255, 0.05) !important;
    }
    [data-bs-theme="dark"] .text-dark {
        color: #f8fafc !important;
    }
    [data-bs-theme="dark"] .text-muted {
        color: #94a3b8 !important;
    }
    [data-bs-theme="dark"] .social-link-item {
        background-color: #152030;
    }
    [data-bs-theme="dark"] .social-link-item:hover {
        background-color: #1a2a40;
    }
    [data-bs-theme="dark"] .input-group-text {
        background-color: #0f172a !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #94a3b8 !important;
    }
    [data-bs-theme="dark"] .form-control {
        background-color: #0f172a !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #f8fafc !important;
    }
    [data-bs-theme="dark"] .form-control:focus {
        border-color: #818cf8 !important;
    }
    [data-bs-theme="dark"] .working-hours {
        background-color: #0f172a !important;
    }
    [data-bs-theme="dark"] iframe {
        filter: grayscale(0.5) invert(0.9) hue-rotate(180deg);
    }
</style>
@endpush
@endsection
