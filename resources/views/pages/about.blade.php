@extends('layouts.app')

@section('title', 'من نحن - عن منصة A+ Academy')

@section('content')
<!-- Hero Section -->
<section class="about-hero position-relative overflow-hidden py-5">
    <div class="container py-5 position-relative" style="z-index: 2;">
        <div class="row align-items-center min-vh-50 text-center text-lg-start">
            <div class="col-lg-6" data-aos="fade-up">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                    <i class="fas fa-graduation-cap me-1"></i>
                    تعرّف على منصتنا
                </span>
                <h1 class="display-4 fw-bold mb-4 text-dark">
                    بوابتك نحو تعليم متميز و
                    <span class="text-primary">مستقبل أفضل</span>
                </h1>
                <p class="lead text-muted mb-4 fs-5">
                    منصة A+ Academy هي منصة رائدة في التعليم الإلكتروني، نسعى لتمكين العقول الشابة وتوفير أفضل الكورسات التقنية والمهنية بأعلى جودة تحت إشراف نخبة من الخبراء والمهندسين.
                </p>
                <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3">
                    <a href="{{ route('student.courses.index') }}" class="btn btn-primary btn-lg px-4 py-3">
                        <i class="fas fa-rocket me-2"></i>
                        استكشف الكورسات المتاحة
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-primary btn-lg px-4 py-3">
                        <i class="fas fa-paper-plane me-2"></i>
                        تواصل معنا مباشرة
                    </a>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0" data-aos="zoom-in" data-aos-delay="200">
                <div class="about-hero-image position-relative">
                    <div class="floating-badge position-absolute bg-white shadow p-3 rounded-4 d-flex align-items-center gap-3" style="top: 10%; right: -20px; z-index: 3;">
                        <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">محتوى معتمد</h6>
                            <small class="text-muted">100% جودة عالية</small>
                        </div>
                    </div>
                    <div class="floating-badge position-absolute bg-white shadow p-3 rounded-4 d-flex align-items-center gap-3" style="bottom: 10%; left: -20px; z-index: 3;">
                        <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                            <i class="fas fa-star fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">تقييمات متميزة</h6>
                            <small class="text-muted">تجارب طلاب ناجحة</small>
                        </div>
                    </div>
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                         alt="التعليم الإلكتروني في A+ Academy" class="img-fluid rounded-4 shadow-lg w-100">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Background Shapes -->
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden" style="z-index: 1;">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
    </div>
</section>

<!-- Stats Counter Section -->
<section class="py-5 bg-primary text-white position-relative overflow-hidden">
    <div class="container position-relative" style="z-index: 2;">
        <div class="row text-center">
            <div class="col-6 col-lg-3 mb-4 mb-lg-0" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card py-3">
                    <i class="fas fa-book fa-2x mb-3 opacity-75"></i>
                    <h2 class="display-5 fw-bold mb-2 counter" data-target="{{ $coursesCount }}">0</h2>
                    <p class="fs-6 mb-0">كورس تعليمي متاح</p>
                </div>
            </div>
            <div class="col-6 col-lg-3 mb-4 mb-lg-0" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card py-3">
                    <i class="fas fa-users fa-2x mb-3 opacity-75"></i>
                    <h2 class="display-5 fw-bold mb-2 counter" data-target="{{ $studentsCount }}">0</h2>
                    <p class="fs-6 mb-0">طالب نشط مسجل</p>
                </div>
            </div>
            <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card py-3">
                    <i class="fas fa-chalkboard-teacher fa-2x mb-3 opacity-75"></i>
                    <h2 class="display-5 fw-bold mb-2 counter" data-target="{{ $instructorsCount }}">0</h2>
                    <p class="fs-6 mb-0">مدرب وخبير متميز</p>
                </div>
            </div>
            <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-card py-3">
                    <i class="fas fa-graduation-cap fa-2x mb-3 opacity-75"></i>
                    <h2 class="display-5 fw-bold mb-2 counter" data-target="{{ $enrollmentsCount }}">0</h2>
                    <p class="fs-6 mb-0">عملية انضمام للكورسات</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vision & Mission Section -->
