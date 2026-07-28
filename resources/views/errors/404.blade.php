@extends('layouts.app')

@section('title', 'الصفحة غير موجودة (404)')
@section('meta_title', 'الصفحة غير موجودة - 404')
@section('meta_description', 'عذراً، الصفحة التي تبحث عنها غير موجودة. يمكنك تصفح أحدث الكورسات أو البحث عما تريده في المنصة التعليمية.')

@section('content')
<div class="error-404-page py-5">
    <div class="container py-4">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8" data-aos="fade-up">
                <!-- 404 Badge Graphic -->
                <div class="display-1 fw-bold text-primary mb-2" style="font-size: 6rem; letter-spacing: 2px;">
                    4<span class="text-warning">0</span>4
                </div>
                <div class="badge bg-danger-subtle text-danger px-3 py-2 fs-6 rounded-pill mb-4">
                    <i class="fas fa-exclamation-triangle me-1"></i> الخطأ 404 - الرابط غير موجود
                </div>

                <!-- Main Headline & Subtitle -->
                <h1 class="h2 fw-bold mb-3 text-dark">عذراً! الصفحة التي تبحث عنها غير موجودة أو تم نقلها</h1>
                <p class="lead text-muted mb-4">
                    قد تكون كتبت الرابط بشكل غير صحيح، أو أن هذه الصفحة تم حذفها. يمكنك استخدام البحث أدناه للوصول للكورس الذي تريده، أو استكشاف الكورسات المقترحة.
                </p>

                <!-- Search Bar to Keep User Engaged -->
                <div class="row justify-content-center mb-5">
                    <div class="col-md-8">
                        <form action="{{ route('student.courses.index') }}" method="GET" class="search-form">
                            <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border">
                                <input type="text" name="search" class="form-control border-0 px-4 fs-6" 
                                       placeholder="ابحث عن الكورس، اللغة، أو المجال الذي تود تعلمه..." required>
                                <button class="btn btn-primary px-4 fw-semibold" type="submit">
                                    <i class="fas fa-search me-1"></i> بحث
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Navigation Quick Buttons -->
                <div class="d-flex justify-content-center gap-3 flex-wrap mb-5">
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">
                        <i class="fas fa-home me-2"></i> العودة للصفحة الرئيسية
                    </a>
                    <a href="{{ route('student.courses.index') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4 shadow-sm">
                        <i class="fas fa-graduation-cap me-2"></i> تصفح جميع الكورسات
                    </a>
                    @if(Route::has('contact'))
                    <a href="{{ route('contact') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-4 shadow-sm">
                        <i class="fas fa-headset me-2 text-primary"></i> اتصل بالدعم الفني
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recommended Courses Section (Bounce Rate Reduction) -->
        @php
            $recommendedCourses = \App\Models\Course::where('is_published', true)
                ->with(['category', 'instructor'])
                ->inRandomOrder()
                ->take(3)
                ->get();
        @endphp

        @if($recommendedCourses->count() > 0)
            <div class="recommended-section mt-5 pt-4 border-top" data-aos="fade-up">
                <div class="text-center mb-4">
                    <h2 class="h4 fw-bold mb-2 text-dark">قد تهمك هذه الكورسات المميزة</h2>
                    <p class="text-muted small">اختر من أفضل الكورسات الأكثر شعبية لدينا واستكمل رحلة تعلمك</p>
                </div>

                <div class="row g-4">
                    @foreach($recommendedCourses as $course)
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm course-card rounded-4 overflow-hidden">
                                <div class="position-relative">
                                    <img src="{{ $course->thumbnail_url }}" class="card-img-top" 
                                         alt="{{ $course->title }}" loading="lazy" decoding="async"
                                         style="height: 200px; object-fit: cover;">
                                    @if($course->category)
                                        <span class="badge bg-primary position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill">
                                            {{ $course->category->name }}
                                        </span>
                                    @endif
                                </div>
                                <div class="card-body d-flex flex-column p-4">
                                    <h3 class="h6 fw-bold mb-2 text-dark line-clamp-2">
                                        <a href="{{ route('student.courses.show', $course) }}" class="text-decoration-none text-dark hover-primary">
                                            {{ $course->title }}
                                        </a>
                                    </h3>
                                    <p class="text-muted small mb-3 flex-grow-1 line-clamp-2">
                                        {{ $course->short_description }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                                        <span class="fw-bold text-primary">
                                            {{ $course->getEffectivePrice() == 0 ? 'مجاني' : \App\Models\Setting::formatPrice($course->getEffectivePrice()) }}
                                        </span>
                                        <a href="{{ route('student.courses.show', $course) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            عرض الكورس <i class="fas fa-arrow-left ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .hover-primary:hover {
        color: var(--primary-color) !important;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
