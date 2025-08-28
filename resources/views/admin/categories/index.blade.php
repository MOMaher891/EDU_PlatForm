@extends('layouts.app')

@section('title', 'إدارة التصنيفات')

@section('content')
<div class="admin-categories-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="page-title">
                        <i class="fas fa-folder me-3"></i>
                        إدارة التصنيفات
                    </h1>
                    <p class="page-subtitle">إدارة وتنظيم تصنيفات الكورسات في المنصة</p>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus me-2"></i>
                        إضافة تصنيف جديد
                    </button>
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
                        <i class="fas fa-folder"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $categories->count() }}</h3>
                        <p class="stats-label">إجمالي التصنيفات</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-success" data-aos="fade-up" data-aos-delay="100">
                    <div class="stats-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $categories->where('is_active', true)->count() }}</h3>
                        <p class="stats-label">التصنيفات النشطة</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-warning" data-aos="fade-up" data-aos-delay="200">
                    <div class="stats-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $categories->sum('courses_count') }}</h3>
                        <p class="stats-label">إجمالي الكورسات</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-info" data-aos="fade-up" data-aos-delay="300">
                    <div class="stats-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $categories->where('courses_count', '>', 0)->count() }}</h3>
                        <p class="stats-label">تصنيفات تحتوي على كورسات</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="categories-grid-section" data-aos="fade-up">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-list me-2"></i>
                            قائمة التصنيفات
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted">إجمالي النتائج: {{ $categories->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        @forelse($categories as $category)
                        <div class="col-lg-4 col-md-6">
                            <div class="category-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-4">
                                        <div class="category-header d-flex align-items-center justify-content-between mb-3">
                                            <div class="category-icon">
                                                <div class="icon-wrapper" style="background-color: {{ $category->color ?? '#667eea' }}">
                                                    <i class="fas {{ $category->icon ?? 'fa-folder' }}"></i>
                                                </div>
                                            </div>
                                            <div class="category-status">
                                                @if($category->is_active)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>
                                                        نشط
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-pause-circle me-1"></i>
                                                        غير نشط
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="category-content">
                                            <h5 class="category-title mb-2">{{ $category->name }}</h5>
                                            <p class="category-description text-muted mb-3">
                                                {{ $category->description ?? 'لا يوجد وصف للتصنيف' }}
                                            </p>

                                            <div class="category-stats d-flex justify-content-between align-items-center mb-3">
                                                <div class="stat-item">
                                                    <span class="stat-number">{{ $category->courses_count }}</span>
                                                    <span class="stat-label">كورس</span>
                                                </div>
                                                <div class="stat-item">
                                                    <span class="stat-number">{{ $category->created_at->format('Y/m/d') }}</span>
                                                    <span class="stat-label">تاريخ الإنشاء</span>
                                                </div>
                                            </div>

                                            <div class="category-actions">
                                                <div class="btn-group w-100" role="group">
                                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                                            onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}', '{{ $category->icon }}', '{{ $category->color }}', {{ $category->is_active ? 'true' : 'false' }})">
                                                        <i class="fas fa-edit me-1"></i>
                                                        تعديل
                                                    </button>
                                                    @if($category->courses_count == 0)
                                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                                onclick="deleteCategory({{ $category->id }}, '{{ $category->name }}')">
                                                            <i class="fas fa-trash me-1"></i>
                                                            حذف
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                                                            <i class="fas fa-lock me-1"></i>
                                                            لا يمكن الحذف
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="empty-state text-center py-5">
                                <i class="fas fa-folder fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد تصنيفات</h5>
                                <p class="text-muted">لم يتم إنشاء أي تصنيفات بعد. ابدأ بإضافة تصنيف جديد.</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                    <i class="fas fa-plus me-2"></i>
                                    إضافة تصنيف جديد
                                </button>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">
                    <i class="fas fa-plus me-2"></i>
                    إضافة تصنيف جديد
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اسم التصنيف</label>
                        <input type="text" class="form-control" name="name" required placeholder="أدخل اسم التصنيف">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">وصف التصنيف</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="أدخل وصف التصنيف"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">الأيقونة</label>
                                <select class="form-select" name="icon">
                                    <option value="fa-folder">📁 مجلد</option>
                                    <option value="fa-book">📚 كتاب</option>
                                    <option value="fa-code">💻 برمجة</option>
                                    <option value="fa-palette">🎨 تصميم</option>
                                    <option value="fa-chart-line">📊 إحصائيات</option>
                                    <option value="fa-language">🌐 لغات</option>
                                    <option value="fa-music">🎵 موسيقى</option>
                                    <option value="fa-camera">📷 تصوير</option>
                                    <option value="fa-dumbbell">🏋️ رياضة</option>
                                    <option value="fa-utensils">🍽️ طبخ</option>
                                </select>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">اللون</label>
                                <input type="color" class="form-control form-control-color w-100" name="color" value="#667eea">
                            </div>
                        </div> --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        حفظ التصنيف
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">
                    <i class="fas fa-edit me-2"></i>
                    تعديل التصنيف
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editCategoryForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اسم التصنيف</label>
                        <input type="text" class="form-control" name="name" id="editCategoryName" required placeholder="أدخل اسم التصنيف">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">وصف التصنيف</label>
                        <textarea class="form-control" name="description" id="editCategoryDescription" rows="3" placeholder="أدخل وصف التصنيف"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">الأيقونة</label>
                                <select class="form-select" name="icon" id="editCategoryIcon">
                                    <option value="fa-folder">📁 مجلد</option>
                                    <option value="fa-book">📚 كتاب</option>
                                    <option value="fa-code">💻 برمجة</option>
                                    <option value="fa-palette">🎨 تصميم</option>
                                    <option value="fa-chart-line">📊 إحصائيات</option>
                                    <option value="fa-language">🌐 لغات</option>
                                    <option value="fa-music">🎵 موسيقى</option>
                                    <option value="fa-camera">📷 تصوير</option>
                                    <option value="fa-dumbbell">🏋️ رياضة</option>
                                    <option value="fa-utensils">🍽️ طبخ</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">اللون</label>
                                <input type="color" class="form-control form-control-color w-100" name="color" id="editCategoryColor" value="#667eea">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.admin-categories-page {
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
    margin-bottom: 0;
}