<section class="py-5 my-5">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <div class="col-md-6" data-aos="fade-right">
                <div class="card h-100 border-0 shadow-sm p-4 text-center text-md-start">
                    <div class="card-body">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary d-inline-flex p-3 rounded-circle mb-4">
                            <i class="fas fa-eye fa-2x"></i>
                        </div>
                        <h3 class="fw-bold mb-3 text-dark">رؤيتنا</h3>
                        <p class="text-muted leading-relaxed">
                            نسعى لأن نكون المنصة التعليمية الأولى والأنسب لكل طالب عربي يبحث عن المعرفة الحقيقية والتطبيق العملي. نهدف لتقليص الفجوة بين الحياة الأكاديمية ومتطلبات سوق العمل الفعلي عبر توفير مسارات تعليمية وتطبيقية متكاملة.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-left" data-aos-delay="200">
                <div class="card h-100 border-0 shadow-sm p-4 text-center text-md-start">
                    <div class="card-body">
                        <div class="icon-box bg-success bg-opacity-10 text-success d-inline-flex p-3 rounded-circle mb-4">
                            <i class="fas fa-bullseye fa-2x"></i>
                        </div>
                        <h3 class="fw-bold mb-3 text-dark">رسالتنا</h3>
                        <p class="text-muted leading-relaxed">
                            تقديم تجربة تعليمية فريدة وميسرة تمتاز بالمرونة والجودة العالية. نلتزم بدعم المتعلمين طوال رحلتهم وتزويدهم بالمعرفة المتجددة وتوفير الأدوات اللازمة لتحويل شغفهم إلى مشاريع ناجحة وفرص عمل واعدة.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Core Values Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5" data-aos="fade-up">
            <div class="col-lg-6 mx-auto">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">قيمنا الأساسية</span>
                <h2 class="fw-bold mb-3 text-dark">المبادئ التي تقود نجاحنا</h2>
                <p class="text-muted">هذه القيم هي البوصلة التي توجهنا دائمًا لتقديم الأفضل لطلابنا</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card border-0 shadow-sm h-100 p-3 text-center">
                    <div class="card-body">
                        <div class="value-icon text-primary mb-3">
                            <i class="fas fa-award fa-3x"></i>
                        </div>
                        <h5 class="fw-bold mb-3 text-dark">الجودة والتميز</h5>
                        <p class="text-muted mb-0">نحرص على مراجعة المحتوى بدقة وعناية تامة لنقدم فيديوهات ومواد مساندة تفوق التوقعات.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 shadow-sm h-100 p-3 text-center">
                    <div class="card-body">
                        <div class="value-icon text-success mb-3">
                            <i class="fas fa-lightbulb fa-3x"></i>
                        </div>
                        <h5 class="fw-bold mb-3 text-dark">الابتكار والتجدد</h5>
                        <p class="text-muted mb-0">نتابع أحدث التطورات التقنية ونقوم بتحديث كورساتنا باستمرار لتواكب سوق العمل المتغير.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card border-0 shadow-sm h-100 p-3 text-center">
                    <div class="card-body">
                        <div class="value-icon text-warning mb-3">
                            <i class="fas fa-handshake fa-3x"></i>
                        </div>
                        <h5 class="fw-bold mb-3 text-dark">الالتزام والدعم</h5>
                        <p class="text-muted mb-0">علاقتنا بالطالب لا تنتهي بانتهاء الكورس، نوفر قنوات دعم وتواصل لحل أي عوائق برمجية أو تقنية.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Founder Section -->
