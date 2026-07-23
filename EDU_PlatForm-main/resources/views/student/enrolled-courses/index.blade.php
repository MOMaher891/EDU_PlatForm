@extends('layouts.app')

@section('title', 'كورساتي المسجلة')

@section('content')
<div class="enrolled-courses-page">
    <!-- Header Section -->
    <div class="page-header py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8" data-aos="fade-right">
                    <div class="text-white">
                        <h1 class="display-5 fw-bold mb-2">
                            كورساتي المسجلة 📚
                        </h1>
                        <p class="lead mb-0">تابع تقدمك في جميع الكورسات المسجلة</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-left">
                    <div class="text-center text-white">
                        <div class="stats-overview rounded-4 p-3 border border-white border-opacity-10" style="background: rgba(255, 255, 255, 0.15) !important; backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                            <div class="row text-center align-items-center">
                                <div class="col-4 border-end border-white border-opacity-10">
                                    <h4 class="fw-bold mb-1 text-white" style="font-size: 1.6rem;">{{ $totalEnrolled }}</h4>
                                    <small style="color: rgba(255, 255, 255, 0.85); font-size: 0.75rem; font-weight: 500; display: block;">إجمالي الكورسات</small>
                                </div>
                                <div class="col-4 border-end border-white border-opacity-10">
                                    <h4 class="fw-bold mb-1" style="color: #4ade80; font-size: 1.6rem;">{{ $completedCourses }}</h4>
                                    <small style="color: rgba(255, 255, 255, 0.85); font-size: 0.75rem; font-weight: 500; display: block;">مكتملة</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="fw-bold mb-1" style="color: #fbbf24; font-size: 1.6rem;">{{ $inProgressCourses }}</h4>
                                    <small style="color: rgba(255, 255, 255, 0.85); font-size: 0.75rem; font-weight: 500; display: block;">قيد التقدم</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="mb-4" data-aos="fade-up">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('student.dashboard') }}" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>
                        لوحة التحكم
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-graduation-cap me-1"></i>
                    كورساتي المسجلة
                </li>
            </ol>
        </nav>

        <!-- Filters and Search Section -->
        <div class="filters-section mb-4" data-aos="fade-up">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('student.enrolled-courses.index') }}" method="GET" class="row g-3">
                        <!-- Search -->
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="ابحث في الكورسات..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">جميع الحالات</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                    مكتملة
                                </option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>
                                    قيد التقدم
                                </option>
                                <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>
                                    لم تبدأ
                                </option>
                            </select>
                        </div>

                        <!-- Category Filter -->
                        <div class="col-md-2">
                            <select name="category" class="form-select">
                                <option value="">جميع التصنيفات</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort -->
                        <div class="col-md-2">
                            <select name="sort" class="form-select">
                                <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>الأحدث</option>
                                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>العنوان</option>
                                <option value="progress" {{ request('sort') == 'progress' ? 'selected' : '' }}>التقدم</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>الأقدم</option>
                            </select>
                        </div>

                        <!-- Actions -->
                        <div class="col-md-2">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>
                                    تطبيق
                                </button>
                                <a href="{{ route('student.enrolled-courses.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo me-1"></i>
                                    إعادة تعيين
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="results-section" data-aos="fade-up" data-aos-delay="100">
            @if($enrollments->count() > 0)
                <div class="row g-4">
                    @foreach($enrollments as $enrollment)
                        <div class="col-lg-6 col-xl-4">
                            <div class="enrolled-course-card h-100 p-4 rounded-4"
                                 style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(6, 182, 212, 0.05)); border: 1px solid rgba(99, 102, 241, 0.1);">

                                <!-- Course Image -->
                                <div class="course-image mb-3">
                                    <img src="{{ $enrollment->course->thumbnail ? asset('storage/' . $enrollment->course->thumbnail) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80' }}"
                                         class="img-fluid rounded-3" style="width: 100%; height: 180px; object-fit: cover;"
                                         alt="{{ $enrollment->course->title }}">

                                    <!-- Progress Badge -->
                                    <div class="progress-badge position-absolute top-0 end-0 m-3">
                                        @if($enrollment->progress >= 100)
                                            <span class="badge bg-success fs-6">
                                                <i class="fas fa-check-circle me-1"></i>
                                                100%
                                            </span>
                                        @elseif($enrollment->progress > 0)
                                            <span class="badge bg-warning fs-6">
                                                {{ $enrollment->progress }}%
                                            </span>
                                        @else
                                            <span class="badge bg-secondary fs-6">
                                                لم تبدأ
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Course Info -->
                                <div class="course-info mb-3">
                                    <h6 class="fw-bold mb-2 text-primary">{{ Str::limit($enrollment->course->title, 50) }}</h6>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $enrollment->course->instructor->name }}
                                    </p>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-layer-group me-1"></i>
                                        {{ $enrollment->course->category->name ?? 'بدون تصنيف' }}
                                    </p>
                                </div>

                                <!-- Progress Section -->
                                <div class="progress-section mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">التقدم العام</small>
                                        <small class="fw-bold text-primary">{{ $enrollment->progress }}%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-gradient" style="width: {{ $enrollment->progress }}%; background: linear-gradient(90deg, #6366f1, #06b6d4);"></div>
                                    </div>
                                </div>

                                <!-- Course Stats -->
                                <div class="course-stats d-flex justify-content-between mb-3">
                                    <div class="stat-item text-center">
                                        <small class="text-muted d-block">المدة</small>
                                        <span class="fw-bold">{{ $enrollment->course->duration_hours ?? 0 }} ساعة</span>
                                    </div>
                                    <div class="stat-item text-center">
                                        <small class="text-muted d-block">الدروس</small>
                                        <span class="fw-bold">{{ $enrollment->course->getTotalLessons() ?? 0 }}</span>
                                    </div>
                                    <div class="stat-item text-center">
                                        <small class="text-muted d-block">آخر نشاط</small>
                                        <span class="fw-bold small">{{ $enrollment->updated_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                <!-- Course Actions -->
                                <div class="course-actions d-grid gap-2">
                                    @if($enrollment->progress >= 100)
                                        <button class="btn btn-success" disabled>
                                            <i class="fas fa-certificate me-1"></i>
                                            مكتمل
                                        </button>
                                    @else
                                        <a href="{{ route('student.courses.learn', $enrollment->course) }}"
                                           class="btn btn-primary">
                                            <i class="fas fa-play me-1"></i>
                                            {{ $enrollment->progress > 0 ? 'متابعة التعلم' : 'ابدأ التعلم' }}
                                        </a>
                                    @endif

                                    <a href="{{ route('student.courses.show', $enrollment->course) }}"
                                       class="btn btn-outline-secondary">
                                        <i class="fas fa-eye me-1"></i>
                                        عرض التفاصيل
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pagination-section mt-5" data-aos="fade-up" data-aos-delay="200">
                    <div class="d-flex justify-content-center">
                        {{ $enrollments->appends(request()->query())->links() }}
                    </div>
                </div>

            @else
                <!-- Empty State -->
                <div class="empty-state text-center py-5" data-aos="fade-up">
                    <i class="fas fa-graduation-cap fa-4x text-muted mb-4"></i>
                    <h4 class="fw-bold mb-3">لا توجد كورسات مسجلة</h4>
                    <p class="text-muted mb-4">لم تسجل في أي كورس بعد. ابدأ رحلة التعلم واكتشف الكورسات المتاحة</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('student.courses.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i>
                            تصفح الكورسات
                        </a>
                        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>
                            العودة للوحة التحكم
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .page-header {
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .stats-overview {
        backdrop-filter: blur(10px);
    }

    .enrolled-course-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .enrolled-course-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .enrolled-course-card:hover::before {
        left: 100%;
    }

    .enrolled-course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(99, 102, 241, 0.15);
        border-color: rgba(99, 102, 241, 0.3);
    }

    .course-image {
        position: relative;
    }

    .progress-badge {
        z-index: 10;
    }

    .progress-section .progress {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .progress-section .progress-bar {
        border-radius: 10px;
        position: relative;
        overflow: hidden;
    }

    .progress-section .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    .course-stats .stat-item {
        transition: all 0.3s ease;
        padding: 8px;
        border-radius: 8px;
    }

    .course-stats .stat-item:hover {
        background: rgba(99, 102, 241, 0.1);
        transform: translateY(-2px);
    }

    .course-actions .btn {
        transition: all 0.3s ease;
        border-radius: 8px;
        font-weight: 500;
    }

    .course-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .filters-section .form-control,
    .filters-section .form-select {
        border-radius: 8px;
        border: 1px solid rgba(99, 102, 241, 0.2);
        transition: all 0.3s ease;
    }

    .filters-section .form-control:focus,
    .filters-section .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
    }

    .empty-state {
        background: rgba(99, 102, 241, 0.02);
        border-radius: 16px;
        border: 2px dashed rgba(99, 102, 241, 0.2);
    }

    .pagination-section .pagination {
        gap: 5px;
    }

    .pagination-section .page-link {
        border-radius: 8px;
        border: 1px solid rgba(99, 102, 241, 0.2);
        color: #6366f1;
        transition: all 0.3s ease;
    }

    .pagination-section .page-link:hover {
        background-color: #6366f1;
        border-color: #6366f1;
        color: white;
    }

    .pagination-section .page-item.active .page-link {
        background-color: #6366f1;
        border-color: #6366f1;
    }

    /* Breadcrumb Styles */
    .breadcrumb {
        background: rgba(99, 102, 241, 0.05);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        border: 1px solid rgba(99, 102, 241, 0.1);
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: #6366f1;
        font-weight: bold;
        font-size: 1.2em;
    }

    .breadcrumb-item a {
        color: #6366f1;
        transition: all 0.3s ease;
    }

    .breadcrumb-item a:hover {
        color: #4f46e5;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #6b7280;
    }
</style>
@endpush
@endsection