.categories-grid-section {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.category-card {
    transition: transform 0.2s ease-in-out;
}

.category-card:hover {
    transform: translateY(-5px);
}

.category-header {
    margin-bottom: 1rem;
}

.icon-wrapper {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.category-title {
    font-weight: 600;
    color: #212529;
}

.category-description {
    font-size: 0.875rem;
    line-height: 1.5;
}

.category-stats {
    border-top: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
    padding: 1rem 0;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-weight: bold;
    font-size: 1.125rem;
    color: #212529;
}

.stat-label {
    font-size: 0.75rem;
    color: #6c757d;
}

.category-actions {
    margin-top: 1rem;
}

.btn-group .btn {
    border-radius: 0.375rem;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-state i {
    opacity: 0.5;
}

.badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
}

.form-control-color {
    height: 38px;
}
</style>

<script>
function editCategory(id, name, description, icon, color, isActive) {
    document.getElementById('editCategoryName').value = name;
    document.getElementById('editCategoryDescription').value = description || '';
    document.getElementById('editCategoryIcon').value = icon || 'fa-folder';
    document.getElementById('editCategoryColor').value = color || '#667eea';

    document.getElementById('editCategoryForm').action = `/admin/categories/${id}`;

    const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
    modal.show();
}

function deleteCategory(id, name) {
    if (confirm(`هل أنت متأكد من حذف التصنيف "${name}"؟`)) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/admin/categories/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                const successAlert = document.createElement('div');
                successAlert.className = 'alert alert-success alert-dismissible fade show';
                successAlert.innerHTML = `
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.admin-categories-page .container-fluid').insertBefore(successAlert, document.querySelector('.admin-categories-page .container-fluid').firstChild);

                // Reload the page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                alert(data.message || 'حدث خطأ أثناء حذف التصنيف');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء حذف التصنيف');
        });
    }
}

// Handle form submissions with AJAX
document.addEventListener('DOMContentLoaded', function() {
    // Handle add category form
    const addCategoryForm = document.querySelector('#addCategoryModal form');
    if (addCategoryForm) {
        addCategoryForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'حدث خطأ أثناء إضافة التصنيف');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء إضافة التصنيف');
            });
        });
    }

    // Handle edit category form
    const editCategoryForm = document.querySelector('#editCategoryForm');
    if (editCategoryForm) {
        editCategoryForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'حدث خطأ أثناء تحديث التصنيف');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء تحديث التصنيف');
            });
        });
    }
});
</script>
@endsection