<section class="py-5 my-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 text-center mb-4 mb-lg-0" data-aos="fade-right">
                <div class="founder-image-wrapper d-inline-block position-relative">
                    <div class="founder-decor"></div>
                    <img src="https://ui-avatars.com/api/?name=Mohamed+Maher&background=6366f1&color=fff&size=300"
                         alt="م. محمد ماهر" class="img-fluid rounded-circle shadow-lg position-relative" style="z-index: 2; width: 250px; height: 250px; border: 8px solid #fff;">
                </div>
            </div>
            <div class="col-lg-7" data-aos="fade-left" data-aos-delay="200">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">مؤسس المنصة</span>
                <h3 class="fw-bold mb-3 text-dark">كلمة مؤسس منصة A+ Academy</h3>
                <h5 class="text-primary mb-3">م. محمد ماهر (Software Engineer & Educator)</h5>
                <p class="text-muted leading-relaxed mb-4">
                    "بدأنا A+ Academy برؤية واضحة: إزالة كل الحواجز أمام الطلاب الطموحين وتمكينهم من تعلم البرمجة والتقنيات الحديثة بشكل ميسر وعملي بعيداً عن التلقين النظري الجاف. نحن هنا لنوجهك، خطوة بخطوة، لتبني مشاريعك الخاصة وتدخل سوق العمل بكل ثقة."
                </p>
                <div class="founder-signature">
                    <p class="mb-0 fw-bold text-dark">م. محمد ماهر</p>
                    <small class="text-muted">المؤسس والمدير التنفيذي لـ A+ Academy</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-light text-center">
    <div class="container py-4" data-aos="zoom-in">
        <h2 class="fw-bold mb-4 text-dark">ابدأ رحلتك التعليمية اليوم معنا</h2>
        <p class="text-muted lead mb-4">انضم إلى مجتمعنا المتنامي وابدأ باكتساب المهارات التي تفتح لك أبواب المستقبل.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('student.courses.index') }}" class="btn btn-primary btn-lg px-4 py-3">ابدأ التعلم الآن</a>
            @guest
            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-4 py-3">أنشئ حسابك المجاني</a>
            @endguest
        </div>
    </div>
</section>

@push('styles')
<style>
    .about-hero {
        background: linear-gradient(135deg, rgba(248, 250, 252, 0.8) 0%, rgba(255, 255, 255, 0.9) 100%);
    }

    [data-bs-theme="dark"] .about-hero {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%) !important;
    }

    .shape {
        position: absolute;
        border-radius: 50%;
        opacity: 0.05;
        z-index: 1;
    }
    .shape-1 {
        width: 300px;
        height: 300px;
        background: var(--primary-color);
        top: -100px;
        right: -100px;
    }
    .shape-2 {
        width: 200px;
        height: 200px;
        background: #f43f5e;
        bottom: -50px;
        left: -50px;
    }

    .leading-relaxed {
        line-height: 1.8;
    }

    .founder-image-wrapper {
        position: relative;
    }

    .founder-decor {
        position: absolute;
        top: -15px;
        left: -15px;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 4px dashed var(--primary-color);
        z-index: 1;
        animation: rotateDecor 20s linear infinite;
    }

    @keyframes rotateDecor {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Dark theme support overrides */
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
    [data-bs-theme="dark"] .floating-badge {
        background-color: #1e293b !important;
        color: #f8fafc !important;
    }
    [data-bs-theme="dark"] .founder-decor {
        border-color: #818cf8;
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
            if (target === 0) {
                counter.textContent = '0';
                return;
            }
            const increment = Math.max(1, Math.ceil(target / 80));
            let current = 0;

            const updateCounter = () => {
                if (current < target) {
                    current += increment;
                    if (current > target) current = target;
                    counter.textContent = current + '+';
                    setTimeout(updateCounter, 25);
                } else {
                    counter.textContent = target + '+';
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
    }, { threshold: 0.2 });

    const statsSection = document.querySelector('.counter')?.closest('section');
    if (statsSection) {
        observer.observe(statsSection);
    }
</script>
@endpush
@endsection
