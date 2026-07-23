@extends('layouts.app')

@section('title', 'تفاصيل الدرس - ' . $lesson->title)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex align-items-center">
                    <i class="fas fa-chalkboard me-2"></i>
                    <h5 class="mb-0">تفاصيل الدرس - {{ $lesson->title }}</h5>
                </div>
                <div class="card-body">
                    @if($lesson->file_type === 'video' && ($lesson->video_url || $lesson->file_path))
                    <div class="mb-4">
                        @if($lesson->video_embed_url && str_contains($lesson->video_embed_url, 't.me'))
                            <!-- Telegram Video Card for Admin View -->
                            <div class="telegram-video-card p-4 rounded-3 text-center" 
                                 style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); 
                                        border: 1px solid rgba(255,255,255,0.08); 
                                        color: #f8fafc; 
                                        min-height: 220px; 
                                        display: flex; 
                                        flex-direction: column; 
                                        align-items: center; 
                                        justify-content: center;
                                        border-radius: 12px;">
                                <div class="mb-3 d-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px; border-radius: 50%; background: rgba(34, 158, 217, 0.15);">
                                    <i class="fab fa-telegram-plane fa-2x" style="color: #229ED9;"></i>
                                </div>
                                <h6 class="fw-bold mb-2 text-white">فيديو تيليجرام</h6>
                                <p class="text-muted small mb-3 px-3" style="max-width: 500px;color:rgba(var(--bs-white-rgb), var(--bs-text-opacity)) !important;">
                                    هذا الدرس يستخدم فيديو مستضاف على تيليجرام. يمكنك معاينته وفتحه عبر التطبيق مباشرة.
                                </p>
                                <a href="{{ $lesson->video_url }}" target="_blank" class="btn btn-sm text-white px-3 py-2" style="background-color: #229ED9; border: none; border-radius: 20px;">
                                    <i class="fab fa-telegram-plane me-1"></i>
                                    فتح الفيديو في تيليجرام
                                </a>
                                @if(!str_contains($lesson->video_embed_url, 't.me/c/'))
                                    <div class="w-100 mt-3 pt-3 border-top" style="border-color: rgba(255,255,255,0.06) !important;">
                                        <div class="ratio ratio-16x9 mx-auto rounded overflow-hidden" style="max-width: 500px; border: 1px solid rgba(255,255,255,0.1);">
                                            <iframe src="{{ $lesson->video_embed_url }}" frameborder="0" allowfullscreen></iframe>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @elseif($lesson->video_embed_url)
                        <div class="ratio ratio-16x9 rounded overflow-hidden">
                            <iframe src="{{ $lesson->video_embed_url }}" title="lesson video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        </div>
                        @else
                        <div class="ratio ratio-16x9 rounded overflow-hidden">
                            @php
                                $src = null;
                                if ($lesson->video_url) {
                                    $src = $lesson->video_url;
                                } elseif ($lesson->file_path) {
                                    $path = ltrim((string) $lesson->file_path, '/');
                                    if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
                                        $src = $path;
                                    } elseif (\Illuminate\Support\Str::startsWith($path, 'public/storage/')) {
                                        $src = asset(substr($path, strlen('public/')));
                                    } elseif (\Illuminate\Support\Str::startsWith($path, 'storage/')) {
                                        $src = asset($path);
                                    } else {
                                        $src = asset('storage/' . $path);
                                    }
                                }
                            @endphp
                            @if($src)
                            <video class="w-100 h-100" controls playsinline preload="metadata" style="object-fit: cover;">
                                <source src="{{ $src }}" type="video/mp4">
                                متصفحك لا يدعم تشغيل الفيديو.
                            </video>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endif

                    <div class="row g-4">
                        <!-- Lesson Details -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-3">معلومات الدرس</h6>

                            <div class="d-flex flex-column gap-3">
                                <div>
                                    <div class="text-muted small">العنوان</div>
                                    <p class="mb-0">{{ $lesson->title }}</p>
                                </div>

                                <div>
                                    <div class="text-muted small">نوع الملف</div>
                                    <p class="mb-0">
                                        @switch($lesson->file_type)
                                            @case('video')
                                                <span class="text-primary">فيديو</span>
                                                @break
                                            @case('pdf')
                                                <span class="text-danger">PDF</span>
                                                @break
                                            @case('document')
                                                <span class="text-success">مستند</span>
                                                @break
                                            @case('quiz')
                                                <span class="text-purple">اختبار</span>
                                                @break
                                            @default
                                                {{ $lesson->file_type }}
                                        @endswitch
                                    </p>
                                </div>

                                <div>
                                    <div class="text-muted small">الترتيب</div>
                                    <p class="mb-0">{{ $lesson->order_index }}</p>
                                </div>

                                <div>
                                    <div class="text-muted small">الحالة</div>
                                    <p class="mb-0">
                                        @if($lesson->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </p>
                                </div>

                                <div>
                                    <div class="text-muted small">مجاني</div>
                                    <p class="mb-0">
                                        @if($lesson->is_free)
                                            <span class="badge bg-success">نعم</span>
                                        @else
                                            <span class="badge bg-secondary">لا</span>
                                        @endif
                                    </p>
                                </div>

                                @if($lesson->video_duration)
                                <div>
                                    <div class="text-muted small">مدة الفيديو</div>
                                    <p class="mb-0">{{ gmdate('H:i:s', $lesson->video_duration) }}</p>
                                </div>
                                @endif

                                @if($lesson->video_url)
                                <div>
                                    <div class="text-muted small">رابط الفيديو</div>
                                    <p class="mb-0">
                                        <a href="{{ $lesson->video_url }}" target="_blank" class="text-primary text-decoration-none">
                                            {{ $lesson->video_url }}
                                        </a>
                                    </p>
                                </div>
                                @endif

                                @if($lesson->file_path)
                                <div>
                                    <div class="text-muted small">مسار الملف</div>
                                    <p class="mb-0">
                                        <a href="{{ $lesson->file_path }}" target="_blank" class="text-primary text-decoration-none">
                                            {{ $lesson->file_path }}
                                        </a>
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Section and Course Info -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-3">معلومات القسم والكورس</h6>

                            <div class="d-flex flex-column gap-3">
                                <div>
                                    <div class="text-muted small">القسم</div>
                                    <p class="mb-0">{{ $lesson->section->title }}</p>
                                </div>

                                <div>
                                    <div class="text-muted small">الكورس</div>
                                    <p class="mb-0">{{ $lesson->section->course->title }}</p>
                                </div>

                                <div>
                                    <div class="text-muted small">المدرب</div>
                                    <p class="mb-0">{{ $lesson->section->course->instructor->name }}</p>
                                </div>

                                <div>
                                    <div class="text-muted small">تاريخ الإنشاء</div>
                                    <p class="mb-0">{{ optional($lesson->created_at)->format('Y-m-d H:i:s') ?? '-' }}</p>
                                </div>

                                <div>
                                    <div class="text-muted small">آخر تحديث</div>
                                    <p class="mb-0">{{ optional($lesson->updated_at)->format('Y-m-d H:i:s') ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    @if($lesson->content)
                    <div class="mt-4">
                        <h6 class="fw-semibold mb-3">محتوى الدرس</h6>
                        <div class="p-3 rounded" style="background:#f8f9fa;">
                            {!! nl2br(e($lesson->content)) !!}
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.lessons.index', $lesson->section) }}" class="btn btn-secondary">
                            العودة إلى الدروس
                        </a>
                        <a href="{{ route('admin.lessons.edit', $lesson) }}" class="btn btn-primary">
                            تعديل الدرس
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
