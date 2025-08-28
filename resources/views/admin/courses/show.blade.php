@extends('layouts.app')

@section('title', 'تفاصيل الكورس - ' . $course->title)

@section('content')
<div class="admin-course-show-page">
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
                                <a href="{{ route('admin.courses.index') }}">
                                    <i class="fas fa-book"></i>
                                    الكورسات
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                تفاصيل الكورس
                            </li>
                        </ol>
                    </nav>
                    <h1 class="page-title">
                        <i class="fas fa-book me-3"></i>
                        تفاصيل الكورس
                    </h1>
                </div>
                <div class="col-md-6 text-end">
                    <div class="header-actions">
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-right me-1"></i>
                            العودة للقائمة
                        </a>
                        <a href="{{ route('admin.sections.index', $course) }}" class="btn btn-success me-2">
                            <i class="fas fa-list me-1"></i>
                            إدارة الأقسام
                        </a>
                        <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>
                            تعديل الكورس
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Course Details -->
            <div class="col-lg-8">
                <!-- Course Header -->
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up">
                    <div class="card-body p-0">
                        <div class="course-header">
                            <div class="course-thumbnail">
                                <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://via.placeholder.com/400x250' }}"
                                     alt="{{ $course->title }}" class="w-100">
                                @if($course->preview_video)
                                    <div class="video-overlay">
                                        <button class="btn btn-light btn-lg" onclick="playPreviewVideo()">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div class="course-info p-4">
                                <div class="course-meta mb-3">
                                    <span class="badge bg-info me-2">{{ $course->category->name }}</span>
                                    <span class="badge bg-secondary me-2">{{ $course->level }}</span>
                                    @if($course->is_featured)
                                        <span class="badge bg-warning">مميز</span>
                                    @endif
                                </div>
                                <h2 class="course-title mb-3">{{ $course->title }}</h2>
                                <p class="course-description text-muted mb-3">{{ $course->short_description }}</p>

                                <div class="course-stats row text-center">
                                    <div class="col-3">
                                        <div class="stat-item">
                                            <h4 class="stat-number">{{ $course->enrollments_count ?? 0 }}</h4>
                                            <p class="stat-label">الطلاب</p>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="stat-item">
                                            <h4 class="stat-number">{{ $course->sections->count() }}</h4>
                                            <p class="stat-label">الأقسام</p>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="stat-item">
                                            <h4 class="stat-number">{{ $course->getTotalLessons() }}</h4>
                                            <p class="stat-label">الدروس</p>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="stat-item">
                                            <h4 class="stat-number">{{ $course->duration_hours }}</h4>
                                            <p class="stat-label">الساعات</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Content -->
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>
                            محتوى الكورس
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($course->sections->count() > 0)
                            <div class="course-sections">
                                @foreach($course->sections as $section)
                                    <div class="section-item mb-3">
                                        <div class="section-header" data-bs-toggle="collapse"
                                             data-bs-target="#section{{ $section->id }}" aria-expanded="false">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="section-title mb-0">{{ $section->title }}</h6>
                                                    <small class="text-muted">{{ $section->lessons->count() }} درس</small>
                                                </div>
                                                <i class="fas fa-chevron-down"></i>
                                            </div>
                                        </div>
                                        <div class="collapse" id="section{{ $section->id }}">
                                            <div class="section-content">
                                                <div class="lessons-list">
                                                    @foreach($section->lessons as $lesson)
                                                        <div class="lesson-item">
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-play-circle text-primary me-2"></i>
                                                                <span class="lesson-title">{{ $lesson->title }}</span>
                                                                <span class="lesson-duration ms-auto">{{ $lesson->duration_minutes }} دقيقة</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-list fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد أقسام</h5>
                                <p class="text-muted">لم يتم إضافة أي قسم لهذا الكورس بعد</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Course Description -->
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-text me-2"></i>
                            الوصف التفصيلي
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="course-description-content">
                            {!! nl2br(e($course->description)) !!}
                        </div>
                    </div>
                </div>

                <!-- What You'll Learn -->
                @if($course->what_you_learn && count($course->what_you_learn) > 0)
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-graduation-cap me-2"></i>
                            ماذا ستتعلم
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="learning-points">
                            @foreach($course->what_you_learn as $point)
                                <div class="learning-point">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span>{{ $point }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Requirements -->
                @if($course->requirements && count($course->requirements) > 0)
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>
                            المتطلبات الأساسية
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="requirements-list">
                            @foreach($course->requirements as $requirement)
                                <div class="requirement-item">
                                    <i class="fas fa-circle text-primary me-2"></i>
                                    <span>{{ $requirement }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Course Info Card -->
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            معلومات الكورس
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="course-info-list">
                            <div class="info-item mb-3">
                                <div class="info-label">المدرب:</div>
                                <div class="info-value">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($course->instructor->name) }}&background=6366f1&color=fff&size=32"
                                             alt="{{ $course->instructor->name }}" class="rounded-circle me-2">
                                        <span>{{ $course->instructor->name }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="info-item mb-3">
                                <div class="info-label">التصنيف:</div>
                                <div class="info-value">
                                    <span class="badge bg-info">{{ $course->category->name }}</span>
                                </div>
                            </div>
                            <div class="info-item mb-3">
                                <div class="info-label">المستوى:</div>
                                <div class="info-value">
                                    <span class="badge bg-secondary">{{ $course->level }}</span>
                                </div>
                            </div>
                            <div class="info-item mb-3">
                                <div class="info-label">المدة:</div>
                                <div class="info-value">{{ $course->duration_hours }} ساعة</div>
                            </div>
                            <div class="info-item mb-3">
                                <div class="info-label">تاريخ الإنشاء:</div>
                                <div class="info-value">{{ $course->created_at->format('Y/m/d') }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">آخر تحديث:</div>
                                <div class="info-value">{{ $course->updated_at->format('Y/m/d') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-dollar-sign me-2"></i>
                            التسعير
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="pricing-info">
                            @if($course->discount_price)
                                <div class="original-price text-decoration-line-through text-muted">
                                    {{ $course->price }} ريال
                                </div>
                                <div class="discount-price text-success fw-bold fs-4">
                                    {{ $course->discount_price }} ريال
                                </div>
                                <div class="discount-badge">
                                    <span class="badge bg-danger">{{ $course->getDiscountPercentage() }}% خصم</span>
                                </div>
                            @else
                                <div class="price fw-bold fs-4">
                                    {{ $course->price }} ريال
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Course Status -->
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-toggle-on me-2"></i>
                            حالة الكورس
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="status-info">
                            <div class="status-item mb-2">
                                <span class="status-label">النشر:</span>
                                @if($course->is_published)
                                    <span class="badge bg-success">منشور</span>
                                @else
                                    <span class="badge bg-warning">مسودة</span>
                                @endif
                            </div>
                            <div class="status-item mb-2">
                                <span class="status-label">التصنيف:</span>
                                @if($course->is_featured)
                                    <span class="badge bg-primary">مميز</span>
                                @else
                                    <span class="badge bg-secondary">عادي</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enrollments -->
                <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="400">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-users me-2"></i>
                            الطلاب المسجلين
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($course->enrollments->count() > 0)
                            <div class="enrollments-list">
                                @foreach($course->enrollments->take(5) as $enrollment)
                                    <div class="enrollment-item d-flex align-items-center mb-2">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($enrollment->user->name) }}&background=6366f1&color=fff&size=32"
                                             alt="{{ $enrollment->user->name }}" class="rounded-circle me-2">
                                        <div class="enrollment-info">
                                            <div class="enrollment-name">{{ $enrollment->user->name }}</div>
                                            <small class="text-muted">{{ $enrollment->created_at->format('Y/m/d') }}</small>
                                        </div>
                                    </div>
                                @endforeach
                                @if($course->enrollments->count() > 5)
                                    <div class="text-center mt-2">
                                        <small class="text-muted">و {{ $course->enrollments->count() - 5 }} طالب آخر</small>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">لا يوجد طلاب مسجلين</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Video Preview Modal -->
@if($course->preview_video)
<div class="modal fade" id="videoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">معاينة الكورس</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <video id="previewVideo" controls class="w-100">
                    <source src="{{ asset('storage/' . $course->preview_video) }}" type="video/mp4">
                    متصفحك لا يدعم تشغيل الفيديو.
                </video>
            </div>
        </div>
    </div>
</div>
@endif

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

.course-header {
    position: relative;
}

.course-thumbnail {
    position: relative;
    overflow: hidden;
}

.course-thumbnail img {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

.video-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0, 0, 0, 0.7);
    border-radius: 50%;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.video-overlay:hover {
    background: rgba(0, 0, 0, 0.9);
    transform: translate(-50%, -50%) scale(1.1);
}

.course-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--dark-color);
}

