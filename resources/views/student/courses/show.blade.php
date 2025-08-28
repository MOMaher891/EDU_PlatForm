@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="course-details-page">
    <!-- Course Hero Section -->
    <section class="course-hero py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8" data-aos="fade-right">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb text-white">
                            <li class="breadcrumb-item">
                                <a href="{{ route('student.courses.index') }}" class="text-white text-decoration-none opacity-75">
                                    الكورسات
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('student.courses.index', ['category' => $course->category->id]) }}" class="text-white text-decoration-none opacity-75">
                                    {{ $course->category->name }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active text-white">{{ Str::limit($course->title, 30) }}</li>
                        </ol>
                    </nav>

                    <div class="course-badges mb-3">
                        <span class="badge bg-light text-dark px-3 py-2 me-2">{{ $course->category->name }}</span>
                        <span class="badge bg-{{ $course->level == 'beginner' ? 'success' : ($course->level == 'intermediate' ? 'warning' : 'danger') }} px-3 py-2 me-2">
                            {{ $course->level == 'beginner' ? 'مبتدئ' : ($course->level == 'intermediate' ? 'متوسط' : 'متقدم') }}
                        </span>
                        @if($course->is_featured)
                            <span class="badge bg-warning text-dark px-3 py-2">
                                <i class="fas fa-star me-1"></i>
                                مميز
                            </span>
                        @endif
                    </div>

                    <h1 class="display-5 fw-bold text-white mb-4">{{ $course->title }}</h1>
                    <p class="lead text-white opacity-90 mb-4">{{ $course->short_description }}</p>

                    <div class="course-meta d-flex flex-wrap gap-4 mb-4">
                        <div class="d-flex align-items-center text-white">
                            <i class="fas fa-star text-warning me-2"></i>
                            <span class="fw-bold me-1">4.8</span>
                            <span class="opacity-75">({{ rand(100, 1000) }} تقييم)</span>
                        </div>
                        <div class="d-flex align-items-center text-white">
                            <i class="fas fa-users me-2"></i>
                            <span>{{ $course->enrollments->count() }} طالب</span>
                        </div>
                        <div class="d-flex align-items-center text-white">
                            <i class="fas fa-clock me-2"></i>
                            <span>{{ $course->duration_hours }} ساعة</span>
                        </div>
                        <div class="d-flex align-items-center text-white">
                            <i class="fas fa-play-circle me-2"></i>
                            <span>{{ $totalLessons }} درس</span>
                        </div>
                        <div class="d-flex align-items-center text-white">
                            <i class="fas fa-globe me-2"></i>
                            <span>العربية</span>
                        </div>
                    </div>

                    <div class="instructor-info d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($course->instructor->name) }}&background=ffffff&color=6366f1&size=60"
                             class="rounded-circle me-3" alt="{{ $course->instructor->name }}">
                        <div class="text-white">
                            <h6 class="mb-1">{{ $course->instructor->name }}</h6>
                            <small class="opacity-75">مدرب معتمد</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4" data-aos="fade-left">
                    <div class="course-preview-card">
                        <!-- Course Preview Video/Image -->
                        <div class="preview-container position-relative mb-3">
                            <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80' }}"
                                 class="img-fluid rounded-4 shadow-lg" alt="{{ $course->title }}" style="width: 100%; height: 250px; object-fit: cover;">
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <button class="btn btn-light btn-lg rounded-circle" data-bs-toggle="modal" data-bs-target="#previewModal">
                                    <i class="fas fa-play text-primary"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container py-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Course Navigation Tabs -->
                <div class="course-tabs mb-4" data-aos="fade-up">
                    <ul class="nav nav-pills nav-fill bg-light rounded-pill p-1" id="courseTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button">
                                <i class="fas fa-info-circle me-1"></i>
                                نظرة عامة
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill" id="curriculum-tab" data-bs-toggle="pill" data-bs-target="#curriculum" type="button">
                                <i class="fas fa-list me-1"></i>
                                المنهج
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill" id="instructor-tab" data-bs-toggle="pill" data-bs-target="#instructor" type="button">
                                <i class="fas fa-user me-1"></i>
                                المدرب
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill" id="reviews-tab" data-bs-toggle="pill" data-bs-target="#reviews" type="button">
                                <i class="fas fa-star me-1"></i>
                                التقييمات
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content -->
                <div class="tab-content" id="courseTabContent">
                    <!-- Overview Tab -->
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" data-aos="fade-up">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h4 class="fw-bold mb-3">وصف الكورس</h4>
                                <div class="course-description">
                                    {!! nl2br(e($course->description)) !!}
                                </div>
                            </div>
                        </div>

                        <!-- What You'll Learn -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h4 class="fw-bold mb-3">ما ستتعلمه في هذا الكورس</h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                                            <span>إتقان المفاهيم الأساسية والمتقدمة</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                                            <span>تطبيق عملي على مشاريع حقيقية</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                                            <span>الحصول على شهادة معتمدة</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                                            <span>دعم مستمر من المدرب</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Requirements -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h4 class="fw-bold mb-3">المتطلبات</h4>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-laptop text-primary me-2"></i>
                                        جهاز كمبيوتر أو هاتف ذكي
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-wifi text-primary me-2"></i>
                                        اتصال بالإنترنت
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-heart text-primary me-2"></i>
                                        الرغبة في التعلم والتطوير
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Curriculum Tab -->
                    <div class="tab-pane fade" id="curriculum" role="tabpanel" data-aos="fade-up">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h4 class="fw-bold mb-0">محتوى الكورس</h4>
                                    <div class="text-muted">
                                        <small>{{ $course->sections->count() }} قسم • {{ $totalLessons }} درس • {{ $course->duration_hours }} ساعة</small>
                                    </div>
                                </div>

                                <div class="accordion" id="curriculumAccordion">
                                    @foreach($course->sections as $index => $section)
                                        @php
                                            $hasAccess = $isEnrolled || in_array($section->id, $accessibleSections->pluck('id')->toArray());
                                            $isPurchasable = $section->isPurchasable();
                                        @endphp
                                        <div class="accordion-item border-0 mb-3 shadow-sm">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }} fw-semibold"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#section{{ $section->id }}">
                                                    <div class="d-flex justify-content-between w-100 me-3">
                                                        <div class="d-flex align-items-center">
                                                            <span>
                                                                <i class="fas fa-folder me-2"></i>
                                                                {{ $section->title }}
                                                            </span>
                                                            @if($hasAccess)
                                                                <span class="badge bg-success ms-2">
                                                                    <i class="fas fa-check me-1"></i>
                                                                    متاح
                                                                </span>
                                                            @elseif($isPurchasable)
                                                                <span class="badge bg-warning ms-2">
                                                                    <i class="fas fa-shopping-cart me-1"></i>
                                                                    للشراء
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary ms-2">
                                                                    <i class="fas fa-lock me-1"></i>
                                                                    مقفل
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            @if($isPurchasable && !$hasAccess)
                                                                <div class="me-3">
                                                                    <span class="text-primary fw-bold">${{ $section->getEffectivePrice() }}</span>
                                                                    @if($section->discount_price)
                                                                        <small class="text-muted text-decoration-line-through ms-1">${{ $section->price }}</small>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                            <small class="text-muted">
                                                                {{ $section->lessons->count() }} دروس
                                                            </small>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="section{{ $section->id }}"
                                                 class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                                                 data-bs-parent="#curriculumAccordion">
                                                <div class="accordion-body">
                                                    @if($isPurchasable && !$hasAccess)
                                                        <div class="alert alert-info border-0 mb-3">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <div>
                                                                    <h6 class="fw-bold mb-1">شراء هذا القسم فقط</h6>
                                                                    <small class="text-muted">احصل على وصول فوري لهذا القسم</small>
                                                                </div>
                                                                <a href="{{ route('payment.section.checkout', ['course' => $course, 'section' => $section]) }}" 
                                                                   class="btn btn-primary btn-sm">
                                                                    <i class="fas fa-shopping-cart me-1"></i>
                                                                    شراء القسم - ${{ $section->getEffectivePrice() }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    @foreach($section->lessons as $lesson)
                                                        <div class="lesson-item d-flex justify-content-between align-items-center py-3 border-bottom">
                                                            <div class="d-flex align-items-center">
                                                                <div class="lesson-icon me-3">
                                                                    @if($lesson->file_type == 'video')
                                                                        <i class="fas fa-play-circle text-primary"></i>
                                                                    @elseif($lesson->file_type == 'pdf')
                                                                        <i class="fas fa-file-pdf text-danger"></i>
                                                                    @else
                                                                        <i class="fas fa-file-alt text-secondary"></i>
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-1">{{ $lesson->title }}</h6>
                                                                    @if($lesson->is_free)
                                                                        <span class="badge bg-success">مجاني</span>
                                                                    @elseif($hasAccess)
                                                                        <span class="badge bg-primary">متاح</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">مدفوع</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="lesson-meta text-end">
                                                                @if($lesson->video_duration)
                                                                    <small class="text-muted d-block">
                                                                        <i class="fas fa-clock me-1"></i>
                                                                        {{ gmdate('i:s', $lesson->video_duration) }}
                                                                    </small>
                                                                @endif
                                                                @if($lesson->is_free || $hasAccess)
                                                                    <a href="{{ route('student.courses.learn', $course, ['lesson' => $lesson->id]) }}" 
                                                                       class="btn btn-sm btn-outline-primary mt-1">
                                                                        <i class="fas fa-play me-1"></i>
                                                                        مشاهدة
                                                                    </a>
                                                                @else
                                                                    <i class="fas fa-lock text-muted"></i>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Instructor Tab -->
                    <div class="tab-pane fade" id="instructor" role="tabpanel" data-aos="fade-up">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-md-3 text-center mb-4">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($course->instructor->name) }}&background=6366f1&color=fff&size=150"
                                             class="rounded-circle mb-3" alt="{{ $course->instructor->name }}">
                                        <h5 class="fw-bold">{{ $course->instructor->name }}</h5>
                                        <p class="text-muted">مدرب معتمد</p>
                                    </div>
                                    <div class="col-md-9">
                                        <h4 class="fw-bold mb-3">نبذة عن المدرب</h4>
                                        <p class="text-muted mb-4">
                                            مدرب خبير في مجال {{ $course->category->name }} مع أكثر من 10 سنوات من الخبرة العملية.
                                            حاصل على شهادات متخصصة ومعتمدة دولياً. قام بتدريب آلاف الطلاب وساعدهم في تطوير مهاراتهم المهنية.
                                        </p>

                                        <div class="instructor-stats row g-3 mb-4">
                                            <div class="col-6 col-md-3">
                                                <div class="text-center">
                                                    <h5 class="fw-bold text-primary mb-1">{{ $course->instructor->instructedCourses->count() }}</h5>
                                                    <small class="text-muted">كورس</small>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="text-center">
                                                    <h5 class="fw-bold text-success mb-1">{{ rand(1000, 5000) }}</h5>
                                                    <small class="text-muted">طالب</small>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="text-center">
                                                    <h5 class="fw-bold text-warning mb-1">4.9</h5>
                                                    <small class="text-muted">تقييم</small>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="text-center">
                                                    <h5 class="fw-bold text-info mb-1">{{ rand(500, 2000) }}</h5>
                                                    <small class="text-muted">مراجعة</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="instructor-social">
                                            <h6 class="fw-bold mb-2">تواصل مع المدرب</h6>
                                            <div class="d-flex gap-2">
                                                <a href="#" class="btn btn-outline-primary btn-sm">
                                                    <i class="fab fa-linkedin"></i>
                                                </a>
                                                <a href="#" class="btn btn-outline-info btn-sm">
                                                    <i class="fab fa-twitter"></i>
                                                </a>
                                                <a href="#" class="btn btn-outline-dark btn-sm">
                                                    <i class="fas fa-globe"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reviews Tab -->
                    <div class="tab-pane fade" id="reviews" role="tabpanel" data-aos="fade-up">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <!-- Reviews Summary -->
                                <div class="reviews-summary row mb-4">
                                    <div class="col-md-4 text-center">
                                        <h2 class="display-4 fw-bold text-primary mb-2">4.8</h2>
                                        <div class="text-warning mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star"></i>
                                            @endfor
                                        </div>
                                        <p class="text-muted">{{ rand(100, 500) }} تقييم</p>
                                    </div>
                                    <div class="col-md-8">
                                        @for($i = 5; $i >= 1; $i--)
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="me-2">{{ $i }}</span>
                                                <i class="fas fa-star text-warning me-2"></i>
                                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                    <div class="progress-bar" style="width: {{ rand(60, 95) }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ rand(10, 100) }}</small>
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <!-- Individual Reviews -->
                                <div class="reviews-list">
                                    @for($i = 1; $i <= 5; $i++)
                                        <div class="review-item border-bottom pb-4 mb-4">
                                            <div class="d-flex align-items-start">
                                                <img src="https://ui-avatars.com/api/?name=User{{ $i }}&background=random"
                                                     class="rounded-circle me-3" width="50" height="50" alt="User">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <h6 class="fw-bold mb-1">المستخدم {{ $i }}</h6>
                                                            <div class="text-warning">
                                                                @for($j = 1; $j <= 5; $j++)
                                                                    <i class="fas fa-star{{ $j <= rand(4, 5) ? '' : ' opacity-25' }} small"></i>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">{{ rand(1, 30) }} يوم مضى</small>
                                                    </div>
                                                    <p class="text-muted mb-0">
                                                        كورس ممتاز ومفيد جداً. المدرب يشرح بطريقة واضحة ومبسطة.
                                                        أنصح به بشدة لكل من يريد تعلم هذا المجال.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>

                                <div class="text-center">
                                    <button class="btn btn-outline-primary">
                                        عرض المزيد من التقييمات
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Purchase Card -->
                <div class="card border-0 shadow-lg sticky-top" style="top: 100px;" data-aos="fade-left">
                    <div class="card-body p-4">
                        @if($isEnrolled)
                            <div class="alert alert-success border-0 mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle fa-2x me-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">أنت مسجل في هذا الكورس</h6>
                                        <small>يمكنك الوصول إلى جميع الدروس</small>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('student.courses.learn', $course) }}" class="btn btn-success btn-lg">
                                    <i class="fas fa-play me-2"></i>
                                    متابعة التعلم
                                </a>
                                <button class="btn btn-outline-primary">
                                    <i class="fas fa-download me-2"></i>
                                    تحميل الشهادة
                                </button>
                            </div>
                        @else
                            <div class="price-section text-center mb-4">
                                @if($course->getEffectivePrice() == 0)
                                    <h2 class="display-4 fw-bold text-success mb-2">مجاني</h2>
                                @else
                                    <h2 class="display-4 fw-bold text-primary mb-2">${{ $course->getEffectivePrice() }}</h2>
                                    @if($course->discount_price)
                                        <div class="mb-2">
                                            <span class="text-muted text-decoration-line-through fs-4">${{ $course->price }}</span>
                                            <span class="badge bg-danger ms-2 fs-6">
                                                خصم {{ round((($course->price - $course->discount_price) / $course->price) * 100) }}%
                                            </span>
                                        </div>
                                    @endif
                                @endif
                                <p class="text-muted mb-0">سعر لمرة واحدة • وصول مدى الحياة</p>
                            </div>

                            @auth
                                <div class="d-grid gap-2 mb-3">
                                    <a href="{{ route('payment.checkout', $course) }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        {{ $course->getEffectivePrice() == 0 ? 'التسجيل المجاني' : 'شراء الكورس الآن' }}
                                    </a>
                                    <button class="btn btn-outline-primary">
                                        <i class="fas fa-heart me-2"></i>
                                        إضافة للمفضلة
                                    </button>
                                </div>
                            @else
                                <div class="d-grid gap-2 mb-3">
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        سجل دخولك للشراء
                                    </a>
                                    <a href="{{ route('register') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-user-plus me-2"></i>
                                        إنشاء حساب جديد
                                    </a>
                                </div>
                            @endauth
                        @endif

                        <div class="guarantee-section text-center">
                            <div class="d-flex align-items-center justify-content-center text-success mb-2">
                                <i class="fas fa-shield-alt me-2"></i>
                                <small class="fw-semibold">ضمان استرداد المال</small>
                            </div>
                            <small class="text-muted">خلال 30 يوم من تاريخ الشراء</small>
                        </div>
                    </div>
                </div>

                <!-- Course Features -->
                <div class="card border-0 shadow-sm mt-4" data-aos="fade-left" data-aos-delay="100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-gift me-2"></i>
                            ما ستحصل عليه
                        </h6>
                        <ul class="list-unstyled">
                            <li class="d-flex align-items-center mb-3">
                                <i class="fas fa-play-circle text-primary me-3"></i>
                                <span>{{ $totalLessons }} درس فيديو عالي الجودة</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="fas fa-clock text-success me-3"></i>
                                <span>{{ $course->duration_hours }} ساعة من المحتوى</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="fas fa-infinity text-info me-3"></i>
                                <span>وصول مدى الحياة</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="fas fa-certificate text-warning me-3"></i>
                                <span>شهادة إتمام معتمدة</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="fas fa-mobile-alt text-secondary me-3"></i>
                                <span>متاح على جميع الأجهزة</span>
                            </li>
                            <li class="d-flex align-items-center mb-0">
                                <i class="fas fa-headset text-danger me-3"></i>
                                <span>دعم فني مجاني</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Share Course -->
                <div class="card border-0 shadow-sm mt-4" data-aos="fade-left" data-aos-delay="200">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-share-alt me-2"></i>
                            شارك الكورس
                        </h6>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary flex-fill" onclick="shareOnFacebook()">
                                <i class="fab fa-facebook"></i>
                            </button>
                            <button class="btn btn-outline-info flex-fill" onclick="shareOnTwitter()">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button class="btn btn-outline-success flex-fill" onclick="shareOnWhatsApp()">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                            <button class="btn btn-outline-secondary flex-fill" onclick="copyLink()">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title">معاينة الكورس</h5>
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
    .course-hero {
        position: relative;
        overflow: hidden;
    }

    .course-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .preview-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.3);
        border-radius: 1rem;
        transition: all 0.3s ease;
    }

    .preview-container:hover::before {
        background: rgba(0, 0, 0, 0.5);
    }

    .nav-pills .nav-link {
        color: #6c757d;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
    }

    .lesson-item {
        transition: all 0.3s ease;
        border-radius: 8px;
        margin: 0 -1rem;
        padding: 1rem !important;
    }

    .lesson-item:hover {
        background: rgba(99, 102, 241, 0.05);
        transform: translateX(5px);
    }

    .instructor-stats .text-center {
        padding: 1rem;
        border-radius: 8px;
        background: rgba(99, 102, 241, 0.05);
        transition: all 0.3s ease;
    }

    .instructor-stats .text-center:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .review-item {
        transition: all 0.3s ease;
        border-radius: 8px;
        margin: 0 -1rem;
        padding: 1rem !important;
    }

    .review-item:hover {
        background: rgba(0, 0, 0, 0.02);
    }

    .sticky-top {
        transition: all 0.3s ease;
    }

    @media (max-width: 992px) {
        .sticky-top {
            position: relative !important;
            top: auto !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Share Functions
    function shareOnFacebook() {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent('{{ $course->title }}');
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
    }

    function shareOnTwitter() {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent('{{ $course->title }}');
        window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, '_blank', 'width=600,height=400');
    }

    function shareOnWhatsApp() {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent('{{ $course->title }}');
        window.open(`https://wa.me/?text=${title} ${url}`, '_blank');
    }

    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            // Show toast notification
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
            toast.style.zIndex = '1060';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check me-2"></i>
                        تم نسخ الرابط بنجاح!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            setTimeout(() => {
                document.body.removeChild(toast);
            }, 3000);
        });
    }

    // Smooth scroll to tabs
    document.querySelectorAll('[data-bs-toggle="pill"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function() {
            document.getElementById('courseTab').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });
    });

    // Auto-play preview video when modal opens
    document.getElementById('previewModal').addEventListener('shown.bs.modal', function() {
        const iframe = this.querySelector('iframe');
        const src = iframe.src;
        iframe.src = src + '?autoplay=1';
    });

    document.getElementById('previewModal').addEventListener('hidden.bs.modal', function() {
        const iframe = this.querySelector('iframe');
        iframe.src = iframe.src.replace('?autoplay=1', '');
    });
</script>
@endpush
@endsection
