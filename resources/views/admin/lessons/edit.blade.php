@extends('layouts.app')

@section('title', 'تعديل الدرس - ' . $lesson->title)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex align-items-center">
                    <i class="fas fa-edit me-2"></i>
                    <h5 class="mb-0">تعديل الدرس - {{ $lesson->title }}</h5>
                </div>
                <div class="card-body">
                    @php
                        $initialVideoSource = 'file';
                        if ($lesson->file_type === 'video' && !empty($lesson->video_url)) {
                            $initialVideoSource = 'link';
                        }
                    @endphp

                    @if($lesson->file_type === 'video' && ($lesson->video_embed_url || $lesson->file_path))
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-2">معاينة الفيديو</h6>
                        <div class="ratio ratio-16x9 rounded overflow-hidden" id="videoPreviewWrapper">
                            @if($lesson->video_embed_url)
                                @if(str_contains($lesson->video_embed_url, 't.me'))
                                    <div class="telegram-preview-placeholder d-flex flex-column align-items-center justify-content-center bg-dark text-white p-4 h-100" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                                        <i class="fab fa-telegram-plane fa-3x mb-2" style="color: #229ED9;"></i>
                                        <h6 class="mb-1 text-white fw-bold">فيديو تيليجرام</h6>
                                        <p class="text-muted small mb-2 text-center" style="max-width: 400px;">رابط تيليجرام خارجي. سيتم فتح هذا الفيديو للطلاب مباشرة أو عبر التطبيق.</p>
                                        <a href="{{ $lesson->video_url }}" target="_blank" class="btn btn-sm text-white px-3 py-1.5" style="background-color: #229ED9; border: none; border-radius: 20px;">
                                            فتح الفيديو للمعاينة
                                        </a>
                                    </div>
                                @else
                                    <iframe id="videoPreviewIframe" src="{{ $lesson->video_embed_url }}" title="preview" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                @endif
                            @elseif($lesson->file_path)
                            @php
                                $path = ltrim((string) $lesson->file_path, '/');
                                $videoSrc = null;
                                if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
                                    $videoSrc = $path;
                                } elseif (\Illuminate\Support\Str::startsWith($path, 'public/storage/')) {
                                    $videoSrc = asset(substr($path, strlen('public/')));
                                } elseif (\Illuminate\Support\Str::startsWith($path, 'storage/')) {
                                    $videoSrc = asset($path);
                                } else {
                                    $videoSrc = asset('storage/' . $path);
                                }
                            @endphp
                            @if($videoSrc)
                            <video id="videoPreview" class="w-100 h-100" controls playsinline preload="metadata" style="object-fit: cover;">
                                <source id="videoPreviewSource" src="{{ $videoSrc }}" type="video/mp4">
                                متصفحك لا يدعم تشغيل الفيديو.
                            </video>
                            @endif
                            @endif
                        </div>
                    </div>
                    @endif
                    <form action="{{ route('admin.lessons.update', $lesson) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <!-- Title -->
                            <div class="col-12">
                                <label for="title" class="form-label">عنوان الدرس</label>
                                <input id="title" type="text" name="title" value="{{ old('title', $lesson->title) }}" required autofocus class="form-control @error('title') is-invalid @enderror">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Content -->
                            <div class="col-12">
                                <label for="content" class="form-label">محتوى الدرس</label>
                                <textarea id="content" name="content" rows="6" class="form-control @error('content') is-invalid @enderror">{{ old('content', $lesson->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- File Type -->
                            <div class="col-md-6">
                                <label for="file_type" class="form-label">نوع الملف</label>
                                <select id="file_type" name="file_type" class="form-select">
                                    <option value="video" {{ old('file_type', $lesson->file_type) == 'video' ? 'selected' : '' }}>فيديو</option>
                                    <option value="pdf" {{ old('file_type', $lesson->file_type) == 'pdf' ? 'selected' : '' }}>PDF</option>
                                    <option value="document" {{ old('file_type', $lesson->file_type) == 'document' ? 'selected' : '' }}>مستند</option>
                                    <option value="image" {{ old('file_type', $lesson->file_type) == 'image' ? 'selected' : '' }}>صورة</option>
                                    <option value="quiz" {{ old('file_type', $lesson->file_type) == 'quiz' ? 'selected' : '' }}>اختبار</option>
                                </select>
                                @error('file_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Video Source Selection -->
                            <div class="col-md-6" id="videoSourceWrapper" style="display: none;">
                                <label class="form-label">مصدر الفيديو</label>
                                <div class="d-flex gap-4 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="video_source" id="video_source_file" value="file" {{ old('video_source', $initialVideoSource) == 'file' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="video_source_file">
                                            رفع ملف فيديو
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="video_source" id="video_source_link" value="link" {{ old('video_source', $initialVideoSource) == 'link' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="video_source_link">
                                            رابط فيديو خارجي (YouTube / Vimeo / Link)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Current File -->
                            @if($lesson->hasFile())
                            <div class="col-12" id="currentFileWrapper">
                                <div class="bg-light p-3 rounded">
                                    <h6 class="fw-semibold text-muted mb-2">الملف الحالي</h6>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="{{ $lesson->file_icon }} fs-4 text-secondary me-2"></i>
                                            <div>
                                                <p class="mb-0 fw-medium">{{ $lesson->file_name ?? basename($lesson->file_path) }}</p>
                                                @if($lesson->file_size)
                                                    <small class="text-muted">{{ $lesson->file_size_human }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            @if($lesson->file_url)
                                                <a href="{{ $lesson->file_url }}" target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i>
                                                    عرض
                                                </a>
                                            @endif
                                            <a href="{{ route('admin.lessons.download', $lesson) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-download me-1"></i>
                                                تحميل
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- File Upload Wrapper -->
                            <div class="col-12" id="fileUploadWrapper" style="display: none;">
                                <label for="lesson_file" class="form-label" id="fileUploadLabel">رفع ملف جديد (اختياري)</label>
                                <div class="upload-area mt-1" id="fileUpload">
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                        <p class="upload-text" id="uploadAreaText">اسحب وأفلت الملف هنا أو اضغط للاختيار</p>
                                        <small class="text-muted" id="uploadAreaHelp">الحد الأقصى {{ $appSettings->max_file_size ?? 10 }}MB</small>
                                    </div>
                                    <input type="file" class="form-control @error('lesson_file') is-invalid @enderror"
                                           id="lesson_file" name="lesson_file" style="display: none;">
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

                            <!-- Video URL Wrapper -->
                            <div class="col-md-6" id="videoUrlWrapper" style="display: none;">
                                <label for="video_url" class="form-label">رابط الفيديو</label>
                                <input id="video_url" type="url" name="video_url" value="{{ old('video_url', $lesson->video_url) }}" class="form-control @error('video_url') is-invalid @enderror">
                                <small class="text-muted" id="videoUrlHelp">يمكن أن يكون رابط YouTube أو Vimeo أو أي منصة فيديو أخرى</small>
                                @error('video_url')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Video Duration Wrapper -->
                            <div class="col-md-6" id="videoDurationWrapper" style="display: none;">
                                <label for="video_duration" class="form-label">مدة الفيديو (بالثواني)</label>
                                <input id="video_duration" type="number" name="video_duration" value="{{ old('video_duration', $lesson->video_duration) }}" min="0" class="form-control @error('video_duration') is-invalid @enderror">
                                @error('video_duration')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Order Index -->
                            <div class="col-md-6">
                                <label for="order_index" class="form-label">ترتيب الدرس</label>
                                <input id="order_index" type="number" name="order_index" value="{{ old('order_index', $lesson->order_index) }}" min="0" class="form-control @error('order_index') is-invalid @enderror">
                                <small class="text-muted">اتركه فارغاً ليتم ترتيبه تلقائياً</small>
                                @error('order_index')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div class="col-md-6">
                                <label for="price" class="form-label">سعر الدرس</label>
                                <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $lesson->price) }}" class="form-control @error('price') is-invalid @enderror">
                                @error('price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Collapsible Advanced File Path Section -->
                            <div class="col-12 mb-3">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#advancedOptions" aria-expanded="false" aria-controls="advancedOptions">
                                    <i class="fas fa-cog me-1"></i>
                                    خيارات متقدمة (مسار ملف محلي)
                                </button>
                                <div class="collapse mt-2" id="advancedOptions">
                                    <div class="card card-body bg-light border-0">
                                        <label for="file_path" class="form-label">مسار الملف</label>
                                        <input type="text" class="form-control @error('file_path') is-invalid @enderror"
                                               id="file_path" name="file_path" value="{{ old('file_path', $lesson->file_path) }}">
                                        <div class="form-text">مسار الملف المحلي أو رابط التحميل</div>
                                        @error('file_path')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Is Free -->
                            <div class="col-md-6 d-flex align-items-center gap-2">
                                <input id="is_free" type="checkbox" name="is_free" value="1" {{ old('is_free', $lesson->is_free) ? 'checked' : '' }} class="form-check-input">
                                <label for="is_free" class="form-check-label">درس مجاني</label>
                                @error('is_free')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Is Active -->
                            <div class="col-md-6 d-flex align-items-center gap-2">
                                <input id="is_active" type="checkbox" name="is_active" value="1" {{ old('is_active', $lesson->is_active) ? 'checked' : '' }} class="form-check-input">
                                <label for="is_active" class="form-check-label">درس نشط</label>
                                @error('is_active')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 gap-2">
                            <a href="{{ route('admin.lessons.index', $lesson->section) }}" class="btn btn-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary">تحديث الدرس</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
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
        border-color: #6366f1;
        background: rgba(99, 102, 241, 0.05);
    }

    .upload-area.dragover {
        border-color: #6366f1;
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
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        setupFileUpload();
        setupDynamicFields();
    });

    function setupDynamicFields() {
        const fileTypeSelect = document.getElementById('file_type');
        const videoSourceRadios = document.getElementsByName('video_source');

        if (fileTypeSelect) {
            fileTypeSelect.addEventListener('change', toggleFields);
        }

        videoSourceRadios.forEach(radio => {
            radio.addEventListener('change', toggleFields);
        });

        // Run once on load to initialize correct state
        toggleFields();
    }

    function toggleFields() {
        const fileType = document.getElementById('file_type').value;
        const videoSource = document.querySelector('input[name="video_source"]:checked')?.value || 'file';

        const videoSourceWrapper = document.getElementById('videoSourceWrapper');
        const fileUploadWrapper = document.getElementById('fileUploadWrapper');
        const videoUrlWrapper = document.getElementById('videoUrlWrapper');
        const videoDurationWrapper = document.getElementById('videoDurationWrapper');
        const input = document.getElementById('lesson_file');

        const fileUploadLabel = document.getElementById('fileUploadLabel');
        const uploadAreaText = document.getElementById('uploadAreaText');
        const uploadAreaHelp = document.getElementById('uploadAreaHelp');
        const currentFileWrapper = document.getElementById('currentFileWrapper');

        // Hide everything first
        videoSourceWrapper.style.display = 'none';
        fileUploadWrapper.style.display = 'none';
        videoUrlWrapper.style.display = 'none';
        videoDurationWrapper.style.display = 'none';

        if (fileType === 'video') {
            videoSourceWrapper.style.display = 'block';
            videoDurationWrapper.style.display = 'block';

            if (videoSource === 'file') {
                fileUploadWrapper.style.display = 'block';
                if (currentFileWrapper) currentFileWrapper.style.display = 'block';
                if (input) input.setAttribute('accept', 'video/*');
                if (fileUploadLabel) fileUploadLabel.innerHTML = 'رفع ملف فيديو جديد (اختياري)';
                if (uploadAreaText) uploadAreaText.textContent = 'اسحب وأفلت ملف الفيديو هنا أو اضغط للاختيار';
                if (uploadAreaHelp) uploadAreaHelp.textContent = 'الحد الأقصى {{ $appSettings->max_file_size ?? 10 }}MB (يدعم mp4, webm, avi)';
            } else {
                videoUrlWrapper.style.display = 'block';
                if (currentFileWrapper) currentFileWrapper.style.display = 'none';
            }
        } else if (fileType === 'pdf') {
            fileUploadWrapper.style.display = 'block';
            if (currentFileWrapper) currentFileWrapper.style.display = 'block';
            if (input) input.setAttribute('accept', '.pdf');
            if (fileUploadLabel) fileUploadLabel.innerHTML = 'رفع ملف PDF جديد (اختياري)';
            if (uploadAreaText) uploadAreaText.textContent = 'اسحب وأفلت ملف PDF هنا أو اضغط للاختيار';
            if (uploadAreaHelp) uploadAreaHelp.textContent = 'الحد الأقصى {{ $appSettings->max_file_size ?? 10 }}MB';
        } else if (fileType === 'image') {
            fileUploadWrapper.style.display = 'block';
            if (currentFileWrapper) currentFileWrapper.style.display = 'block';
            if (input) input.setAttribute('accept', 'image/*');
            if (fileUploadLabel) fileUploadLabel.innerHTML = 'رفع صورة جديدة (اختياري)';
            if (uploadAreaText) uploadAreaText.textContent = 'اسحب وأفلت الصورة هنا أو اضغط للاختيار';
            if (uploadAreaHelp) uploadAreaHelp.textContent = 'الحد الأقصى 10MB';
        } else if (fileType === 'document') {
            fileUploadWrapper.style.display = 'block';
            if (currentFileWrapper) currentFileWrapper.style.display = 'block';
            if (input) input.setAttribute('accept', '.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt');
            if (fileUploadLabel) fileUploadLabel.innerHTML = 'رفع مستند جديد (اختياري)';
            if (uploadAreaText) uploadAreaText.textContent = 'اسحب وأفلت المستند هنا أو اضغط للاختيار';
            if (uploadAreaHelp) uploadAreaHelp.textContent = 'الحد الأقصى 20MB';
        } else {
            if (currentFileWrapper) currentFileWrapper.style.display = 'none';
        }
    }

    function setupFileUpload() {
        const uploadArea = document.getElementById('fileUpload');
        const input = document.getElementById('lesson_file');
        const preview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const videoPreview = document.getElementById('videoPreview');
        const videoPreviewSource = document.getElementById('videoPreviewSource');
        const videoPreviewIframe = document.getElementById('videoPreviewIframe');
        const videoUrlInput = document.getElementById('video_url');

        if (!uploadArea || !input) return;

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
                handleFileSelect(input, preview, fileName);
            }
        });

        input.addEventListener('change', (e) => {
            handleFileSelect(input, preview, fileName);
            const file = input.files && input.files[0];
            if (file && file.type.startsWith('video/')) {
                // Switch to HTML5 video preview for local file
                const wrapper = document.getElementById('videoPreviewWrapper');
                if (wrapper) {
                    wrapper.innerHTML = '<video id="videoPreview" class="w-100 h-100" controls playsinline preload="metadata" style="object-fit: cover;"><source id="videoPreviewSource" type="video/mp4"></video>';
                }
                const blobUrl = URL.createObjectURL(file);
                const dynVideo = document.getElementById('videoPreview');
                const dynSource = document.getElementById('videoPreviewSource');
                if (dynVideo && dynSource) {
                    dynSource.src = blobUrl;
                    dynVideo.load();
                }
            }
        });

        if (videoUrlInput) {
            videoUrlInput.addEventListener('input', function() {
                const url = this.value.trim();
                if (!url) return;
                const isYouTube = url.includes('youtube.com') || url.includes('youtu.be');
                const isVimeo = url.includes('vimeo.com');
                const isTelegram = url.includes('t.me') || url.includes('telegram.me');
                const isGoogleDrive = url.includes('drive.google.com') || url.includes('docs.google.com');
                let embedSrc = null;
                if (isYouTube) {
                    const short = url.match(/youtu\.be\/([\w-]+)/);
                    const long = url.match(/[?&]v=([\w-]+)/);
                    const id = short ? short[1] : (long ? long[1] : null);
                    if (id) embedSrc = 'https://www.youtube.com/embed/' + id;
                } else if (isVimeo) {
                    const m = url.match(/vimeo\.com\/(\d+)/);
                    if (m) embedSrc = 'https://player.vimeo.com/video/' + m[1];
                } else if (isGoogleDrive) {
                    const match = url.match(/(?:drive|docs)\.google\.com\/(?:file\/d\/|open\?id=)([\w-]+)/);
                    if (match && match[1]) {
                        embedSrc = 'https://drive.google.com/file/d/' + match[1] + '/preview';
                    }
                }
                const wrapper = document.getElementById('videoPreviewWrapper');
                if (!wrapper) return;
                if (isTelegram) {
                    wrapper.innerHTML = `
                        <div class="telegram-preview-placeholder d-flex flex-column align-items-center justify-content-center bg-dark text-white p-4 h-100" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                            <i class="fab fa-telegram-plane fa-3x mb-2" style="color: #229ED9;"></i>
                            <h6 class="mb-1 text-white fw-bold">فيديو تيليجرام</h6>
                            <p class="text-muted small mb-2 text-center" style="max-width: 400px;">رابط تيليجرام خارجي. سيتم فتح هذا الفيديو للطلاب مباشرة أو عبر التطبيق.</p>
                            <a href="${url}" target="_blank" class="btn btn-sm text-white px-3 py-1.5" style="background-color: #229ED9; border: none; border-radius: 20px;">
                                فتح الفيديو للمعاينة
                            </a>
                        </div>
                    `;
                } else if (embedSrc) {
                    wrapper.innerHTML = '<iframe id="videoPreviewIframe" title="preview" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';
                    const ifr = document.getElementById('videoPreviewIframe');
                    if (ifr) ifr.src = embedSrc;
                } else {
                    wrapper.innerHTML = '<video id="videoPreview" class="w-100 h-100" controls playsinline preload="metadata" style="object-fit: cover;"><source id="videoPreviewSource" type="video/mp4"></video>';
                    const dynVideo = document.getElementById('videoPreview');
                    const dynSource = document.getElementById('videoPreviewSource');
                    if (dynVideo && dynSource) {
                        dynSource.src = url;
                        dynVideo.load();
                    }
                }
            });
        }
    }

    function handleFileSelect(input, preview, fileName) {
        const file = input.files[0];
        if (!file) return;

        const maxMb = {{ $appSettings->max_file_size ?? 10 }};
        const maxSize = maxMb * 1024 * 1024;
        if (file.size > maxSize) {
            alert('حجم الملف كبير جداً. الحد الأقصى ' + maxMb + 'MB');
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
                const fileRadio = document.getElementById('video_source_file');
                if (fileRadio) {
                    fileRadio.checked = true;
                }
            } else if (file.type.startsWith('image/')) {
                fileTypeSelect.value = 'image';
            } else if (file.type === 'application/pdf') {
                fileTypeSelect.value = 'pdf';
            } else if (file.type.includes('document') || file.type.includes('word') || file.type.includes('excel') || file.type.includes('powerpoint') || file.type === 'text/plain') {
                fileTypeSelect.value = 'document';
            }
            toggleFields();
        }
    }

    function removeFile() {
        document.getElementById('lesson_file').value = '';
        document.getElementById('filePreview').style.display = 'none';
    }
    </script>
</div>
@endsection
