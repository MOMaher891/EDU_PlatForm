@extends('layouts.app')

@section('title', 'لوحة تحكم الطالب')

@section('content')
<div class="student-dashboard">
    <!-- Header Section -->
    <div class="dashboard-header py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8" data-aos="fade-right">
                    <div class="text-white">
                        <h1 class="display-5 fw-bold mb-2">
                            مرحباً، {{ auth()->user()->name }}! 👋
                        </h1>
                        <p class="lead mb-0">استمر في رحلة التعلم وحقق أهدافك التعليمية</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-left">
                    <div class="text-center text-white">
                        <div class="learning-streak rounded-4 p-3 border border-white border-opacity-10" style="background: rgba(255, 255, 255, 0.15) !important; backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                            <h3 class="fw-bold mb-1 text-white" style="font-size: 1.8rem;">🔥 {{ rand(5, 30) }}</h3>
                            <small style="color: rgba(255, 255, 255, 0.85); font-size: 0.8rem; font-weight: 500; display: block;">يوم متتالي في التعلم</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <!-- Progress Overview -->
        <div class="row g-4 mb-5">
            @php
                $enrolledCourses = $enrolledCourses ?? auth()->user()->enrollments()->with('course')->get();
                $completedCourses = $completedCourses ?? $enrolledCourses->where('progress', 100);
                $inProgressCourses = $inProgressCourses ?? $enrolledCourses->where('progress', '>', 0)->where('progress', '<', 100);
                $totalHours = $totalHours ?? $enrolledCourses->sum(function($enrollment) {
                    return $enrollment->course->duration_hours ?? 0;
                });
            @endphp

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="stat-icon bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-book fa-2x text-primary"></i>
                        </div>
                        <h3 class="fw-bold mb-1 counter" data-target="{{ $enrolledCourses->count() }}">0</h3>
                        <p class="text-muted mb-0">الكورسات المسجلة</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="stat-icon bg-success bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                        <h3 class="fw-bold mb-1 counter" data-target="{{ $completedCourses->count() }}">0</h3>
                        <p class="text-muted mb-0">كورسات مكتملة</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="stat-icon bg-warning bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                        <h3 class="fw-bold mb-1 counter" data-target="{{ $totalHours }}">0</h3>
                        <p class="text-muted mb-0">ساعات التعلم</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-card card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="stat-icon bg-info bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-certificate fa-2x text-info"></i>
                        </div>
                        <h3 class="fw-bold mb-1 counter" data-target="{{ $completedCourses->count() }}">0</h3>
                        <p class="text-muted mb-0">شهادات حاصل عليها</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Continue Learning -->
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up">
                    <div class="card-header bg-transparent border-0 p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-play-circle me-2"></i>
                                تابع التعلم
                            </h5>
                            <a href="{{ route('student.courses.index') }}" class="btn btn-sm btn-outline-primary">
                                تصفح المزيد
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @forelse($inProgressCourses->take(3) as $enrollment)
                            <div class="course-progress-item d-flex align-items-center mb-4 p-3 rounded-3" style="background: rgba(99, 102, 241, 0.05);">
                                <img src="{{ $enrollment->course->thumbnail ? asset('storage/' . $enrollment->course->thumbnail) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80' }}"
                                     class="rounded-3 me-3" style="width: 80px; height: 60px; object-fit: cover;"
                                     alt="{{ $enrollment->course->title }}">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1">{{ $enrollment->course->title }}</h6>
                                    <small class="text-muted d-block mb-2">{{ $enrollment->course->instructor->name }}</small>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-gradient" style="width: {{ $enrollment->progress }}%; background: linear-gradient(90deg, #6366f1, #06b6d4);"></div>
                                    </div>
                                    <small class="text-muted">{{ $enrollment->progress }}% مكتمل</small>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('student.courses.learn', $enrollment->course) }}"
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-play me-1"></i>
                                        متابعة
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                                <h6 class="fw-bold mb-2">لا توجد كورسات قيد التقدم</h6>
                                <p class="text-muted mb-3">ابدأ رحلة التعلم واكتشف الكورسات المتاحة</p>
                                <a href="{{ route('student.courses.index') }}" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>
                                    تصفح الكورسات
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- My Enrolled Courses -->
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="100" id="enrolled-courses">
                    <div class="card-header bg-transparent border-0 p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-graduation-cap me-2"></i>
                                كورساتي المسجلة
                            </h5>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-primary fs-6">{{ $enrolledCourses->count() }} كورس</span>
                                @if($enrolledCourses->count() > 0)
                                    <a href="{{ route('student.enrolled-courses.index') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-1"></i>
                                        عرض الكل
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if($enrolledCourses->count() > 0)
                            <div class="enrolled-courses-grid">
                                @foreach($enrolledCourses as $enrollment)
                                    <div class="enrolled-course-card p-4 rounded-4 mb-4" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(6, 182, 212, 0.05)); border: 1px solid rgba(99, 102, 241, 0.1);">
                                        <div class="row align-items-center">
                                            <div class="col-md-3">
                                                <img src="{{ $enrollment->course->thumbnail ? asset('storage/' . $enrollment->course->thumbnail) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80' }}"
                                                     class="img-fluid rounded-3" style="width: 100%; height: 120px; object-fit: cover;"
                                                     alt="{{ $enrollment->course->title }}">
                                            </div>
                                            <div class="col-md-6">
                                                <div class="course-info">
                                                    <h6 class="fw-bold mb-2 text-primary">{{ $enrollment->course->title }}</h6>
                                                    <p class="text-muted mb-2">
                                                        <i class="fas fa-user me-1"></i>
                                                        {{ $enrollment->course->instructor->name }}
                                                    </p>
                                                    <p class="text-muted mb-2">
                                                        <i class="fas fa-layer-group me-1"></i>
                                                        {{ $enrollment->course->category->name ?? 'بدون تصنيف' }}
                                                    </p>

                                                    <!-- Progress Section -->
                                                    <div class="progress-section mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <small class="text-muted">التقدم العام</small>
                                                            <small class="fw-bold text-primary">{{ $enrollment->progress }}%</small>
                                                        </div>
                                                        <div class="progress" style="height: 10px;">
                                                            <div class="progress-bar bg-gradient" style="width: {{ $enrollment->progress }}%; background: linear-gradient(90deg, #6366f1, #06b6d4);"></div>
                                                        </div>
                                                    </div>

                                                    <!-- Course Stats -->
                                                    <div class="course-stats d-flex gap-3">
                                                        <div class="stat-item text-center">
                                                            <small class="text-muted d-block">المدة</small>
                                                            <span class="fw-bold">{{ $enrollment->course->duration_hours ?? 0 }} ساعة</span>
                                                        </div>
                                                        <div class="stat-item text-center">
                                                            <small class="text-muted d-block">الدروس</small>
                                                            <span class="fw-bold">{{ $enrollment->course->getTotalLessons() ?? 0 }}</span>
                                                        </div>
                                                        <div class="stat-item text-center">
                                                            <small class="text-muted d-block">الحالة</small>
                                                            @if($enrollment->progress >= 100)
                                                                <span class="badge bg-success">مكتمل</span>
                                                            @elseif($enrollment->progress > 0)
                                                                <span class="badge bg-warning">قيد التقدم</span>
                                                            @else
                                                                <span class="badge bg-secondary">لم يبدأ</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <div class="course-actions d-grid gap-2">
                                                    @if($enrollment->progress >= 100)
                                                        <button class="btn btn-success btn-sm" disabled>
                                                            <i class="fas fa-certificate me-1"></i>
                                                            مكتمل
                                                        </button>
                                                    @else
                                                        <a href="{{ route('student.courses.learn', $enrollment->course) }}"
                                                           class="btn btn-primary btn-sm">
                                                            <i class="fas fa-play me-1"></i>
                                                            {{ $enrollment->progress > 0 ? 'متابعة' : 'ابدأ التعلم' }}
                                                        </a>
                                                    @endif

                                                    <a href="{{ route('student.courses.show', $enrollment->course) }}"
                                                       class="btn btn-outline-secondary btn-sm">
                                                        <i class="fas fa-eye me-1"></i>
                                                        عرض التفاصيل
                                                    </a>

                                                    @if($enrollment->progress > 0)
                                                        <div class="mt-2">
                                                            <small class="text-muted">
                                                                آخر نشاط: {{ $enrollment->updated_at->diffForHumans() }}
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                <h6 class="fw-bold mb-2">لم تسجل في أي كورس بعد</h6>
                                <p class="text-muted mb-3">ابدأ رحلة التعلم واكتشف الكورسات المتاحة</p>
                                <a href="{{ route('student.courses.index') }}" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>
                                    تصفح الكورسات
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Achievements -->
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-trophy me-2"></i>
                            الإنجازات الأخيرة
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="achievements-grid row g-3">
                            <div class="col-md-6">
                                <div class="achievement-item d-flex align-items-center p-3 rounded-3" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 193, 7, 0.05));">
                                    <div class="achievement-icon bg-warning bg-opacity-20 rounded-circle p-2 me-3">
                                        <i class="fas fa-medal text-warning fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">أول كورس مكتمل</h6>
                                        <small class="text-muted">أكملت أول كورس بنجاح</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="achievement-item d-flex align-items-center p-3 rounded-3" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));">
                                    <div class="achievement-icon bg-success bg-opacity-20 rounded-circle p-2 me-3">
                                        <i class="fas fa-fire text-success fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">متعلم نشط</h6>
                                        <small class="text-muted">7 أيام متتالية في التعلم</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="achievement-item d-flex align-items-center p-3 rounded-3" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(99, 102, 241, 0.05));">
                                    <div class="achievement-icon bg-primary bg-opacity-20 rounded-circle p-2 me-3">
                                        <i class="fas fa-star text-primary fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">مقيم ممتاز</h6>
                                        <small class="text-muted">قيمت 5 كورسات</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="achievement-item d-flex align-items-center p-3 rounded-3" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));">
                                    <div class="achievement-icon bg-danger bg-opacity-20 rounded-circle p-2 me-3">
                                        <i class="fas fa-heart text-danger fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">محب للتعلم</h6>
                                        <small class="text-muted">أضفت 10 كورسات للمفضلة</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Learning Progress Chart -->
                <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            تقدم التعلم الأسبوعي
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="learningChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Learning Goals -->
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-target me-2"></i>
                            أهداف التعلم
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="goal-item mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">إكمال 3 كورسات هذا الشهر</span>
                                <span class="text-primary fw-bold">2/3</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-primary" style="width: 66%"></div>
                            </div>
                            <small class="text-muted">باقي كورس واحد لتحقيق الهدف</small>
                        </div>

                        <div class="goal-item mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">20 ساعة تعلم أسبوعياً</span>
                                <span class="text-success fw-bold">15/20</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: 75%"></div>
                            </div>
                            <small class="text-muted">5 ساعات متبقية هذا الأسبوع</small>
                        </div>

                        <div class="goal-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">الحصول على 5 شهادات</span>
                                <span class="text-warning fw-bold">{{ $completedCourses->count() }}/5</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: {{ ($completedCourses->count() / 5) * 100 }}%"></div>
                            </div>
                            <small class="text-muted">{{ 5 - $completedCourses->count() }} شهادات متبقية</small>
                        </div>
                    </div>
                </div>

                <!-- Recommended Courses -->
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            كورسات مقترحة لك
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @php
                            $recommendedCourses = \App\Models\Course::where('is_published', true)
                                ->whereNotIn('id', $enrolledCourses->pluck('course_id'))
                                ->inRandomOrder()
                                ->take(3)
                                ->get();
                        @endphp

                        @foreach($recommendedCourses as $course)
                            <div class="recommended-course mb-3 p-3 rounded-3" style="background: rgba(0, 0, 0, 0.02);">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=60&q=80' }}"
                                         class="rounded me-3" style="width: 50px; height: 40px; object-fit: cover;"
                                         alt="{{ $course->title }}">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 small">{{ Str::limit($course->title, 40) }}</h6>
                                        <small class="text-muted">{{ $course->instructor->name }}</small>
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <small class="text-primary fw-bold">${{ $course->getEffectivePrice() }}</small>
                                            <a href="{{ route('student.courses.show', $course) }}" class="btn btn-sm btn-outline-primary">
                                                عرض
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-bolt me-2"></i>
                            إجراءات سريعة
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-grid gap-2">
                            <a href="{{ route('student.courses.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-search me-2"></i>
                                تصفح الكورسات
                            </a>
                            <a href="{{ route('student.enrolled-courses.index') }}" class="btn btn-outline-success">
                                <i class="fas fa-graduation-cap me-2"></i>
                                كورساتي المسجلة
                            </a>
                            <button class="btn btn-outline-secondary">
                                <i class="fas fa-heart me-2"></i>
                                المفضلة
                            </button>
                            <button class="btn btn-outline-info">
                                <i class="fas fa-certificate me-2"></i>
                                شهاداتي
                            </button>
                            <button class="btn btn-outline-success">
                                <i class="fas fa-user-edit me-2"></i>
                                تحديث الملف الشخصي
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .dashboard-header {
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .stat-card {
        transition: all 0.3s ease;
        border-radius: 16px;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
    }

    .course-progress-item {
        transition: all 0.3s ease;
        border: 1px solid rgba(99, 102, 241, 0.1);
    }

    .course-progress-item:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(99, 102, 241, 0.1);
    }

    .achievement-item {
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .achievement-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .goal-item {
        padding: 1rem;
        border-radius: 12px;
        background: rgba(0, 0, 0, 0.02);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .goal-item:hover {
        background: rgba(99, 102, 241, 0.05);
        border-color: rgba(99, 102, 241, 0.1);
    }

    .recommended-course {
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .recommended-course:hover {
        background: rgba(99, 102, 241, 0.05) !important;
        border-color: rgba(99, 102, 241, 0.1);
    }

    .learning-streak {
        backdrop-filter: blur(10px);
    }

    .counter {
        transition: all 0.3s ease;
    }

    /* Enrolled Courses Styles */
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

    .enrolled-course-card .course-info h6 {
        transition: color 0.3s ease;
    }

    .enrolled-course-card:hover .course-info h6 {
        color: #6366f1 !important;
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

    .course-actions .btn {
        transition: all 0.3s ease;
        border-radius: 8px;
        font-weight: 500;
    }

    .course-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .enrolled-courses-grid {
        max-height: 600px;
        overflow-y: auto;
        padding-right: 10px;
    }

    .enrolled-courses-grid::-webkit-scrollbar {
        width: 6px;
    }

    .enrolled-courses-grid::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
    }

    .enrolled-courses-grid::-webkit-scrollbar-thumb {
        background: rgba(99, 102, 241, 0.3);
        border-radius: 10px;
    }

    .enrolled-courses-grid::-webkit-scrollbar-thumb:hover {
        background: rgba(99, 102, 241, 0.5);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Counter Animation
    function animateCounters() {
        const counters = document.querySelectorAll('.counter');

        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const increment = target / 50;
            let current = 0;

            const updateCounter = () => {
                if (current < target) {
                    current += increment;
                    counter.textContent = Math.ceil(current);
                    setTimeout(updateCounter, 30);
                } else {
                    counter.textContent = target;
                }
            };

            updateCounter();
        });
    }

    // Trigger counter animation when page loads
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(animateCounters, 500);
    });

    // Learning Progress Chart
    const ctx = document.getElementById('learningChart').getContext('2d');
    const learningChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
            datasets: [{
                label: 'ساعات التعلم',
                data: [2, 3, 1, 4, 2, 5, 3],
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#6366f1',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 6,
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    hoverRadius: 8
                }
            }
        }
    });

    // Smooth scrolling to enrolled courses section
    function scrollToEnrolledCourses() {
        const enrolledCoursesSection = document.getElementById('enrolled-courses');
        if (enrolledCoursesSection) {
            enrolledCoursesSection.scrollIntoView({ behavior: 'smooth' });
        }
    }
</script>
@endpush
@endsection
