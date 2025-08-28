@extends('layouts.app')

@section('title', 'التقارير والإحصائيات')

@section('content')
<div class="admin-reports-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="page-title">
                        <i class="fas fa-chart-bar me-3"></i>
                        التقارير والإحصائيات
                    </h1>
                    <p class="page-subtitle">نظرة شاملة على إحصائيات المنصة وأدائها</p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exportReportModal">
                            <i class="fas fa-download me-2"></i>
                            تصدير التقرير
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>
                            طباعة التقرير
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Overall Statistics -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-primary" data-aos="fade-up">
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ number_format($stats['total_users']) }}</h3>
                        <p class="stats-label">إجمالي المستخدمين</p>
                        <div class="stats-trend">
                            <span class="trend-up">
                                <i class="fas fa-arrow-up me-1"></i>
                                +{{ $stats['monthly_users'] }} هذا الشهر
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-success" data-aos="fade-up" data-aos-delay="100">
                    <div class="stats-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ number_format($stats['total_courses']) }}</h3>
                        <p class="stats-label">إجمالي الكورسات</p>
                        <div class="stats-trend">
                            <span class="trend-up">
                                <i class="fas fa-arrow-up me-1"></i>
                                +{{ $stats['monthly_courses'] }} هذا الشهر
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-warning" data-aos="fade-up" data-aos-delay="200">
                    <div class="stats-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ number_format($stats['total_enrollments']) }}</h3>
                        <p class="stats-label">إجمالي التسجيلات</p>
                        <div class="stats-trend">
                            <span class="trend-up">
                                <i class="fas fa-arrow-up me-1"></i>
                                +{{ $stats['monthly_enrollments'] }} هذا الشهر
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-info" data-aos="fade-up" data-aos-delay="300">
                    <div class="stats-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">${{ number_format($stats['total_revenue'], 2) }}</h3>
                        <p class="stats-label">إجمالي الإيرادات</p>
                        <div class="stats-trend">
                            <span class="trend-up">
                                <i class="fas fa-arrow-up me-1"></i>
                                +${{ number_format($stats['monthly_revenue'], 2) }} هذا الشهر
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <!-- Revenue Chart -->
            <div class="col-lg-8 mb-4">
                <div class="chart-card" data-aos="fade-up">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-chart-line me-2"></i>
                                الإيرادات الشهرية
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <canvas id="revenueChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Growth Chart -->
            <div class="col-lg-4 mb-4">
                <div class="chart-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-chart-pie me-2"></i>
                                توزيع المستخدمين
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <canvas id="userDistributionChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Statistics -->
        <div class="row mb-4">
            <!-- Monthly Statistics -->
            <div class="col-lg-6 mb-4">
                <div class="detailed-stats-card" data-aos="fade-up">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>
                                إحصائيات هذا الشهر
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="stat-item">
                                        <div class="stat-icon bg-primary bg-opacity-10 rounded-circle p-3">
                                            <i class="fas fa-user-plus text-primary"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h4 class="stat-number">{{ $stats['monthly_users'] }}</h4>
                                            <p class="stat-label">مستخدمين جدد</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item">
                                        <div class="stat-icon bg-success bg-opacity-10 rounded-circle p-3">
                                            <i class="fas fa-book text-success"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h4 class="stat-number">{{ $stats['monthly_courses'] }}</h4>
                                            <p class="stat-label">كورسات جديدة</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item">
                                        <div class="stat-icon bg-warning bg-opacity-10 rounded-circle p-3">
                                            <i class="fas fa-graduation-cap text-warning"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h4 class="stat-number">{{ $stats['monthly_enrollments'] }}</h4>
                                            <p class="stat-label">تسجيلات جديدة</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item">
                                        <div class="stat-icon bg-info bg-opacity-10 rounded-circle p-3">
                                            <i class="fas fa-dollar-sign text-info"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h4 class="stat-number">${{ number_format($stats['monthly_revenue'], 2) }}</h4>
                                            <p class="stat-label">إيرادات الشهر</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="col-lg-6 mb-4">
                <div class="performance-metrics-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                مؤشرات الأداء
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="metric-item">
                                <div class="metric-header">
                                    <span class="metric-label">معدل نمو المستخدمين</span>
                                    <span class="metric-value">+{{ round(($stats['monthly_users'] / max($stats['total_users'], 1)) * 100, 1) }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" style="width: {{ min(($stats['monthly_users'] / max($stats['total_users'], 1)) * 100, 100) }}%"></div>
                                </div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-header">
                                    <span class="metric-label">معدل نمو الكورسات</span>
                                    <span class="metric-value">+{{ round(($stats['monthly_courses'] / max($stats['total_courses'], 1)) * 100, 1) }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: {{ min(($stats['monthly_courses'] / max($stats['total_courses'], 1)) * 100, 100) }}%"></div>
                                </div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-header">
                                    <span class="metric-label">معدل نمو التسجيلات</span>
                                    <span class="metric-value">+{{ round(($stats['monthly_enrollments'] / max($stats['total_enrollments'], 1)) * 100, 1) }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" style="width: {{ min(($stats['monthly_enrollments'] / max($stats['total_enrollments'], 1)) * 100, 100) }}%"></div>
                                </div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-header">
                                    <span class="metric-label">معدل نمو الإيرادات</span>
                                    <span class="metric-value">+{{ round(($stats['monthly_revenue'] / max($stats['total_revenue'], 1)) * 100, 1) }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-info" style="width: {{ min(($stats['monthly_revenue'] / max($stats['total_revenue'], 1)) * 100, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-12">
                <div class="recent-activity-card" data-aos="fade-up">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-clock me-2"></i>
                                النشاط الأخير
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="activity-list">
                                <div class="activity-item d-flex align-items-center p-4 border-bottom">
                                    <div class="activity-icon bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-user-plus text-success"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">مستخدم جديد انضم للمنصة</h6>
                                        <small class="text-muted">تم تسجيل مستخدم جديد في النظام</small>
                                    </div>
                                    <small class="text-muted">منذ 5 دقائق</small>
                                </div>
                                
                                <div class="activity-item d-flex align-items-center p-4 border-bottom">
                                    <div class="activity-icon bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-book text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">كورس جديد تم إضافته</h6>
                                        <small class="text-muted">تم إضافة كورس جديد إلى المنصة</small>
                                    </div>
                                    <small class="text-muted">منذ ساعة</small>
                                </div>
                                
                                <div class="activity-item d-flex align-items-center p-4 border-bottom">
                                    <div class="activity-icon bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-graduation-cap text-warning"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">تسجيل جديد في كورس</h6>
                                        <small class="text-muted">تم تسجيل طالب جديد في كورس</small>
                                    </div>
                                    <small class="text-muted">منذ ساعتين</small>
                                </div>
                                
                                <div class="activity-item d-flex align-items-center p-4">
                                    <div class="activity-icon bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-credit-card text-info"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">دفع جديد تم إتمامه</h6>
                                        <small class="text-muted">تم إتمام عملية دفع جديدة</small>
                                    </div>
                                    <small class="text-muted">منذ 3 ساعات</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Report Modal -->
<div class="modal fade" id="exportReportModal" tabindex="-1" aria-labelledby="exportReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportReportModalLabel">
                    <i class="fas fa-download me-2"></i>
                    تصدير التقرير
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="GET" action="{{ route('admin.reports.index') }}">
                    <div class="mb-3">
                        <label class="form-label">نوع التقرير</label>
                        <select class="form-select" name="report_type">
                            <option value="comprehensive">تقرير شامل</option>
                            <option value="financial">تقرير مالي</option>
                            <option value="users">تقرير المستخدمين</option>
                            <option value="courses">تقرير الكورسات</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الفترة الزمنية</label>
                        <select class="form-select" name="report_period">
                            <option value="month">هذا الشهر</option>
                            <option value="quarter">هذا الربع</option>
                            <option value="year">هذا العام</option>
                            <option value="all">جميع البيانات</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">نوع التصدير</label>
                        <select class="form-select" name="export_format">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>
                    تصدير التقرير
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.admin-reports-page {
    background-color: #f8f9fa;
    min-height: 100vh;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    opacity: 0.9;
    margin-bottom: 0;
}

.stats-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: transform 0.2s ease-in-out;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}

.stats-primary .stats-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stats-success .stats-icon {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.stats-warning .stats-icon {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: white;
}

.stats-info .stats-icon {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
    color: white;
}

.stats-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stats-label {
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.stats-trend {
    font-size: 0.875rem;
}

.trend-up {
    color: #28a745;
}

.trend-down {
    color: #dc3545;
}

.chart-card,
.detailed-stats-card,
.performance-metrics-card,
.recent-activity-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-content {
    flex-grow: 1;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.stat-label {
    color: #6c757d;
    margin-bottom: 0;
    font-size: 0.875rem;
}

.metric-item {
    margin-bottom: 1.5rem;
}

.metric-item:last-child {
    margin-bottom: 0;
}

.metric-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.metric-label {
    font-weight: 600;
    color: #495057;
}

.metric-value {
    font-weight: bold;
    color: #28a745;
}

.progress {
    height: 0.5rem;
    border-radius: 0.25rem;
}

.activity-item {
    transition: background-color 0.2s ease-in-out;
}

.activity-item:hover {
    background-color: #f8f9fa;
}

.activity-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

@media print {
    .page-header,
    .btn,
    .modal {
        display: none !important;
    }
    
    .admin-reports-page {
        background-color: white !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
        datasets: [{
            label: 'الإيرادات الشهرية',
            data: [12000, 19000, 15000, 25000, 22000, 30000],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
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

// User Distribution Chart
const userDistributionCtx = document.getElementById('userDistributionChart').getContext('2d');
const userDistributionChart = new Chart(userDistributionCtx, {
    type: 'doughnut',
    data: {
        labels: ['طلاب', 'مدرسين', 'مديرين'],
        datasets: [{
            data: [70, 25, 5],
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});
</script>
@endsection
