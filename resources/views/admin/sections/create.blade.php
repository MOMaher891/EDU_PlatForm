@extends('layouts.app')

@section('title', 'إضافة قسم جديد - ' . $course->title)

@section('content')
<div class="admin-section-create-page">
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
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.courses.show', $course) }}">
                                    {{ $course->title }}
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.sections.index', $course) }}">
                                    الأقسام
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                إضافة قسم جديد
                            </li>
                        </ol>
                    </nav>
                    <h1 class="page-title">
                        <i class="fas fa-plus me-3"></i>
                        إضافة قسم جديد
                    </h1>
                </div>
                <div class="col-md-6 text-end">
                    <div class="header-actions">
                        <a href="{{ route('admin.sections.index', $course) }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-right me-1"></i>
                            العودة للقائمة
                        </a>
                        <button type="submit" form="createSectionForm" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            إنشاء القسم
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Create Form -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" data-aos="fade-up">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus me-2"></i>
                            معلومات القسم الجديد
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="createSectionForm" action="{{ route('admin.sections.store', $course) }}" method="POST">
                            @csrf

                            <div class="row">
                                <!-- Title -->
                                <div class="col-md-12 mb-3">
                                    <label for="title" class="form-label">
                                        <i class="fas fa-heading me-1"></i>
                                        عنوان القسم
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title" value="{{ old('title') }}" required autofocus>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">
                                        <i class="fas fa-align-left me-1"></i>
                                        وصف القسم
                                    </label>
                                    <textarea id="description" name="description" rows="4"
                                              class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Order Index -->
                                <div class="col-md-6 mb-3">
                                    <label for="order_index" class="form-label">
                                        <i class="fas fa-sort-numeric-up me-1"></i>
                                        ترتيب القسم
                                    </label>
                                    <input type="number" class="form-control @error('order_index') is-invalid @enderror"
                                           id="order_index" name="order_index" value="{{ old('order_index') }}" min="0">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        اتركه فارغاً ليتم ترتيبه تلقائياً
                                    </div>
                                    @error('order_index')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Is Active -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-toggle-on me-1"></i>
                                        حالة القسم
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active"
                                               name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            قسم نشط
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Form Actions -->
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="{{ route('admin.sections.index', $course) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i>
                                            إلغاء
                                        </a>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            إنشاء القسم
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Help Sidebar -->
            <div class="col-lg-4">
                <!-- Form Guidelines -->
                <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            إرشادات إنشاء القسم
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="guidelines-list">
                            <div class="guideline-item mb-3">
                                <div class="guideline-icon bg-primary">
                                    <i class="fas fa-heading"></i>
                                </div>
                                <div class="guideline-content">
                                    <h6 class="guideline-title">عنوان القسم</h6>
                                    <p class="guideline-text">أدخل عنواناً واضحاً ووصفياً للقسم</p>
                                </div>
                            </div>
                            <div class="guideline-item mb-3">
                                <div class="guideline-icon bg-success">
                                    <i class="fas fa-align-left"></i>
                                </div>
                                <div class="guideline-content">
                                    <h6 class="guideline-title">وصف القسم</h6>
                                    <p class="guideline-text">أضف وصفاً مختصراً لمحتوى القسم</p>
                                </div>
                            </div>
                            <div class="guideline-item">
                                <div class="guideline-icon bg-warning">
                                    <i class="fas fa-sort-numeric-up"></i>
                                </div>
                                <div class="guideline-content">
                                    <h6 class="guideline-title">ترتيب القسم</h6>
                                    <p class="guideline-text">حدد ترتيب القسم في الكورس</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Info -->
                <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            معلومات الكورس
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="course-info">
                            <div class="info-item mb-3">
                                <div class="info-label">اسم الكورس:</div>
                                <div class="info-value">{{ $course->title }}</div>
                            </div>
                            <div class="info-item mb-3">
                                <div class="info-label">المدرب:</div>
                                <div class="info-value">{{ $course->instructor->name }}</div>
                            </div>
                            <div class="info-item mb-3">
                                <div class="info-label">عدد الأقسام الحالية:</div>
                                <div class="info-value">{{ $course->sections->count() }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">عدد الدروس:</div>
                                <div class="info-value">{{ $course->getTotalLessons() }}</div>
                            </div>
                        </div>
                    </div>
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

.form-label {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.form-text {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.guideline-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.guideline-item:last-child {
    margin-bottom: 0;
}

.guideline-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-left: 1rem;
    flex-shrink: 0;
}

.guideline-content {
    flex: 1;
}

.guideline-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--dark-color);
    margin: 0 0 0.25rem 0;
}

.guideline-text {
    font-size: 0.8rem;
    color: #6c757d;
    margin: 0;
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

.form-actions {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .header-actions {
        margin-top: 1rem;
        text-align: center !important;
    }

    .form-actions .col-md-6 {
        margin-bottom: 1rem;
    }

    .form-actions .col-md-6:last-child {
        margin-bottom: 0;
    }
}
</style>
@endsection
