@extends('layouts.app')

@section('title', 'إدارة الكورسات')

@section('content')
<div class="admin-courses-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="page-title">
                        <i class="fas fa-book me-3"></i>
                        إدارة الكورسات
                    </h1>
                    <p class="page-subtitle">إدارة وتنظيم جميع الكورسات في المنصة</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>
                        إضافة كورس جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-primary" data-aos="fade-up">
                    <div class="stats-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $courses->total() }}</h3>
                        <p class="stats-label">إجمالي الكورسات</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-success" data-aos="fade-up" data-aos-delay="100">
                    <div class="stats-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $courses->where('is_published', true)->count() }}</h3>
                        <p class="stats-label">الكورسات المنشورة</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-warning" data-aos="fade-up" data-aos-delay="200">
                    <div class="stats-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $courses->where('is_published', false)->count() }}</h3>
                        <p class="stats-label">الكورسات المسودة</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-info" data-aos="fade-up" data-aos-delay="300">
                    <div class="stats-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $courses->where('is_featured', true)->count() }}</h3>
                        <p class="stats-label">الكورسات المميزة</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="filters-section mb-4" data-aos="fade-up">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.courses.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">البحث</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" name="search"
                                       value="{{ request('search') }}"
                                       placeholder="البحث في عنوان الكورس">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">التصنيف</label>
                            <select class="form-select" name="category">
                                <option value="">جميع التصنيفات</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">الحالة</label>
                            <select class="form-select" name="status">
                                <option value="">جميع الحالات</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشور</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">المستوى</label>
                            <select class="form-select" name="level">
                                <option value="">جميع المستويات</option>
                                <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>مبتدئ</option>
                                <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>متوسط</option>
                                <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>متقدم</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>
                                    تطبيق
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Courses Table -->
        <div class="courses-table-section" data-aos="fade-up">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>
                                قائمة الكورسات
                            </h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="table-actions">
                                <button class="btn btn-outline-primary btn-sm" onclick="exportCourses()">
                                    <i class="fas fa-download me-1"></i>
                                    تصدير
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="bulkActions()">
                                    <i class="fas fa-tasks me-1"></i>
                                    إجراءات مجمعة
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($courses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                            </div>
                                        </th>
                                        <th>الكورس</th>
                                        <th>المدرب</th>
                                        <th>التصنيف</th>
                                        <th>السعر</th>
                                        <th>الطلاب</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الإنشاء</th>
                                        <th width="120">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courses as $course)
                                        <tr class="course-row" data-course-id="{{ $course->id }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input course-checkbox" type="checkbox" value="{{ $course->id }}">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="course-info">
                                                    <div class="course-thumbnail">
                                                        <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://via.placeholder.com/60x40' }}"
                                                             alt="{{ $course->title }}" class="rounded">
                                                    </div>
                                                    <div class="course-details">
                                                        <h6 class="course-title">{{ $course->title }}</h6>
                                                        <p class="course-description">{{ Str::limit($course->short_description, 50) }}</p>
                                                        <div class="course-meta">
                                                            <span class="badge bg-secondary">{{ $course->level }}</span>
                                                            <span class="text-muted">{{ $course->duration_hours }} ساعة</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="instructor-info">
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($course->instructor->name) }}&background=6366f1&color=fff&size=32"
                                                         alt="{{ $course->instructor->name }}" class="rounded-circle me-2">
                                                    <span>{{ $course->instructor->name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $course->category->name }}</span>
                                            </td>
                                            <td>
                                                @if($course->discount_price)
                                                    <div class="price-info">
                                                        <span class="text-decoration-line-through text-muted">{{ $course->price }} ريال</span>
                                                        <br>
                                                        <span class="text-success fw-bold">{{ $course->discount_price }} ريال</span>
                                                        <span class="badge bg-danger ms-1">{{ $course->getDiscountPercentage() }}%</span>
                                                    </div>
                                                @else
                                                    <span class="fw-bold">{{ $course->price }} ريال</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="enrollment-info">
                                                    <span class="fw-bold">{{ $course->enrollments_count ?? 0 }}</span>
                                                    <br>
                                                    <small class="text-muted">طالب</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($course->is_published)
                                                    <span class="badge bg-success">منشور</span>
                                                @else
                                                    <span class="badge bg-warning">مسودة</span>
                                                @endif
                                                @if($course->is_featured)
                                                    <br>
                                                    <span class="badge bg-primary mt-1">مميز</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="date-info">
                                                    <div class="fw-bold">{{ $course->created_at->format('Y/m/d') }}</div>
                                                    <small class="text-muted">{{ $course->created_at->format('H:i') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('admin.courses.show', $course) }}"
                                                       class="btn btn-sm btn-outline-primary"
                                                       data-bs-toggle="tooltip" title="عرض التفاصيل">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.courses.edit', $course) }}"
                                                       class="btn btn-sm btn-outline-warning"
                                                       data-bs-toggle="tooltip" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteCourse({{ $course->id }})"
                                                            data-bs-toggle="tooltip" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="card-footer bg-white">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <p class="mb-0 text-muted">
                                        عرض {{ $courses->firstItem() ?? 0 }} إلى {{ $courses->lastItem() ?? 0 }}
                                        من أصل {{ $courses->total() }} كورس
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    {{ $courses->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد كورسات</h5>
                            <p class="text-muted">لم يتم إنشاء أي كورس بعد</p>
                            <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>
                                إضافة كورس جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

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

.page-subtitle {
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
}

.stats-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    transition: transform 0.3s ease;
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
    font-size: 1.5rem;
    color: white;
    margin-left: 1rem;
}

.stats-primary .stats-icon { background: linear-gradient(135deg, #667eea, #764ba2); }
.stats-success .stats-icon { background: linear-gradient(135deg, #10b981, #059669); }
.stats-warning .stats-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stats-info .stats-icon { background: linear-gradient(135deg, #06b6d4, #0891b2); }

.stats-number {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    color: var(--dark-color);
}

.stats-label {
    margin: 0;
    color: #6c757d;
    font-size: 0.9rem;
}

.course-info {
    display: flex;
    align-items: center;
}

.course-thumbnail img {
    width: 60px;
    height: 40px;
    object-fit: cover;
    margin-left: 1rem;
}

.course-title {
    font-weight: 600;
    margin: 0 0 0.25rem 0;
    color: var(--dark-color);
}

.course-description {
    margin: 0 0 0.5rem 0;
    color: #6c757d;
    font-size: 0.8rem;
}

.course-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.instructor-info {
    display: flex;
    align-items: center;
}

.action-buttons {
    display: flex;
    gap: 0.25rem;
}

.action-buttons .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.price-info {
    line-height: 1.2;
}

.enrollment-info {
    text-align: center;
}

.date-info {
    text-align: center;
}

.table th {
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    background: #f8f9fa;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}

@media (max-width: 768px) {
    .course-info {
        flex-direction: column;
        align-items: flex-start;
    }

    .course-thumbnail {
        margin-bottom: 0.5rem;
    }

    .action-buttons {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Select all functionality
    const selectAll = document.getElementById('selectAll');
    const courseCheckboxes = document.querySelectorAll('.course-checkbox');

    selectAll.addEventListener('change', function() {
        courseCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    courseCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (!this.checked) {
                selectAll.checked = false;
            } else {
                const allChecked = Array.from(courseCheckboxes).every(cb => cb.checked);
                selectAll.checked = allChecked;
            }
        });
    });
});

function deleteCourse(courseId) {
    if (confirm('هل أنت متأكد من حذف هذا الكورس؟')) {
        window.location.href = `/admin/courses/delete/${courseId}`;
    }
}

function exportCourses() {
    alert('سيتم إضافة ميزة التصدير قريباً');
}

function bulkActions() {
    const selectedCourses = Array.from(document.querySelectorAll('.course-checkbox:checked')).map(cb => cb.value);
    if (selectedCourses.length === 0) {
        alert('يرجى اختيار كورسات أولاً');
        return;
    }
    alert(`تم اختيار ${selectedCourses.length} كورس`);
}
</script>
@endsection
