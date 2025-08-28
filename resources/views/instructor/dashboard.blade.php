@extends('layouts.app')

@section('title', 'لوحة تحكم المدرب')

@section('content')
<div class="instructor-dashboard">
    <!-- Header Section -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="dashboard-title">
                        <i class="fas fa-chalkboard-teacher me-3"></i>
                        مرحباً، {{ Auth::user()->name }}
                    </h1>
                    <p class="dashboard-subtitle">إليك نظرة عامة على أداء كورساتك</p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="quick-actions">
                        <a href="{{ route('instructor.courses.create') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>
                            إنشاء كورس جديد
                        </a>
                        <button class="btn btn-outline-secondary btn-lg ms-2" data-bs-toggle="modal" data-bs-target="#analyticsModal">
                            <i class="fas fa-chart-line me-2"></i>
                            التحليلات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-primary" data-aos="fade-up" data-aos-delay="100">
                    <div class="stats-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number" data-target="{{ $totalCourses }}">0</h3>
                        <p class="stats-label">إجمالي الكورسات</p>
                        <div class="stats-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+{{ $monthlyStats['courses'] }} هذا الشهر</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-success" data-aos="fade-up" data-aos-delay="200">
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number" data-target="{{ $totalStudents }}">0</h3>
                        <p class="stats-label">إجمالي الطلاب</p>
                        <div class="stats-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+{{ $monthlyStats['students'] }} هذا الشهر</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-warning" data-aos="fade-up" data-aos-delay="300">
                    <div class="stats-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number" data-target="{{ $totalRevenue }}">0</h3>
                        <p class="stats-label">إجمالي الأرباح ($)</p>
                        <div class="stats-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+${{ $monthlyStats['revenue'] }} هذا الشهر</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-info" data-aos="fade-up" data-aos-delay="400">
                    <div class="stats-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number" data-target="{{ number_format($averageRating, 1) }}">0</h3>
                        <p class="stats-label">متوسط التقييم</p>
                        <div class="rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $averageRating ? 'text-warning' : 'text-muted' }}"></i>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Courses -->
            <div class="col-xl-8 mb-4">
                <div class="dashboard-card" data-aos="fade-up">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-book me-2"></i>
                            الكورسات الحديثة
                        </h5>
                        <a href="{{ route('instructor.courses.index') }}" class="btn btn-sm btn-outline-primary">
                            عرض الكل
                        </a>
                    </div>
                    <div class="card-body">
                        @if($recentCourses->count() > 0)
                            <div class="courses-list">
                                @foreach($recentCourses as $course)
                                    <div class="course-item">
                                        <div class="course-thumbnail">
                                            <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80' }}" 
                                                 alt="{{ $course->title }}">
                                            <div class="course-status {{ $course->is_published ? 'published' : 'draft' }}">
                                                {{ $course->is_published ? 'منشور' : 'مسودة' }}
                                            </div>
                                        </div>
                                        <div class="course-info">
                                            <h6 class="course-title">{{ $course->title }}</h6>
                                            <p class="course-meta">
                                                <span class="students-count">
                                                    <i class="fas fa-users me-1"></i>
                                                    {{ $course->enrollments_count }} طالب
                                                </span>
                                                <span class="course-price">
                                                    <i class="fas fa-dollar-sign me-1"></i>
                                                    ${{ $course->getEffectivePrice() }}
                                                </span>
                                            </p>
                                            <div class="course-progress">
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: {{ rand(60, 100) }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ rand(60, 100) }}% مكتمل</small>
                                            </div>
                                        </div>
                                        <div class="course-actions">
                                            <a href="{{ route('instructor.courses.show', $course) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('instructor.courses.edit', $course) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                                <h6>لا توجد كورسات بعد</h6>
                                <p class="text-muted">ابدأ بإنشاء كورسك الأول</p>
                                <a href="{{ route('instructor.courses.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>
                                    إنشاء كورس جديد
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Students -->
            <div class="col-xl-4 mb-4">
                <div class="dashboard-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-user-graduate me-2"></i>
                            الطلاب الجدد
                        </h5>
                        <a href="{{ route('instructor.students.index') }}" class="btn btn-sm btn-outline-primary">
                            عرض الكل
                        </a>
                    </div>
                    <div class="card-body">
                        @if($recentStudents->count() > 0)
                            <div class="students-list">
                                @foreach($recentStudents->take(5) as $student)
                                    <div class="student-item">
                                        <div class="student-avatar">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=6366f1&color=fff&size=40" 
                                                 alt="{{ $student->name }}">
                                        </div>
                                        <div class="student-info">
                                            <h6 class="student-name">{{ $student->name }}</h6>
                                            <p class="student-courses">
                                                {{ $student->enrollments->count() }} كورس
                                            </p>
                                        </div>
                                        <div class="student-date">
                                            <small class="text-muted">
                                                {{ $student->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-user-graduate fa-2x text-muted mb-2"></i>
                                <p class="text-muted">لا يوجد طلاب جدد</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Revenue Chart -->
            <div class="col-xl-8 mb-4">
                <div class="dashboard-card" data-aos="fade-up">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-chart-line me-2"></i>
                            إحصائيات الأرباح
                        </h5>
                        <div class="chart-filters">
                            <button class="btn btn-sm btn-outline-primary active" data-period="week">أسبوع</button>
                            <button class="btn btn-sm btn-outline-primary" data-period="month">شهر</button>
                            <button class="btn btn-sm btn-outline-primary" data-period="year">سنة</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Course Performance -->
            <div class="col-xl-4 mb-4">
                <div class="dashboard-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-trophy me-2"></i>
                            أفضل الكورسات
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="top-courses">
                            @foreach($recentCourses->sortByDesc('enrollments_count')->take(5) as $index => $course)
                                <div class="top-course-item">
                                    <div class="course-rank">
                                        <span class="rank-number">{{ $index + 1 }}</span>
                                    </div>
                                    <div class="course-details">
                                        <h6 class="course-name">{{ Str::limit($course->title, 30) }}</h6>
                                        <div class="course-stats">
                                            <span class="students">{{ $course->enrollments_count }} طالب</span>
                                            <span class="revenue">${{ $course->getEffectivePrice() * $course->enrollments_count }}</span>
                                        </div>
                                    </div>
                                    <div class="course-trend">
                                        <i class="fas fa-arrow-up text-success"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Goals Section -->
        <div class="row">
            <div class="col-12">
                <div class="dashboard-card" data-aos="fade-up">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-bullseye me-2"></i>
                            الأهداف الشهرية
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="goals-grid">
                            <div class="goal-item">
                                <div class="goal-icon">
                                    <i class="fas fa-book text-primary"></i>
                                </div>
                                <div class="goal-content">
                                    <h6>إنشاء كورسات جديدة</h6>
                                    <div class="goal-progress">
                                        <div class="progress">
                                            <div class="progress-bar bg-primary" style="width: {{ ($monthlyStats['courses'] / 5) * 100 }}%"></div>
                                        </div>
                                        <span class="goal-text">{{ $monthlyStats['courses'] }}/5 كورسات</span>
                                    </div>
                                </div>
                            </div>

                            <div class="goal-item">
                                <div class="goal-icon">
                                    <i class="fas fa-users text-success"></i>
                                </div>
                                <div class="goal-content">
                                    <h6>جذب طلاب جدد</h6>
                                    <div class="goal-progress">
                                        <div class="progress">
                                            <div class="progress-bar bg-success" style="width: {{ ($monthlyStats['students'] / 100) * 100 }}%"></div>
                                        </div>
                                        <span class="goal-text">{{ $monthlyStats['students'] }}/100 طالب</span>
                                    </div>
                                </div>
                            </div>

                            <div class="goal-item">
                                <div class="goal-icon">
                                    <i class="fas fa-dollar-sign text-warning"></i>
                                </div>
                                <div class="goal-content">
                                    <h6>تحقيق الأرباح</h6>
                                    <div class="goal-progress">
                                        <div class="progress">
                                            <div class="progress-bar bg-warning" style="width: {{ ($monthlyStats['revenue'] / 1000) * 100 }}%"></div>
                                        </div>
                                        <span class="goal-text">${{ $monthlyStats['revenue'] }}/$1000</span>
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

<!-- Analytics Modal -->
<div class="modal fade" id="analyticsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">التحليلات التفصيلية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="studentsChart"></canvas>
                    </div>
                    <div class="col-md-6">
                        <canvas id="coursesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.instructor-dashboard {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding-bottom: 50px;
}

.dashboard-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 30px 0;
    margin-bottom: 30px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.dashboard-title {
    color: white;
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.dashboard-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    margin: 0;
}

.quick-actions .btn {
    border-radius: 12px;
    font-weight: 600;
    padding: 12px 24px;
    transition: all 0.3s ease;
}

.quick-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.stats-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.stats-primary::before { background: linear-gradient(90deg, #6366f1, #8b5cf6); }
.stats-success::before { background: linear-gradient(90deg, #10b981, #34d399); }
.stats-warning::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.stats-info::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 20px;
}

.stats-primary .stats-icon { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
.stats-success .stats-icon { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.stats-warning .stats-icon { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.stats-info .stats-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }

.stats-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 5px 0;
}

.stats-label {
    color: #6b7280;
    font-weight: 500;
    margin: 0 0 10px 0;
}

.stats-change {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.9rem;
    font-weight: 600;
}

.stats-change.positive { color: #10b981; }
.stats-change.negative { color: #ef4444; }

.dashboard-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.card-header {
    padding: 25px 30px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.card-body {
    padding: 30px;
}

.courses-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.course-item {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 20px;
    border: 1px solid #f3f4f6;
    border-radius: 15px;
    transition: all 0.3s ease;
}

.course-item:hover {
    border-color: #6366f1;
    background: rgba(99, 102, 241, 0.02);
    transform: translateX(5px);
}

.course-thumbnail {
    position: relative;
    width: 80px;
    height: 60px;
    border-radius: 10px;
    overflow: hidden;
}

.course-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.course-status {
    position: absolute;
    top: 5px;
    right: 5px;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
}

.course-status.published {
    background: #10b981;
    color: white;
}

.course-status.draft {
    background: #f59e0b;
    color: white;
}

.course-info {
    flex: 1;
}

.course-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 8px 0;
}

.course-meta {
    display: flex;
    gap: 20px;
    margin: 0 0 10px 0;
    color: #6b7280;
    font-size: 0.9rem;
}

.course-progress .progress {
    height: 6px;
    background: #f3f4f6;
    border-radius: 3px;
    margin-bottom: 5px;
}

.course-progress .progress-bar {
    background: linear-gradient(90deg, #6366f1, #8b5cf6);
    border-radius: 3px;
}

.course-actions {
    display: flex;
    gap: 10px;
}

.course-actions .btn {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
}

.students-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.student-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.student-item:hover {
    background: rgba(99, 102, 241, 0.05);
}

.student-avatar img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.student-info {
    flex: 1;
}

.student-name {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 4px 0;
}

.student-courses {
    color: #6b7280;
    font-size: 0.9rem;
    margin: 0;
}

.chart-filters {
    display: flex;
    gap: 5px;
}

.chart-filters .btn.active {
    background: #6366f1;
    color: white;
    border-color: #6366f1;
}

.top-courses {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.top-course-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border-radius: 12px;
    background: rgba(99, 102, 241, 0.05);
    transition: all 0.3s ease;
}

.top-course-item:hover {
    background: rgba(99, 102, 241, 0.1);
    transform: translateX(5px);
}

.course-rank {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #6366f1;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
}

.course-details {
    flex: 1;
}

.course-name {
    font-size: 0.95rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 5px 0;
}

.course-stats {
    display: flex;
    gap: 15px;
    font-size: 0.8rem;
    color: #6b7280;
}

.goals-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.goal-item {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 25px;
    border-radius: 15px;
    background: rgba(99, 102, 241, 0.05);
    transition: all 0.3s ease;
}

.goal-item:hover {
    background: rgba(99, 102, 241, 0.1);
    transform: translateY(-2px);
}

.goal-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.goal-content {
    flex: 1;
}

.goal-content h6 {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 10px 0;
}

.goal-progress {
    display: flex;
    align-items: center;
    gap: 15px;
}

.goal-progress .progress {
    flex: 1;
    height: 8px;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 4px;
}

.goal-text {
    font-size: 0.9rem;
    font-weight: 600;
    color: #374151;
    white-space: nowrap;
}

.rating-stars {
    margin-top: 5px;
}

@media (max-width: 768px) {
    .dashboard-title {
        font-size: 2rem;
    }
    
    .quick-actions {
        margin-top: 20px;
    }
    
    .quick-actions .btn {
        display: block;
        width: 100%;
        margin-bottom: 10px;
    }
    
    .stats-card {
        margin-bottom: 20px;
    }
    
    .course-item {
        flex-direction: column;
        text-align: center;
    }
    
    .goals-grid {
        grid-template-columns: 1fr;
    }
    
    .goal-item {
        flex-direction: column;
        text-align: center;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Animate numbers
function animateNumbers() {
    const numbers = document.querySelectorAll('.stats-number');
    numbers.forEach(number => {
        const target = parseFloat(number.dataset.target);
        const increment = target / 100;
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            number.textContent = Math.floor(current);
        }, 20);
    });
}

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
        datasets: [{
            label: 'الأرباح ($)',
            data: [1200, 1900, 3000, 5000, 2000, 3000],
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
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
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Students Chart (for modal)
const studentsCtx = document.getElementById('studentsChart').getContext('2d');
const studentsChart = new Chart(studentsCtx, {
    type: 'doughnut',
    data: {
        labels: ['طلاب جدد', 'طلاب نشطون', 'طلاب غير نشطين'],
        datasets: [{
            data: [30, 50, 20],
            backgroundColor: ['#10b981', '#6366f1', '#f59e0b'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Courses Chart (for modal)
const coursesCtx = document.getElementById('coursesChart').getContext('2d');
const coursesChart = new Chart(coursesCtx, {
    type: 'bar',
    data: {
        labels: ['منشور', 'مسودة', 'قيد المراجعة'],
        datasets: [{
            label: 'عدد الكورسات',
            data: [12, 5, 3],
            backgroundColor: ['#10b981', '#f59e0b', '#6366f1'],
            borderRadius: 8,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Chart period filters
document.querySelectorAll('[data-period]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // Update chart data based on period
        const period = this.dataset.period;
        updateRevenueChart(period);
    });
});

function updateRevenueChart(period) {
    let labels, data;
    
    switch(period) {
        case 'week':
            labels = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
            data = [200, 300, 250, 400, 350, 500, 450];
            break;
        case 'month':
            labels = ['الأسبوع 1', 'الأسبوع 2', 'الأسبوع 3', 'الأسبوع 4'];
            data = [1200, 1500, 1800, 2000];
            break;
        case 'year':
            labels = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
            data = [1200, 1900, 3000, 5000, 2000, 3000, 4000, 3500, 4500, 5500, 6000, 7000];
            break;
    }
    
    revenueChart.data.labels = labels;
    revenueChart.data.datasets[0].data = data;
    revenueChart.update();
}

// Initialize animations when page loads
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(animateNumbers, 500);
});

// AOS Animation
AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true
});
</script>
@endpush
@endsection
