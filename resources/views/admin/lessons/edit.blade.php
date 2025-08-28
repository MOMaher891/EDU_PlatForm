<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('تعديل الدرس') }} - {{ $lesson->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.lessons.update', $lesson) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Title -->
                            <div>
                                <x-input-label for="title" :value="__('عنوان الدرس')" />
                                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $lesson->title)" required autofocus />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <!-- Content -->
                            <div>
                                <x-input-label for="content" :value="__('محتوى الدرس')" />
                                <textarea id="content" name="content" rows="6" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('content', $lesson->content) }}</textarea>
                                <x-input-error :messages="$errors->get('content')" class="mt-2" />
                            </div>

                            <!-- File Type -->
                            <div>
                                <x-input-label for="file_type" :value="__('نوع الملف')" />
                                <select id="file_type" name="file_type" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                    <option value="video" {{ old('file_type', $lesson->file_type) == 'video' ? 'selected' : '' }}>فيديو</option>
                                    <option value="pdf" {{ old('file_type', $lesson->file_type) == 'pdf' ? 'selected' : '' }}>PDF</option>
                                    <option value="document" {{ old('file_type', $lesson->file_type) == 'document' ? 'selected' : '' }}>مستند</option>
                                    <option value="image" {{ old('file_type', $lesson->file_type) == 'image' ? 'selected' : '' }}>صورة</option>
                                    <option value="quiz" {{ old('file_type', $lesson->file_type) == 'quiz' ? 'selected' : '' }}>اختبار</option>
                                </select>
                                <x-input-error :messages="$errors->get('file_type')" class="mt-2" />
                            </div>

                            <!-- Current File -->
                            @if($lesson->hasFile())
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-700 mb-2">الملف الحالي</h4>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="{{ $lesson->file_icon }} text-2xl text-gray-500 mr-3"></i>
                                        <div>
                                            <p class="font-medium">{{ $lesson->file_name ?? basename($lesson->file_path) }}</p>
                                            @if($lesson->file_size)
                                                <p class="text-sm text-gray-500">{{ $lesson->file_size_human }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        @if($lesson->file_url)
                                            <a href="{{ $lesson->file_url }}" target="_blank" class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-2 px-3 rounded">
                                                <i class="fas fa-eye mr-1"></i>
                                                عرض
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.lessons.download', $lesson) }}" class="bg-green-500 hover:bg-green-700 text-white text-xs font-bold py-2 px-3 rounded">
                                            <i class="fas fa-download mr-1"></i>
                                            تحميل
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- File Upload -->
                            <div>
                                <x-input-label for="lesson_file" :value="__('رفع ملف جديد (اختياري)')" />
                                <div class="upload-area mt-1" id="fileUpload">
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

                            <!-- Video URL -->
                            <div>
                                <x-input-label for="video_url" :value="__('رابط الفيديو')" />
                                <x-text-input id="video_url" class="block mt-1 w-full" type="url" name="video_url" :value="old('video_url', $lesson->video_url)" />
                                <p class="text-sm text-gray-500 mt-1">يمكن أن يكون رابط YouTube أو Vimeo أو أي منصة فيديو أخرى</p>
                                <x-input-error :messages="$errors->get('video_url')" class="mt-2" />
                            </div>

                            <!-- Video Duration -->
                            <div>
                                <x-input-label for="video_duration" :value="__('مدة الفيديو (بالثواني)')" />
                                <x-text-input id="video_duration" class="block mt-1 w-full" type="number" name="video_duration" :value="old('video_duration', $lesson->video_duration)" min="0" />
                                <x-input-error :messages="$errors->get('video_duration')" class="mt-2" />
                            </div>

                            <!-- Order Index -->
                            <div>
                                <x-input-label for="order_index" :value="__('ترتيب الدرس')" />
                                <x-text-input id="order_index" class="block mt-1 w-full" type="number" name="order_index" :value="old('order_index', $lesson->order_index)" min="0" />
                                <p class="text-sm text-gray-500 mt-1">اتركه فارغاً ليتم ترتيبه تلقائياً</p>
                                <x-input-error :messages="$errors->get('order_index')" class="mt-2" />
                            </div>

                            <!-- Is Free -->
                            <div class="flex items-center">
                                <input id="is_free" type="checkbox" name="is_free" value="1" {{ old('is_free', $lesson->is_free) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <x-input-label for="is_free" :value="__('درس مجاني')" class="mr-2" />
                                <x-input-error :messages="$errors->get('is_free')" class="mt-2" />
                            </div>

                            <!-- Is Active -->
                            <div class="flex items-center">
                                <input id="is_active" type="checkbox" name="is_active" value="1" {{ old('is_active', $lesson->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <x-input-label for="is_active" :value="__('درس نشط')" class="mr-2" />
                                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.lessons.index', $lesson->section) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                إلغاء
                            </a>
                            <x-primary-button>
                                {{ __('تحديث الدرس') }}
                            </x-primary-button>
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
    });

    function setupFileUpload() {
        const uploadArea = document.getElementById('fileUpload');
        const input = document.getElementById('lesson_file');
        const preview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');

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
        document.getElementById('lesson_file').value = '';
        document.getElementById('filePreview').style.display = 'none';
    }
    </script>
</x-app-layout>
