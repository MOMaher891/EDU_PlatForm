@extends('layouts.app')

@section('title', 'الصفحة الرئيسية - منصة التعلم الإلكتروني')

@section('content')
<!-- Hero Section -->
<section class="hero-section position-relative overflow-hidden">
    <div class="container py-5">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="hero-content">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                        <i class="fas fa-star me-1"></i>
                        منصة التعلم الأولى في المنطقة
                    </span>
                    <h1 class="display-3 fw-bold mb-4 text-dark">
                        تعلم مهارات
                        <span class="text-primary">المستقبل</span>
                        مع أفضل المدربين
                    </h1>
                    <p class="lead text-muted mb-4 fs-5">
                        اكتشف آلاف الكورسات في مختلف المجالات وطور مهاراتك مع خبراء متخصصين.
                        ابدأ رحلة التعلم اليوم واحصل على شهادات معتمدة.
                    </p>
                    <div class="hero-stats row g-3 mb-4">
                        <div class="col-4">
                            <div class="text-center">
                                <h4 class="fw-bold text-primary mb-0">{{ \App\Models\Course::where('is_published', true)->count() }}+</h4>
                                <small class="text-muted">كورس</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <h4 class="fw-bold text-success mb-0">{{ \App\Models\User::where('role', 'student')->count() }}+</h4>
                                <small class="text-muted">طالب</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <h4 class="fw-bold text-warning mb-0">{{ \App\Models\User::where('role', 'instructor')->count() }}+</h4>
                                <small class="text-muted">مدرب</small>
                            </div>
                        </div>
                    </div>
                    <div class="hero-actions d-flex flex-wrap gap-3">
                        <a href="{{ route('student.courses.index') }}" class="btn btn-primary btn-lg px-4 py-3">
                            <i class="fas fa-rocket me-2"></i>
                            ابدأ التعلم الآن
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-4 py-3">
                                <i class="fas fa-user-plus me-2"></i>
                                إنشاء حساب مجاني
                            </a>
                        @endguest
                        <button class="btn btn-link btn-lg text-decoration-none" data-bs-toggle="modal" data-bs-target="#videoModal">
                            <i class="fas fa-play-circle me-2"></i>
                            شاهد الفيديو التعريفي
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="hero-image position-relative">
                    <div class="floating-cards">
                        <div class="card glass position-absolute" style="top: 10%; right: 10%; width: 200px;" data-aos="fade-up" data-aos-delay="200">
                            <div class="card-body text-center">
                                <i class="fas fa-code text-primary fa-2x mb-2"></i>
                                <h6 class="fw-bold">البرمجة</h6>
                                <small class="text-muted">120+ كورس</small>
                            </div>
                        </div>
                        <div class="card glass position-absolute" style="bottom: 20%; left: 10%; width: 180px;" data-aos="fade-up" data-aos-delay="400">
                            <div class="card-body text-center">
                                <i class="fas fa-paint-brush text-success fa-2x mb-2"></i>
                                <h6 class="fw-bold">التصميم</h6>
                                <small class="text-muted">85+ كورس</small>
                            </div>
                        </div>
                        <div class="card glass position-absolute" style="top: 40%; left: 30%; width: 160px;" data-aos="fade-up" data-aos-delay="600">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-line text-warning fa-2x mb-2"></i>
                                <h6 class="fw-bold">التسويق</h6>
                                <small class="text-muted">65+ كورس</small>
                            </div>
                        </div>
                    </div>
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                         alt="التعلم الإلكتروني" class="img-fluid rounded-4 shadow-lg">
                </div>
            </div>
        </div>
    </div>

    <!-- Background Elements -->
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden" style="z-index: -1;">
        <div class="position-absolute" style="top: 10%; right: 10%; width: 100px; height: 100px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; opacity: 0.1;"></div>
        <div class="position-absolute" style="bottom: 20%; left: 15%; width: 150px; height: 150px; background: linear-gradient(135deg, #f093fb, #f5576c); border-radius: 50%; opacity: 0.1;"></div>
        <div class="position-absolute" style="top: 50%; left: 5%; width: 80px; height: 80px; background: linear-gradient(135deg, #4facfe, #00f2fe); border-radius: 50%; opacity: 0.1;"></div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5" data-aos="fade-up">
            <div class="col-12">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                    لماذا نحن؟
                </span>
                <h2 class="display-5 fw-bold mb-3">مميزات تجعلنا الأفضل</h2>
                <p class="lead text-muted">نوفر لك تجربة تعليمية متكاملة ومتطورة</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-play text-primary fa-2x"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-3">فيديوهات عالية الجودة</h4>
                        <p class="text-muted">محتوى مرئي بدقة 4K مع شرح واضح ومفصل لضمان فهم أفضل وتجربة تعليمية ممتازة</p>
                        <div class="feature-stats mt-3">
                            <small class="text-primary fw-bold">
                                <i class="fas fa-check-circle me-1"></i>
                                جودة 4K
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-certificate text-success fa-2x"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-3">شهادات معتمدة</h4>
                        <p class="text-muted">احصل على شهادات إتمام معتمدة دولياً تضيف قيمة لسيرتك الذاتية وتفتح لك آفاق جديدة</p>
                        <div class="feature-stats mt-3">
                            <small class="text-success fw-bold">
                                <i class="fas fa-award me-1"></i>
                                معتمدة دولياً
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-4">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-users text-warning fa-2x"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-3">مدربين خبراء</h4>
                        <p class="text-muted">تعلم من أفضل المتخصصين والخبراء في مجالاتهم مع سنوات من الخبرة العملية والأكاديمية</p>
                        <div class="feature-stats mt-3">
                            <small class="text-warning fw-bold">
                                <i class="fas fa-star me-1"></i>
                                خبرة +10 سنوات
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-4">
                            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-mobile-alt text-info fa-2x"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-3">تعلم في أي مكان</h4>
                        <p class="text-muted">منصة متجاوبة تعمل على جميع الأجهزة، تعلم من الهاتف أو الكمبيوتر في أي وقت ومكان</p>
                        <div class="feature-stats mt-3">
                            <small class="text-info fw-bold">
                                <i class="fas fa-clock me-1"></i>
                                24/7 متاح
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-4">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-headset text-danger fa-2x"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-3">دعم فني متميز</h4>
                        <p class="text-muted">فريق دعم فني متخصص متاح على مدار الساعة لمساعدتك في أي استفسار أو مشكلة تقنية</p>
                        <div class="feature-stats mt-3">
                            <small class="text-danger fw-bold">
                                <i class="fas fa-phone me-1"></i>
                                دعم فوري
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-4">
                            <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-infinity text-secondary fa-2x"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-3">وصول مدى الحياة</h4>
                        <p class="text-muted">بمجرد شراء الكورس، يمكنك الوصول إليه مدى الحياة مع جميع التحديثات والإضافات الجديدة</p>
                        <div class="feature-stats mt-3">
                            <small class="text-secondary fw-bold">
                                <i class="fas fa-infinity me-1"></i>
                                إلى الأبد
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Courses -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5" data-aos="fade-up">
            <div class="col-12">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                    الكورسات المميزة
                </span>
                <h2 class="display-5 fw-bold mb-3">أشهر الكورسات على المنصة</h2>
                <p class="lead text-muted">اكتشف الكورسات الأكثر شعبية والأعلى تقييماً</p>
            </div>
        </div>

        <div class="row g-4">
            @php
                $featuredCourses = \App\Models\Course::where('is_published', true)
                    ->where('is_featured', true)
                    ->with(['instructor', 'category'])
                    ->take(6)
                    ->get();
            @endphp

            @forelse($featuredCourses as $index => $course)
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                    <div class="card course-card h-100 border-0 shadow-sm">
                        <div class="position-relative overflow-hidden">
                            <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80' }}"
                                 class="card-img-top" alt="{{ $course->title }}" style="height: 250px; object-fit: cover;">

                            <!-- Course Level Badge -->
                            <span class="badge position-absolute top-0 start-0 m-3
                                {{ $course->level == 'beginner' ? 'bg-success' : ($course->level == 'intermediate' ? 'bg-warning' : 'bg-danger') }}">
                                {{ $course->level == 'beginner' ? 'مبتدئ' : ($course->level == 'intermediate' ? 'متوسط' : 'متقدم') }}
                            </span>

                            <!-- Discount Badge -->
                            @if($course->discount_price)
                                <span class="badge bg-danger position-absolute top-0 end-0 m-3">
                                    خصم {{ round((($course->price - $course->discount_price) / $course->price) * 100) }}%
                                </span>
                            @endif

                            <!-- Hover Overlay -->
                            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center opacity-0 transition-opacity" style="transition: opacity 0.3s ease;">
                                <a href="{{ route('student.courses.show', $course) }}" class="btn btn-light btn-lg">
                                    <i class="fas fa-eye me-2"></i>
                                    عرض التفاصيل
                                </a>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $course->category->name }}</span>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $i <= 4 ? '' : ' opacity-25' }}"></i>
                                    @endfor
                                    <small class="text-muted ms-1">(4.8)</small>
                                </div>
                            </div>

                            <h5 class="card-title fw-bold mb-3">{{ $course->title }}</h5>
                            <p class="card-text text-muted mb-3">
                                {{ Str::limit($course->short_description, 100) }}
                            </p>

                            <div class="course-meta d-flex justify-content-between align-items-center mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $course->duration_hours }} ساعة
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-users me-1"></i>
                                    {{ rand(50, 500) }} طالب
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-play-circle me-1"></i>
                                    {{ rand(20, 100) }} درس
                                </small>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="instructor d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($course->instructor->name) }}&background=6366f1&color=fff"
                                         class="rounded-circle me-2" width="32" height="32" alt="{{ $course->instructor->name }}">
                                    <small class="text-muted">{{ $course->instructor->name }}</small>
                                </div>
                                <div class="price">
                                    <span class="text-primary fw-bold fs-5">${{ $course->getEffectivePrice() }}</span>
                                    @if($course->discount_price)
                                        <span class="text-muted text-decoration-line-through ms-2 small">${{ $course->price }}</span>
                                    @endif
                                </div>
                            </div>

                            <a href="{{ route('student.courses.show', $course) }}" class="btn btn-primary w-100">
                                <i class="fas fa-shopping-cart me-2"></i>
                                عرض الكورس
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center" data-aos="fade-up">
                    <div class="py-5">
                        <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                        <h4>لا توجد كورسات مميزة حالياً</h4>
                        <p class="text-muted">سيتم إضافة كورسات مميزة قريباً</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('student.courses.index') }}" class="btn btn-outline-primary btn-lg px-5">
                عرض جميع الكورسات
                <i class="fas fa-arrow-left ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container position-relative">
        <div class="row text-center text-white">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-item">
                    <div class="stat-icon mb-3">
                        <i class="fas fa-book fa-3x opacity-75"></i>
                    </div>
                    <h2 class="display-4 fw-bold mb-2 counter" data-target="{{ \App\Models\Course::where('is_published', true)->count() }}">0</h2>
                    <p class="fs-5 mb-0">كورس متاح</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-item">
                    <div class="stat-icon mb-3">
                        <i class="fas fa-users fa-3x opacity-75"></i>
                    </div>
                    <h2 class="display-4 fw-bold mb-2 counter" data-target="{{ \App\Models\User::where('role', 'student')->count() }}">0</h2>
                    <p class="fs-5 mb-0">طالب مسجل</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-item">
                    <div class="stat-icon mb-3">
                        <i class="fas fa-chalkboard-teacher fa-3x opacity-75"></i>
                    </div>
                    <h2 class="display-4 fw-bold mb-2 counter" data-target="{{ \App\Models\User::where('role', 'instructor')->count() }}">0</h2>
                    <p class="fs-5 mb-0">مدرب خبير</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-item">
                    <div class="stat-icon mb-3">
                        <i class="fas fa-graduation-cap fa-3x opacity-75"></i>
                    </div>
                    <h2 class="display-4 fw-bold mb-2 counter" data-target="{{ \App\Models\CourseEnrollment::count() }}">0</h2>
                    <p class="fs-5 mb-0">تسجيل في الكورسات</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Background Elements -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="z-index: -1;">
        <div class="position-absolute" style="top: 20%; right: 10%; width: 200px; height: 200px; background: rgba(255, 255, 255, 0.1); border-radius: 50%;"></div>
        <div class="position-absolute" style="bottom: 10%; left: 15%; width: 150px; height: 150px; background: rgba(255, 255, 255, 0.05); border-radius: 50%;"></div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center" data-aos="fade-up">
                <div class="cta-content">
                    <h2 class="display-5 fw-bold mb-4">هل أنت مستعد لبدء رحلة التعلم؟</h2>
                    <p class="lead text-muted mb-4">
                        انضم إلى آلاف الطلاب الذين يطورون مهاراتهم معنا كل يوم.
                        ابدأ اليوم واحصل على خصم 50% على أول كورس لك!
                    </p>
                    <div class="cta-actions d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('student.courses.index') }}" class="btn btn-primary btn-lg px-5 py-3">
                            <i class="fas fa-rocket me-2"></i>
                            ابدأ التعلم الآن
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-5 py-3">
                                <i class="fas fa-user-plus me-2"></i>
                                إنشاء حساب مجاني
                            </a>
                        @endguest
                    </div>
                    <div class="cta-features mt-4">
                        <div class="row g-3 justify-content-center">
                            <div class="col-auto">
                                <small class="text-muted">
                                    <i class="fas fa-check text-success me-1"></i>
                                    بدون رسوم اشتراك
                                </small>
                            </div>
                            <div class="col-auto">
                                <small class="text-muted">
                                    <i class="fas fa-check text-success me-1"></i>
                                    شهادات معتمدة
                                </small>
                            </div>
                            <div class="col-auto">
                                <small class="text-muted">
                                    <i class="fas fa-check text-success me-1"></i>
                                    دعم فني مجاني
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title">فيديو تعريفي عن المنصة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ratio ratio-16x9">
                    <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(248, 250, 252, 0.9));
        position: relative;
    }

    .floating-cards .card {
        animation: float 6s ease-in-out infinite;
    }

    .floating-cards .card:nth-child(2) {
        animation-delay: -2s;
    }

    .floating-cards .card:nth-child(3) {
        animation-delay: -4s;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }

    .course-card:hover .position-absolute {
        opacity: 1 !important;
    }

    .counter {
        transition: all 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    // Counter Animation
    function animateCounters() {
        const counters = document.querySelectorAll('.counter');

        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const increment = target / 100;
            let current = 0;

            const updateCounter = () => {
                if (current < target) {
                    current += increment;
                    counter.textContent = Math.ceil(current);
                    setTimeout(updateCounter, 20);
                } else {
                    counter.textContent = target;
                }
            };

            updateCounter();
        });
    }

    // Trigger counter animation when section is visible
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.unobserve(entry.target);
            }
        });
    });

    const statsSection = document.querySelector('.counter').closest('section');
    if (statsSection) {
        observer.observe(statsSection);
    }
</script>
@endpush
@endsection
