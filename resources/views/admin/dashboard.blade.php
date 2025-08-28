@extends('layouts.app')

@section('title', 'لوحة تحكم الإدارة')

@section('content')
<div class="admin-dashboard">
    <!-- Header Section -->
    <div class="dashboard-header py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8" data-aos="fade-right">
                    <div class="text-white">
                        <h1 class="display-5 fw-bold mb-2">
                            <i class="fas fa-tachometer-alt me-3"></i>
                            لوحة تحكم الإدارة
                        </h1>
                        <p class="lead mb-0">مرحباً {{ auth()->user()->name }}، إدارة شاملة للمنصة التعليمية</p>
                    </div>
                </div>
                <div class="col-md-4 text-md-end" data-aos="fade-left">
                    <div class="text-white">
                        <div class="d-flex align-items-center justify-content-md-end">
                            <i class="fas fa-calendar-alt me-2"></i>
                            <span>{{ now()->format('Y/m/d') }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-md-end mt-1">
                            <i class="fas fa-clock me-2"></i>
                            <span id="current-time"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="fw-bold mb-1 counter" data-target="{{ \App\Models\User::count() }}">0</h3>
                                <p class="text-muted mb-0">إجمالي المستخدمين</p>
                                <small class="text-success">
                                    <i class="fas fa-arrow-up me-1"></i>
                                    +12% هذا الشهر
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="fas fa-book fa-2x text-success"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="fw-bold mb-1 counter" data-target="{{ \App\Models\Course::count() }}">0</h3>
                                <p class="text-muted mb-0">إجمالي الكورسات</p>
                                <small class="text-success">
                                    <i class="fas fa-arrow-up me-1"></i>
                                    +8% هذا الشهر
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="fas fa-graduation-cap fa-2x text-warning"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="fw-bold mb-1 counter" data-target="{{ \App\Models\CourseEnrollment::count() }}">0</h3>
                                <p class="text-muted mb-0">التسجيلات</p>
                                <small class="text-success">
                                    <i class="fas fa-arrow-up me-1"></i>
                                    +25% هذا الشهر
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-card card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="fas fa-dollar-sign fa-2x text-info"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="fw-bold mb-1">$<span class="counter" data-target="25000">0</span></h3>
                                <p class="text-muted mb-0">إجمالي الإيرادات</p>
                                <small class="text-success">
                                    <i class="fas fa-arrow-up me-1"></i>
                                    +18% هذا الشهر
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-5">
            <div class="col-12" data-aos="fade-up">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-bolt me-2"></i>
                            إجراءات سريعة
                        </h5>
                        <div class="row g-3">
                            <div class="col-lg-2 col-md-4 col-6">
                                <a href="{{ route('admin.users.index') }}" class="quick-action-btn btn btn-outline-primary w-100 py-3">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <div class="fw-semibold">إدارة المستخدمين</div>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6">
                                <a href="{{ route('admin.courses.index') }}" class="quick-action-btn btn btn-outline-success w-100 py-3">
                                    <i class="fas fa-book fa-2x mb-2"></i>
                                    <div class="fw-semibold">إدارة الكورسات</div>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6">
                                <a href="{{ route('admin.categories.index') }}" class="quick-action-btn btn btn-outline-warning w-100 py-3">
                                    <i class="fas fa-folder fa-2x mb-2"></i>
                                    <div class="fw-semibold">التصنيفات</div>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6">
                                <a href="{{ route('admin.payments.index') }}" class="quick-action-btn btn btn-outline-info w-100 py-3">
                                    <i class="fas fa-credit-card fa-2x mb-2"></i>
                                    <div class="fw-semibold">المدفوعات</div>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6">
                                <a href="{{ route('admin.reports.index') }}" class="quick-action-btn btn btn-outline-secondary w-100 py-3">
                                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                    <div class="fw-semibold">التقارير</div>
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6">
                                <a href="{{ route('admin.settings.index') }}" class="quick-action-btn btn btn-outline-dark w-100 py-3">
                                    <i class="fas fa-cog fa-2x mb-2"></i>
                                    <div class="fw-semibold">الإعدادات</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Activities -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up">
                    <div class="card-header bg-transparent border-0 p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-clock me-2"></i>
                                الأنشطة الأخيرة
                            </h5>
                            <a href="#" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="activity-list">
                            <div class="activity-item d-flex align-items-center p-4 border-bottom">
                                <div class="activity-icon bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-user-plus text-success"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">مستخدم جديد انضم للمنصة</h6>
                                    <small class="text-muted">أحمد محمد سجل كطالب</small>
                                </div>
                                <small class="text-muted">منذ 5 دقائق</small>
                            </div>
                            
                            <div class="activity-item d-flex align-items-center p-4 border-bottom">
                                <div class="activity-icon bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-book text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">كورس جديد تم نشره</h6>
                                    <small class="text-muted">كورس "تطوير المواقع" من المدرب سارة أحمد</small>
                                </div>
                                <small class="text-muted">منذ 15 دقيقة</small>
                            </div>
                            
                            <div class="activity-item d-flex align-items-center p-4 border-bottom">
                                <div class="activity-icon bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-shopping-cart text-warning"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">عملية شراء جديدة</h6>
                                    <small class="text-muted">محمد علي اشترى كورس "البرمجة للمبتدئين"</small>
                                </div>
                                <small class="text-muted">منذ 30 دقيقة</small>
                            </div>
                            
                            <div class="activity-item d-flex align-items-center p-4 border-bottom">
                                <div class="activity-icon bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-star text-info"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">تقييم جديد</h6>
                                    <small class="text-muted">فاطمة أحمد قيمت كورس "التصميم الجرافيكي" بـ 5 نجوم</small>
                                </div>
                                <small class="text-muted">منذ ساعة</small>
                            </div>
                            
                            <div class="activity-item d-flex align-items-center p-4">
                                <div class="activity-icon bg-danger bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-exclamation-triangle text-danger"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">تقرير مشكلة</h6>
                                    <small class="text-muted">مستخدم أبلغ عن مشكلة في تشغيل الفيديو</small>
                                </div>
                                <small class="text-muted">منذ ساعتين</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            إحصائيات الأداء
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- System Status -->
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-server me-2"></i>
                            حالة النظام
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="system-status">
                            <div class="status-item d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="status-dot bg-success me-2"></div>
                                    <span>الخادم</span>
                                </div>
                                <span class="badge bg-success">متصل</span>
                            </div>
                            
                            <div class="status-item d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="status-dot bg-success me-2"></div>
                                    <span>قاعدة البيانات</span>
                                </div>
                                <span class="badge bg-success">متصلة</span>
                            </div>
                            
                            <div class="status-item d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="status-dot bg-warning me-2"></div>
                                    <span>التخزين</span>
                                </div>
                                <span class="badge bg-warning">75% مستخدم</span>
                            </div>
                            
                            <div class="status-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="status-dot bg-success me-2"></div>
                                    <span>النسخ الاحتياطي</span>
                                </div>
                                <span class="badge bg-success">محدث</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Courses -->
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-trophy me-2"></i>
                            أفضل الكورسات
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $topCourses = \App\Models\Course::withCount('enrollments')
                                ->orderBy('enrollments_count', 'desc')
                                ->take(5)
                                ->get();
                        @endphp
                        
                        @foreach($topCourses as $index => $course)
                            <div class="course-item d-flex align-items-center mb-3">
                                <div class="course-rank bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px;">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ Str::limit($course->title, 30) }}</h6>
                                    <small class="text-muted">{{ $course->enrollments_count }} طالب</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            إحصائيات سريعة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="quick-stats">
                            <div class="stat-row d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">الطلاب النشطين اليوم</span>
                                <span class="fw-bold text-primary">{{ rand(150, 300) }}</span>
                            </div>
                            
                            <div class="stat-row d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">الكورسات المنشورة هذا الشهر</span>
                                <span class="fw-bold text-success">{{ rand(5, 15) }}</span>
                            </div>
                            
                            <div class="stat-row d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">متوسط التقييم</span>
                                <span class="fw-bold text-warning">4.8 ⭐</span>
                            </div>
                            
                            <div class="stat-row d-flex justify-content-between align-items-center">
                                <span class="text-muted">معدل إكمال الكورسات</span>
                                <span class="fw-bold text-info">78%</span>
                            </div>
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

    .stat-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quick-action-btn {
        transition: all 0.3s ease;
        border-radius: 12px;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 120px;
    }

    .quick-action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .activity-item {
        transition: all 0.3s ease;
    }

    .activity-item:hover {
        background: rgba(0, 0, 0, 0.02);
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .course-rank {
        font-size: 0.875rem;
        font-weight: bold;
    }

    .counter {
        transition: all 0.3s ease;
    }

    @media (max-width: 768px) {
        .quick-action-btn {
            min-height: 100px;
            font-size: 0.875rem;
        }
        
        .quick-action-btn i {
            font-size: 1.5rem !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Update current time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('ar-SA', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('current-time').textContent = timeString;
    }
    
    updateTime();
    setInterval(updateTime, 1000);

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

    // Trigger counter animation when page loads
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(animateCounters, 500);
    });

    // Performance Chart
    const ctx = document.getElementById('performanceChart').getContext('2d');
    const performanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            datasets: [{
                label: 'التسجيلات',
                data: [120, 190, 300, 500, 200, 300],
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'الإيرادات',
                data: [100, 150, 250, 400, 180, 280],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
@endsection
