@extends('layouts.app')

@section('title', 'إضافة درس جديد - ' . $section->title)

@section('content')
<div class="admin-lesson-create-page">
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
                                <a href="{{ route('admin.courses.show', $section->course) }}">
                                    {{ $section->course->title }}
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.sections.index', $section->course) }}">
                                    الأقسام
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.lessons.index', $section) }}">
                                    {{ $section->title }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                إضافة درس جديد
                            </li>
                        </ol>
                    </nav>
                    <h1 class="page-title">
                        <i class="fas fa-plus me-3"></i>
                        إضافة درس جديد
                    </h1>
                </div>
                <div class="col-md-6 text-end">
                    <div class="header-actions">
                        <a href="{{ route('admin.lessons.index', $section) }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-right me-1"></i>
                            العودة للقائمة
                        </a>
                        <button type="submit" form="createLessonForm" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            إنشاء الدرس
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
                            معلومات الدرس الجديد
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="createLessonForm" action="{{ route('admin.lessons.store', $section) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Title -->
                                <div class="col-md-12 mb-3">
                                    <label for="title" class="form-label">
                                        <i class="fas fa-heading me-1"></i>
                                        عنوان الدرس
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title" value="{{ old('title') }}" required autofocus>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Content -->
                                <div class="col-md-12 mb-3">
                                    <label for="content" class="form-label">
                                        <i class="fas fa-align-left me-1"></i>
                                        محتوى الدرس
                                    </label>
                                    <textarea id="content" name="content" rows="6"
                                              class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- File Type -->
                                <div class="col-md-6 mb-3">
                                    <label for="file_type" class="form-label">
                                        <i class="fas fa-file me-1"></i>
                                        نوع الملف
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select id="file_type" name="file_type" class="form-select @error('file_type') is-invalid @enderror">
                                        <option value="">اختر نوع الملف</option>
                                        <option value="video" {{ old('file_type') == 'video' ? 'selected' : '' }}>فيديو</option>
                                        <option value="pdf" {{ old('file_type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                                        <option value="document" {{ old('file_type') == 'document' ? 'selected' : '' }}>مستند</option>
                                        <option value="image" {{ old('file_type') == 'image' ? 'selected' : '' }}>صورة</option>
                                        <option value="quiz" {{ old('file_type') == 'quiz' ? 'selected' : '' }}>اختبار</option>
                                    </select>
                                    @error('file_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- File Upload -->
                                <div class="col-md-12 mb-3">
                                    <label for="lesson_file" class="form-label">
                                        <i class="fas fa-upload me-1"></i>
                                        رفع ملف الدرس
                                    </label>
                                    <div class="upload-area" id="fileUpload">
                                        <div class="upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                            <p class="upload-text">اسحب وأفلت الملف هنا أو اضغط للاختيار</p>
                                            <small class="text-muted">الحد الأقصى 100MB - يدعم: فيديو، صور، PDF، مستندات</small>
                                        </div>
                                        <input type="file" class="form-control @error('lesson_file') is-invalid @enderror"
                                               id="lesson_file" name="lesson_file" style="display: none;" accept="video/*,image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt">
                                    </div>
                                    <div id="filePreview" class="mt-2" style="display: none;">
                                        <div class="file-preview-item">
                                            <i class="fas fa-file me-2"></i>
                                            <span id="fileName"></span>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeFile()">
                                                <i class="fas fa-trash me-1"></i>
                                                إزالة
                                            </button>
                                        </div>
                                    </div>
                                    @error('lesson_file')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Order Index -->
                                <div class="col-md-6 mb-3">
                                    <label for="order_index" class="form-label">
                                        <i class="fas fa-sort-numeric-up me-1"></i>
                                        ترتيب الدرس
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

                                <!-- Video URL -->
                                <div class="col-md-6 mb-3">
                                    <label for="video_url" class="form-label">
                                        <i class="fas fa-video me-1"></i>
                                        رابط الفيديو
                                    </label>
                                    <input type="url" class="form-control @error('video_url') is-invalid @enderror"
                                           id="video_url" name="video_url" value="{{ old('video_url') }}">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        يمكن أن يكون رابط YouTube أو Vimeo أو أي منصة فيديو أخرى
                                    </div>
                                    @error('video_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Video Duration -->
                                <div class="col-md-6 mb-3">
                                    <label for="video_duration" class="form-label">
                                        <i class="fas fa-clock me-1"></i>
                                        مدة الفيديو (بالثواني)
                                    </label>
                                    <input type="number" class="form-control @error('video_duration') is-invalid @enderror"
                                           id="video_duration" name="video_duration" value="{{ old('video_duration') }}" min="0">
                                    @error('video_duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- File Path -->
                                <div class="col-md-12 mb-3">
                                    <label for="file_path" class="form-label">
                                        <i class="fas fa-link me-1"></i>
                                        مسار الملف
                                    </label>
                                    <input type="text" class="form-control @error('file_path') is-invalid @enderror"
                                           id="file_path" name="file_path" value="{{ old('file_path') }}">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        مسار الملف المحلي أو رابط التحميل
                                    </div>
                                    @error('file_path')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Is Free -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-gift me-1"></i>
                                        نوع الدرس
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_free"
                                               name="is_free" value="1" {{ old('is_free') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_free">
                                            درس مجاني
                                        </label>
                                    </div>
                                </div>

                                <!-- Is Active -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-toggle-on me-1"></i>
                                        حالة الدرس
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active"
                                               name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            درس نشط
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Form Actions -->
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="{{ route('admin.lessons.index', $section) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i>
                                            إلغاء
                                        </a>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            إنشاء الدرس
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
                            إرشادات إنشاء الدرس
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="guidelines-list">
                            <div class="guideline-item mb-3">
                                <div class="guideline-icon bg-primary">
                                    <i class="fas fa-heading"></i>
                                </div>
                                <div class="guideline-content">
                                    <h6 class="guideline-title">عنوان الدرس</h6>
                                    <p class="guideline-text">أدخل عنواناً واضحاً ووصفياً للدرس</p>
                                </div>
                            </div>
                            <div class="guideline-item mb-3">
                                <div class="guideline-icon bg-success">
                                    <i class="fas fa-file"></i>
                                </div>
                                <div class="guideline-content">
                                    <h6 class="guideline-title">نوع الملف</h6>
                                    <p class="guideline-text">اختر نوع المحتوى المناسب للدرس</p>
                                </div>
                            </div>
                            <div class="guideline-item">
                                <div class="guideline-icon bg-warning">
                                    <i class="fas fa-gift"></i>
                                </div>
                                <div class="guideline-content">
                                    <h6 class="guideline-title">نوع الدرس</h6>
                                    <p class="guideline-text">حدد ما إذا كان الدرس مجانياً أم مدفوعاً</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Info -->
                <div class="card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            معلومات القسم
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="section-info">
                            <div class="info-item mb-3">
                                <div class="info-label">اسم القسم:</div>
                                <div class="info-value">{{ $section->title }}</div>
                            </div>
                            <div class="info-item mb-3">
                                <div class="info-label">اسم الكورس:</div>
                                <div class="info-value">{{ $section->course->title }}</div>
                            </div>
                            <div class="info-item mb-3">
                                <div class="info-label">عدد الدروس الحالية:</div>
                                <div class="info-value">{{ $section->lessons->count() }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">المدرب:</div>
                                <div class="info-value">{{ $section->course->instructor->name }}</div>
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

.file-preview-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #dee2e6;
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    setupFileUpload();
});

function setupFileUpload() {
    const uploadArea = document.getElementById('fileUpload');
    const input = document.getElementById('lesson_file');
    const preview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');

    if (!uploadArea || !input) {
        console.error('File upload elements not found');
        return;
    }

    uploadArea.addEventListener('click', () => {
        input.click();
    });

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
            handleFileSelect(input, preview, fileName);
        }
    });

    input.addEventListener('change', (e) => {
        console.log('File selected:', e.target.files[0]);
        handleFileSelect(input, preview, fileName);
    });
}

function handleFileSelect(input, preview, fileName) {
    const file = input.files[0];
    if (!file) return;

    const maxSize = 100 * 1024 * 1024; // 100MB
    if (file.size > maxSize) {
        alert('حجم الملف كبير جداً. الحد الأقصى 100MB');
        input.value = '';
        return;
    }

    // Update file name display
    fileName.textContent = file.name;
    preview.style.display = 'block';

    // Update file type based on mime type
    const fileTypeSelect = document.getElementById('file_type');
    if (fileTypeSelect) {
        if (file.type.startsWith('video/')) {
            fileTypeSelect.value = 'video';
        } else if (file.type.startsWith('image/')) {
            fileTypeSelect.value = 'image';
        } else if (file.type === 'application/pdf') {
            fileTypeSelect.value = 'pdf';
        } else if (file.type.includes('document') || file.type.includes('word') || file.type.includes('excel') || file.type.includes('powerpoint') || file.type === 'text/plain') {
            fileTypeSelect.value = 'document';
        }
    }
}

function removeFile() {
    const input = document.getElementById('lesson_file');
    const preview = document.getElementById('filePreview');
    if (input) {
        input.value = '';
    }
    if (preview) {
        preview.style.display = 'none';
    }
}
</script>
@endsection
