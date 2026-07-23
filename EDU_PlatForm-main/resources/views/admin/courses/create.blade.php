@extends('layouts.app')

@section('title', 'إضافة كورس جديد')

@section('content')
<div class="admin-course-create-page">
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="page-title">
                        <i class="fas fa-plus me-3"></i>
                        إضافة كورس جديد
                    </h1>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-right me-1"></i>
                        العودة للقائمة
                    </a>
                    <button type="submit" form="createCourseForm" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        إنشاء الكورس
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <form id="createCourseForm" method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
            @csrf

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
                                           id="title" name="title" value="{{ old('title') }}" required>
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
                                              maxlength="500" required>{{ old('short_description') }}</textarea>
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
                                              id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
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
                                    <div class="upload-area" id="thumbnailUpload">
                                        <div class="upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                            <p class="upload-text">اسحب وأفلت الصورة هنا أو اضغط للاختيار</p>
                                            <small class="text-muted">JPG, PNG, GIF - الحد الأقصى 2MB</small>
                                        </div>
                                        <input type="file" class="form-control @error('thumbnail') is-invalid @enderror"
                                               id="thumbnail" name="thumbnail" accept="image/*" style="display: none;">
                                    </div>
                                    <div id="thumbnailPreview" class="mt-2" style="display: none;">
                                        <img src="" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="removeThumbnail()">
                                            <i class="fas fa-trash me-1"></i>
                                            إزالة
                                        </button>
                                    </div>
                                    @error('thumbnail')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="preview_video" class="form-label">
                                        فيديو معاينة
                                    </label>
                                    <div class="upload-area" id="videoUpload">
                                        <div class="upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                            <p class="upload-text">اسحب وأفلت الفيديو هنا أو اضغط للاختيار</p>
                                            <small class="text-muted">MP4, MOV, AVI - الحد الأقصى 50MB</small>
                                        </div>
                                        <input type="file" class="form-control @error('preview_video') is-invalid @enderror"
                                               id="preview_video" name="preview_video" accept="video/*" style="display: none;">
                                    </div>
                                    <div id="videoPreview" class="mt-2" style="display: none;">
                                        <video controls class="img-thumbnail" style="max-height: 150px;">
                                            <source src="" type="video/mp4">
                                            متصفحك لا يدعم تشغيل الفيديو.
                                        </video>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="removeVideo()">
                                            <i class="fas fa-trash me-1"></i>
                                            إزالة
                                        </button>
                                    </div>
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
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                            <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
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
                                        <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>مبتدئ</option>
                                        <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>متوسط</option>
                                        <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>متقدم</option>
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
                                           id="duration_hours" name="duration_hours" value="{{ old('duration_hours') }}"
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
                                           id="price" name="price" value="{{ old('price') }}"
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
                                           id="discount_price" name="discount_price" value="{{ old('discount_price') }}"
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
                                               name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_published">
                                            نشر الكورس
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_featured"
                                               name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
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
                                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    إلغاء
                                </a>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    إنشاء الكورس
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

.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.upload-area:hover {
    border-color: var(--primary-color);
    background: rgba(99, 102, 241, 0.05);
}

.upload-area.dragover {
    border-color: var(--primary-color);
    background: rgba(99, 102, 241, 0.1);
}

.upload-content {
    pointer-events: none;
}

.upload-text {
    margin: 0.5rem 0;
    color: #6c757d;
}

.form-actions {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-top: 2rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setupFileUpload('thumbnailUpload', 'thumbnail', 'thumbnailPreview', 'image');
    setupFileUpload('videoUpload', 'preview_video', 'videoPreview', 'video');
});

function setupFileUpload(uploadAreaId, inputId, previewId, type) {
    const uploadArea = document.getElementById(uploadAreaId);
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    uploadArea.addEventListener('click', () => input.click());

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            input.files = files;
            handleFileSelect(input, preview, type);
        }
    });

    input.addEventListener('change', (e) => {
        handleFileSelect(input, preview, type);
    });
}

function handleFileSelect(input, preview, type) {
    const file = input.files[0];
    if (!file) return;

    const maxSize = type === 'image' ? 2 * 1024 * 1024 : 50 * 1024 * 1024;
    if (file.size > maxSize) {
        alert(`حجم الملف كبير جداً. الحد الأقصى ${type === 'image' ? '2MB' : '50MB'}`);
        input.value = '';
        return;
    }

    const validTypes = type === 'image'
        ? ['image/jpeg', 'image/png', 'image/gif']
        : ['video/mp4', 'video/mov', 'video/avi'];

    if (!validTypes.includes(file.type)) {
        alert(`نوع الملف غير مدعوم. الأنواع المدعومة: ${type === 'image' ? 'JPG, PNG, GIF' : 'MP4, MOV, AVI'}`);
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        if (type === 'image') {
            preview.querySelector('img').src = e.target.result;
        } else {
            preview.querySelector('video source').src = e.target.result;
            preview.querySelector('video').load();
        }
        preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
}

function removeThumbnail() {
    document.getElementById('thumbnail').value = '';
    document.getElementById('thumbnailPreview').style.display = 'none';
}

function removeVideo() {
    document.getElementById('preview_video').value = '';
    document.getElementById('videoPreview').style.display = 'none';
}
</script>
@endsection
