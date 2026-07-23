@extends('layouts.app')

@section('title', 'تعديل الكورس - ' . $course->title)

@section('content')
<div class="admin-course-edit-page">
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="page-title">
                        <i class="fas fa-edit me-3"></i>
                        تعديل الكورس
                    </h1>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-right me-1"></i>
                        العودة للتفاصيل
                    </a>
                    <button type="submit" form="editCourseForm" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        حفظ التغييرات
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <form id="editCourseForm" method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                المعلومات الأساسية
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="title" class="form-label">
                                        عنوان الكورس
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title" value="{{ old('title', $course->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="short_description" class="form-label">
                                        الوصف المختصر
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('short_description') is-invalid @enderror"
                                              id="short_description" name="short_description" rows="3"
                                              maxlength="500" required>{{ old('short_description', $course->short_description) }}</textarea>
                                    @error('short_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">
                                        الوصف التفصيلي
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="6" required>{{ old('description', $course->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Media Upload -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-photo-video me-2"></i>
                                الوسائط
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="thumbnail" class="form-label">
                                        صورة الكورس
                                    </label>

                                    @if($course->thumbnail)
                                        <div class="current-thumbnail mb-3">
                                            <img src="{{ asset('storage/' . $course->thumbnail) }}"
                                                 alt="Current thumbnail" class="img-thumbnail" style="max-height: 150px;">
                                        </div>
                                    @endif

                                    <input type="file" class="form-control @error('thumbnail') is-invalid @enderror"
                                           id="thumbnail" name="thumbnail" accept="image/*">
                                    @error('thumbnail')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="preview_video" class="form-label">
                                        فيديو معاينة
                                    </label>

                                    @if($course->preview_video)
                                        <div class="current-video mb-3">
                                            <video controls class="img-thumbnail" style="max-height: 150px;">
                                                <source src="{{ asset('storage/' . $course->preview_video) }}" type="video/mp4">
                                                متصفحك لا يدعم تشغيل الفيديو.
                                            </video>
                                        </div>
                                    @endif

                                    <input type="file" class="form-control @error('preview_video') is-invalid @enderror"
                                           id="preview_video" name="preview_video" accept="video/*">
                                    @error('preview_video')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Details -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-cogs me-2"></i>
                                تفاصيل الكورس
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">
                                        التصنيف
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                        <option value="">اختر التصنيف</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="instructor_id" class="form-label">
                                        المدرب
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('instructor_id') is-invalid @enderror" id="instructor_id" name="instructor_id" required>
                                        <option value="">اختر المدرب</option>
                                        @foreach($instructors as $instructor)
                                            <option value="{{ $instructor->id }}"
                                                {{ old('instructor_id', $course->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                                {{ $instructor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('instructor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="level" class="form-label">
                                        المستوى
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                                        <option value="">اختر المستوى</option>
                                        <option value="beginner" {{ old('level', $course->level) == 'beginner' ? 'selected' : '' }}>مبتدئ</option>
                                        <option value="intermediate" {{ old('level', $course->level) == 'intermediate' ? 'selected' : '' }}>متوسط</option>
                                        <option value="advanced" {{ old('level', $course->level) == 'advanced' ? 'selected' : '' }}>متقدم</option>
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="duration_hours" class="form-label">
                                        المدة بالساعات
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control @error('duration_hours') is-invalid @enderror"
                                           id="duration_hours" name="duration_hours"
                                           value="{{ old('duration_hours', $course->duration_hours) }}"
                                           min="1" max="1000" required>
                                    @error('duration_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="price" class="form-label">
                                        السعر
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror"
                                           id="price" name="price"
                                           value="{{ old('price', $course->price) }}"
                                           min="0" step="0.01" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="discount_price" class="form-label">
                                        سعر الخصم
                                    </label>
                                    <input type="number" class="form-control @error('discount_price') is-invalid @enderror"
                                           id="discount_price" name="discount_price"
                                           value="{{ old('discount_price', $course->discount_price) }}"
                                           min="0" step="0.01">
                                    @error('discount_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        خيارات النشر
                                    </label>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="is_published"
                                               name="is_published" value="1"
                                               {{ old('is_published', $course->is_published) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_published">
                                            نشر الكورس
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_featured"
                                               name="is_featured" value="1"
                                               {{ old('is_featured', $course->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">
                                            كورس مميز
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    إلغاء
                                </a>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    حفظ التغييرات
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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

.form-actions {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-top: 2rem;
}

.current-thumbnail, .current-video {
    text-align: center;
}
</style>
@endsection