.course-description {
    font-size: 1.1rem;
    line-height: 1.6;
}

.stat-item {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin: 0.5rem;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin: 0;
}

.stat-label {
    font-size: 0.8rem;
    color: #6c757d;
    margin: 0;
}

.section-item {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
}

.section-header {
    background: #f8f9fa;
    padding: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.section-header:hover {
    background: #e9ecef;
}

.section-content {
    padding: 1rem;
    background: white;
}

.lesson-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.lesson-item:last-child {
    border-bottom: none;
}

.lesson-title {
    font-weight: 500;
}

.lesson-duration {
    color: #6c757d;
    font-size: 0.9rem;
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

.pricing-info {
    text-align: center;
}

.original-price {
    font-size: 1.1rem;
}

.discount-price {
    font-size: 1.5rem;
}

.discount-badge {
    margin-top: 0.5rem;
}

.status-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.status-label {
    font-weight: 600;
    color: var(--dark-color);
}

.enrollment-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.enrollment-item:last-child {
    border-bottom: none;
}

.enrollment-name {
    font-weight: 500;
    font-size: 0.9rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}

.learning-point, .requirement-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 0.5rem;
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 6px;
}

.learning-point i, .requirement-item i {
    margin-top: 0.2rem;
}
</style>

<script>
function playPreviewVideo() {
    const videoModal = new bootstrap.Modal(document.getElementById('videoModal'));
    videoModal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Stop video when modal is closed
    const videoModal = document.getElementById('videoModal');
    if (videoModal) {
        videoModal.addEventListener('hidden.bs.modal', function () {
            const video = document.getElementById('previewVideo');
            if (video) {
                video.pause();
                video.currentTime = 0;
            }
        });
    }
});
</script>
@endsection
