@extends('layouts.app')

@section('title', $course->title . ' - التعلم')

@section('content')
<div class="learning-interface"
     data-course-id="{{ $course->id }}"
     data-current-lesson="{{ $currentLesson->id ?? '' }}"
     data-next-lesson="{{ $nextLesson->id ?? '' }}"
     data-prev-lesson="{{ $prevLesson->id ?? '' }}">

    <!-- Content Display Section -->
    <div class="content-section">
        <div class="content-container">
            @if($currentLesson)
                <!-- Lesson Header -->
                <div class="lesson-header">
                    <div class="lesson-title-section">
                        <h2 class="lesson-title">{{ $currentLesson->title }}</h2>
                        <div class="lesson-meta">
                            @if($currentLesson->video_duration)
                                <span class="duration">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ gmdate('H:i:s', $currentLesson->video_duration) }}
                                </span>
                            @endif
                            @if($currentLesson->file_size)
                                <span class="file-size">
                                    <i class="fas fa-file me-1"></i>
                                    {{ $currentLesson->file_size_human }}
                                </span>
                            @endif
                            <span class="lesson-number">
                                الدرس {{ $currentLesson->order_index }}
                            </span>
                        </div>
                    </div>

                    <!-- Lesson Actions -->
                    <div class="lesson-actions">
                        <button class="action-btn bookmark-btn" title="إضافة إشارة مرجعية">
                            <i class="fas fa-bookmark"></i>
                        </button>
                        <button class="action-btn note-btn" title="إضافة ملاحظة" data-bs-toggle="modal" data-bs-target="#notesModal">
                            <i class="fas fa-sticky-note"></i>
                        </button>
                        @if($currentLesson->hasVideo())
                            <button class="action-btn speed-btn" title="سرعة التشغيل">
                                <i class="fas fa-tachometer-alt"></i>
                                1x
                            </button>
                        @endif
                        <button class="action-btn fullscreen-btn" title="ملء الشاشة">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>

                <!-- Content Display -->
                <div class="lesson-display-content">
                    @if($currentLesson->hasVideo())
                        <!-- Video Content -->
                        <div class="video-container">
                            <div class="video-wrapper">
                                @if($currentLesson->hasFile() && $currentLesson->isVideo())
                                    <!-- Main Video Player -->
                                    <div class="enhanced-video-player">
                                        <!-- Security Overlay -->
                                        <div class="security-overlay" id="securityOverlay">
                                            <div class="security-warning">
                                                <i class="fas fa-shield-alt"></i>
                                                <span>محتوى محمي - لا يُسمح بالتسجيل أو التحميل</span>
                                            </div>
                                        </div>

                                        <video id="lessonVideo"
                                               class="video-player"
                                               controls
                                               preload="metadata"
                                               crossorigin="anonymous"
                                               disablePictureInPicture
                                               controlsList="nodownload noremoteplayback"
                                               oncontextmenu="return false;"
                                               onselectstart="return false;"
                                               ondragstart="return false;"
                                               ondrop="return false;"
                                               oncopy="return false;"
                                               oncut="return false;"
                                               onpaste="return false;">
                                            <source src="{{ route('student.secure.video', $currentLesson) }}" type="{{ $currentLesson->mime_type ?? 'video/mp4' }}">
                                            متصفحك لا يدعم تشغيل الفيديو
                                        </video>

                                        <!-- Video Progress Bar -->
                                        <div class="video-progress-container">
                                            <div class="video-progress-bar" style="width: 0%"></div>
                                        </div>

                                        <!-- Anti-Recording Watermark -->
                                        <div class="watermark-overlay" id="watermarkOverlay">
                                            <div class="watermark-text">
                                                {{ auth()->user()->name ?? 'User' }} - {{ now()->format('Y-m-d H:i') }}
                                            </div>
                                        </div>

                                        <!-- Video Controls Overlay -->
                                        <div class="video-controls-overlay">
                                            <div class="video-info">
                                                <span class="video-title">{{ $currentLesson->title }}</span>
                                                @if($currentLesson->video_duration)
                                                    <span class="video-duration">{{ gmdate('H:i:s', $currentLesson->video_duration) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @elseif($currentLesson->video_embed_url)
                                    <!-- External Embed Video (YouTube / Vimeo / Telegram) -->
                                    <div class="external-video-container">
                                        @if(str_contains($currentLesson->video_embed_url, 't.me'))
                                            <!-- Telegram Video Custom Layout -->
                                            <div class="telegram-video-card p-4 rounded-4 text-center mb-3" 
                                                 style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); 
                                                        border: 1px solid rgba(255,255,255,0.08); 
                                                        color: #f8fafc; 
                                                        min-height: 280px; 
                                                        display: flex; 
                                                        flex-direction: column; 
                                                        align-items: center; 
                                                        justify-content: center;
                                                        border-radius: 16px;">
                                                
                                                <div class="telegram-logo-box mb-3 d-flex align-items-center justify-content-center" 
                                                     style="width: 70px; height: 70px; 
                                                            border-radius: 50%; 
                                                            background: rgba(34, 158, 217, 0.15); 
                                                            box-shadow: 0 0 20px rgba(34, 158, 217, 0.25);">
                                                    <i class="fab fa-telegram-plane fa-2x" style="color: #229ED9;"></i>
                                                </div>
                                                
                                                <h5 class="fw-bold mb-2 text-white">مشاهدة الفيديو على تطبيق تيليجرام</h5>
                                                
                                                <p class="small mb-4 px-3" style="max-width: 480px; line-height: 1.6; color: rgba(255, 255, 255, 0.75);">
                                                    @if(str_contains($currentLesson->video_embed_url, 't.me/c/'))
                                                        هذا الفيديو مستضاف على <strong>قناة تيليجرام خاصة</strong>. لمشاهدة الفيديو، يرجى التأكد من انضمامك للقناة أولاً ثم اضغط على الزر أدناه لفتح الفيديو مباشرة في تطبيق تيليجرام الخاص بك.
                                                    @else
                                                        هذا الفيديو مستضاف على قناة تيليجرام. يمكنك مشاهدته مباشرة عبر تطبيق تيليجرام أو استخدام شاشة المعاينة أدناه.
                                                    @endif
                                                </p>
                                                
                                                <a href="{{ $currentLesson->video_url }}" target="_blank" 
                                                   class="btn px-4 py-2.5 fw-bold d-inline-flex align-items-center gap-2" 
                                                   style="background-color: #229ED9; 
                                                          color: white; 
                                                          border-radius: 30px; 
                                                          box-shadow: 0 4px 15px rgba(34, 158, 217, 0.35); 
                                                          border: none; 
                                                          transition: all 0.2s ease-in-out;">
                                                    <i class="fab fa-telegram-plane fs-5"></i>
                                                    <span>فتح الفيديو في تيليجرام</span>
                                                </a>
                                                
                                                @if(!str_contains($currentLesson->video_embed_url, 't.me/c/'))
                                                    <!-- Preview Iframe for Public Channel Messages -->
                                                    <div class="w-100 mt-4 border-top pt-4" style="border-color: rgba(255,255,255,0.06) !important;">
                                                        <span class="small d-block mb-3" style="color: rgba(255, 255, 255, 0.7);">شاشة معاينة البث:</span>
                                                        <div class="ratio ratio-16x9 mx-auto rounded-3 overflow-hidden shadow-sm" style="max-width: 600px; border: 1px solid rgba(255,255,255,0.1);">
                                                            <iframe src="{{ $currentLesson->video_embed_url }}"
                                                                    frameborder="0"
                                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                                    allowfullscreen
                                                                    class="video-player">
                                                            </iframe>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <iframe src="{{ $currentLesson->video_embed_url }}"
                                                    frameborder="0"
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                    allowfullscreen
                                                    class="video-player">
                                            </iframe>
                                             <div class="external-video-info">
                                                 @if(str_contains($currentLesson->video_embed_url, 'youtube'))
                                                     <i class="fab fa-youtube me-2 text-danger"></i>
                                                     <span>فيديو YouTube</span>
                                                 @elseif(str_contains($currentLesson->video_embed_url, 'vimeo'))
                                                     <i class="fab fa-vimeo-v me-2 text-info"></i>
                                                     <span>فيديو Vimeo</span>
                                                 @elseif(str_contains($currentLesson->video_embed_url, 'drive.google.com'))
                                                     <i class="fab fa-google-drive me-2 text-primary"></i>
                                                     <span>فيديو Google Drive</span>
                                                 @else
                                                     <i class="fas fa-video me-2"></i>
                                                     <span>فيديو خارجي</span>
                                                 @endif
                                             </div>
                                        @endif
                                    </div>
                                @elseif($currentLesson->video_url)
                                    <!-- Direct Video URL -->
                                    <div class="external-video-container">
                                        <video id="lessonVideo" class="video-player" controls preload="metadata">
                                            <source src="{{ $currentLesson->video_url }}" type="video/mp4">
                                            متصفحك لا يدعم تشغيل الفيديو
                                        </video>
                                        <div class="external-video-info">
                                            <i class="fas fa-external-link-alt me-2"></i>
                                            <span>رابط فيديو خارجي</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                    @elseif($currentLesson->hasFile())
                        <!-- File Content -->
                        <div class="file-container">
                            @if($currentLesson->isImage())
                                <!-- Image Display -->
                                <div class="image-wrapper">
                                    <img src="{{ $currentLesson->file_url }}"
                                         alt="{{ $currentLesson->title }}"
                                         class="lesson-image"
                                         onclick="openImageModal(this.src)">
                                </div>
                            @elseif($currentLesson->isPdf())
                                <!-- PDF Display -->
                                <div class="pdf-wrapper">
                                    <iframe src="{{ $currentLesson->file_url }}"
                                            class="pdf-viewer"
                                            frameborder="0">
                                    </iframe>
                                </div>
                            @else
                                <!-- Other File Types -->
                                <div class="file-preview">
                                    <div class="file-info">
                                        <i class="{{ $currentLesson->file_icon }} fa-3x text-muted mb-3"></i>
                                        <h4>{{ $currentLesson->file_name ?? basename($currentLesson->file_path) }}</h4>
                                        @if($currentLesson->file_size)
                                            <p class="text-muted">{{ $currentLesson->file_size_human }}</p>
                                        @endif
                                    </div>
                                    <div class="file-actions">
                                        <a href="{{ $currentLesson->file_url }}"
                                           target="_blank"
                                           class="btn btn-primary">
                                            <i class="fas fa-eye me-2"></i>
                                            عرض الملف
                                        </a>
                                        <a href="{{ route('student.lessons.download', $currentLesson) }}"
                                           class="btn btn-outline-primary download-btn"
                                           data-lesson-id="{{ $currentLesson->id }}">
                                            <i class="fas fa-download me-2"></i>
                                            تحميل الملف
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>



                        <!-- Lesson Navigation - Under file -->
                        <div class="lesson-navigation">
                            <button class="nav-btn prev-btn" id="prevLesson" {{ !$prevLesson ? 'disabled' : '' }}>
                                <i class="fas fa-chevron-left"></i>
                                الدرس السابق
                            </button>

                            <div class="lesson-actions">
                                <button class="action-btn bookmark-btn" title="إضافة إشارة مرجعية">
                                    <i class="fas fa-bookmark"></i>
                                </button>
                                <button class="action-btn note-btn" title="إضافة ملاحظة" data-bs-toggle="modal" data-bs-target="#notesModal">
                                    <i class="fas fa-sticky-note"></i>
                                </button>
                                @if($currentLesson && $currentLesson->hasVideo())
                                    <button class="action-btn speed-btn" title="سرعة التشغيل">
                                        <i class="fas fa-tachometer-alt"></i>
                                        1x
                                    </button>
                                @endif
                                <button class="action-btn fullscreen-btn" title="ملء الشاشة">
                                    <i class="fas fa-expand"></i>
                                </button>
                            </div>

                            <button class="nav-btn next-btn" id="nextLesson" {{ !$nextLesson ? 'disabled' : '' }}>
                                الدرس التالي
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    @endif
                </div>

                        <!-- Lesson Content - Under video -->
                        @if($currentLesson->content)
                            <div class="lesson-text-content mt-4">
                                <div class="content-header">
                                    <h4>محتوى الدرس</h4>
                                </div>
                                <div class="content-body">
                                    {!! $currentLesson->content !!}
                                </div>
                            </div>
                        @endif

                        <!-- Progress Controls (Unified for all media types) -->
                        <div class="progress-controls-unified mb-4 p-4 rounded-4 shadow-sm border" 
                             style="backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div class="progress-status-container">
                                    <span id="lessonStatusBadge" class="badge {{ isset($lessonProgress[$currentLesson->id]) && $lessonProgress[$currentLesson->id] ? 'bg-success' : 'bg-warning' }} px-3 py-2 fs-6">
                                        <i class="fas {{ isset($lessonProgress[$currentLesson->id]) && $lessonProgress[$currentLesson->id] ? 'fa-check-circle' : 'fa-play-circle' }} me-2"></i>
                                        <span id="lessonStatusText">{{ isset($lessonProgress[$currentLesson->id]) && $lessonProgress[$currentLesson->id] ? 'تم إكمال الدرس' : 'قيد التقدم' }}</span>
                                    </span>
                                </div>

                                <div class="progress-actions-container">
                                    <button id="markComplete" class="btn btn-success px-4 py-2 fw-semibold" style="display: {{ isset($lessonProgress[$currentLesson->id]) && $lessonProgress[$currentLesson->id] ? 'none' : 'block' }}">
                                        <i class="fas fa-check me-2"></i>
                                        إكمال الدرس
                                    </button>
                                    <button id="markIncomplete" class="btn btn-outline-danger px-4 py-2 fw-semibold" style="display: {{ isset($lessonProgress[$currentLesson->id]) && $lessonProgress[$currentLesson->id] ? 'block' : 'none' }}">
                                        <i class="fas fa-undo me-2"></i>
                                        إلغاء الإكمال
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Simple Navigation Controls - Under video -->
                        <div class="simple-navigation mt-4">
                            <div class="nav-controls">
                                <button class="btn btn-outline-primary" id="prevLessonBtn" {{ !$prevLesson ? 'disabled' : '' }} onclick="window.learningInterface.navigateToLesson('prev')">
                                    <i class="fas fa-chevron-left me-2"></i>
                                    الدرس السابق
                                </button>

                                <button class="btn btn-primary" id="nextLessonBtn" {{ !$nextLesson ? 'disabled' : '' }} onclick="window.learningInterface.navigateToLesson('next')">
                                    الدرس التالي
                                    <i class="fas fa-chevron-right me-2"></i>
                                </button>
                            </div>
                        </div>
            @else
                <div class="no-lesson-placeholder">
                    <i class="fas fa-play-circle fa-5x text-muted"></i>
                    <p class="mt-3 text-muted">اختر درساً للبدء</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div class="course-sidebar">
        <!-- Course Header -->
        <div class="sidebar-header">
            <div class="course-info">
                <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80' }}"
                     alt="{{ $course->title }}" class="course-thumbnail">
                <div class="course-details">
                    <h6 class="course-title">{{ $course->title }}</h6>
                    <p class="instructor-name">{{ $course->instructor->name }}</p>
                </div>
            </div>

            <!-- Progress Overview -->
            <div class="progress-overview">
                <div class="progress-stats">
                    <span class="completed-lessons">{{ array_sum($lessonProgress) }}</span>
                    <span class="total-lessons">/ {{ $course->getTotalLessons() }}</span>
                    <span class="progress-text">دروس مكتملة</span>
                </div>
                <div class="progress-bar-wrapper">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $enrollment->progress ?? 0 }}%"></div>
                    </div>
                    <span class="progress-percentage">{{ round($enrollment->progress ?? 0) }}%</span>
                </div>
            </div>
        </div>

        <!-- Course Content -->
        <div class="sidebar-content">
            <div class="content-tabs">
                <button class="tab-btn active" data-tab="curriculum">المنهج</button>
                <button class="tab-btn" data-tab="notes">الملاحظات</button>
                <button class="tab-btn" data-tab="resources">الموارد</button>
            </div>

            <!-- Curriculum Tab -->
            <div class="tab-content active" data-tab="curriculum">
                <div class="curriculum-list">
                    @foreach($accessibleSections as $section)
                        @php
                            $isCurrentSection = $currentLesson && $section->lessons->contains('id', $currentLesson->id);
                        @endphp
                        <div class="section-item">
                            <div class="section-header {{ $isCurrentSection ? '' : 'collapsed' }}" 
                                 data-bs-toggle="collapse" 
                                 data-bs-target="#section-{{ $section->id }}"
                                 aria-expanded="{{ $isCurrentSection ? 'true' : 'false' }}">
                                <div class="section-info">
                                    <h6 class="section-title">{{ $section->title }}</h6>
                                    <span class="section-lessons-count">{{ $section->lessons->count() }} درس</span>
                                </div>
                                <div class="section-progress">
                                    @php
                                        $sectionLessons = $section->lessons->pluck('id')->toArray();
                                        $completedInSection = array_intersect_key($lessonProgress, array_flip($sectionLessons));
                                        $sectionProgress = count($sectionLessons) > 0 ? (count($completedInSection) / count($sectionLessons)) * 100 : 0;
                                    @endphp
                                    <span class="progress-text">{{ count($completedInSection) }}/{{ count($sectionLessons) }}</span>
                                    <div class="mini-progress-bar">
                                        <div class="mini-progress-fill" style="width: {{ $sectionProgress }}%"></div>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-down section-toggle"></i>
                            </div>

                            <div class="section-lessons collapse {{ $isCurrentSection ? 'show' : '' }}" id="section-{{ $section->id }}">
                                <div class="lessons-list">
                                    @foreach($section->lessons as $lesson)
                                        <a href="{{ route('student.courses.learn', ['course' => $course, 'lesson' => $lesson->id]) }}"
                                           class="lesson-item {{ $currentLesson && $currentLesson->id == $lesson->id ? 'active' : '' }} {{ isset($lessonProgress[$lesson->id]) && $lessonProgress[$lesson->id] ? 'completed' : '' }} text-decoration-none"
                                           data-lesson-id="{{ $lesson->id }}"
                                           style="color: inherit; text-decoration: none;">
                                            <div class="lesson-content">
                                                <div class="lesson-icon">
                                                    @if(isset($lessonProgress[$lesson->id]) && $lessonProgress[$lesson->id])
                                                        <i class="fas fa-check-circle text-success"></i>
                                                    @elseif($lesson->file_type == 'video')
                                                        <i class="fas fa-play-circle"></i>
                                                    @elseif($lesson->file_type == 'pdf')
                                                        <i class="fas fa-file-pdf text-danger"></i>
                                                    @else
                                                        <i class="fas fa-file-alt"></i>
                                                    @endif
                                                </div>

                                                <div class="lesson-info">
                                                    <h6 class="lesson-title">{{ $lesson->title }}</h6>
                                                    <div class="lesson-meta">
                                                        @if($lesson->video_duration)
                                                            <span class="duration">{{ gmdate('H:i:s', $lesson->video_duration) }}</span>
                                                        @endif
                                                        @if(isset($lessonWatchTimes[$lesson->id]) && $lessonWatchTimes[$lesson->id] > 0)
                                                            <span class="watch-progress">
                                                                <i class="fas fa-clock"></i>
                                                                {{ gmdate('H:i:s', $lessonWatchTimes[$lesson->id]) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="lesson-link">
                                                <i class="fas fa-external-link-alt"></i>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Notes Tab -->
            <div class="tab-content" data-tab="notes">
                <div class="notes-section">
                    <div class="notes-header">
                        <h6>ملاحظات الدرس</h6>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#notesModal">
                            <i class="fas fa-plus"></i>
                            إضافة ملاحظة
                        </button>
                    </div>
                    <div class="notes-list" id="notesList">
                        <!-- Notes will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Resources Tab -->
            <div class="tab-content" data-tab="resources">
                <div class="resources-section">
                    <h6>الموارد الإضافية</h6>
                    <div class="resources-list">
                        @if($currentLesson && $currentLesson->hasFile())
                            <div class="resource-item">
                                <i class="fas fa-file me-2"></i>
                                <span>{{ $currentLesson->file_name ?? 'ملف الدرس' }}</span>
                                <a href="{{ route('student.lessons.download', $currentLesson) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        @endif

                        @if($course->requirements)
                            <div class="resource-item">
                                <h6>المتطلبات المسبقة</h6>
                                <ul>
                                    @foreach($course->requirements as $requirement)
                                        <li>{{ $requirement }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notes Modal -->
<div class="modal fade" id="notesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة ملاحظة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea id="lessonNote" class="form-control" rows="5" placeholder="اكتب ملاحظاتك هنا..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-outline-danger" id="clearNote">مسح</button>
                <button type="button" class="btn btn-primary" id="saveNote">حفظ</button>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">عرض الصورة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/learning-interface.js') }}"></script>
<script>
// Comprehensive Video Security System
class VideoSecurity {
    constructor() {
        this.video = null;
        this.init();
    }

    init() {
        this.video = document.getElementById('lessonVideo');
        if (this.video) {
            // Add a small delay to ensure page is fully loaded
            setTimeout(() => {
                this.setupSecurityMeasures();
                this.startSecurityMonitoring();
            }, 1000);
        } else {
            console.log('No video element found, security system not initialized');
        }
    }

    setupSecurityMeasures() {
        try {
            // Disable right-click context menu and copying/selection specifically on the video element itself
            if (this.video) {
                this.video.addEventListener('contextmenu', e => e.preventDefault());
                this.video.addEventListener('dragstart', e => e.preventDefault());
                this.video.addEventListener('drop', e => e.preventDefault());
                this.video.addEventListener('selectstart', e => e.preventDefault());
                this.video.addEventListener('copy', e => e.preventDefault());
                this.video.addEventListener('cut', e => e.preventDefault());
                this.video.addEventListener('paste', e => e.preventDefault());
            }

            // Disable keyboard shortcuts for developer tools
            @if($appSettings->block_devtools ?? false)
            document.addEventListener('keydown', e => this.handleKeySecurity(e));
            @endif

            // Add watermark
            this.addWatermark();

            console.log('Video security measures initialized successfully');
        } catch (error) {
            console.error('Error setting up security measures:', error);
        }
    }

    handleKeySecurity(e) {
        // Block common developer tools shortcuts
        const blockedKeys = [
            'F12', 'Ctrl+Shift+I', 'Ctrl+Shift+J', 'Ctrl+U', 'Ctrl+S',
            'Ctrl+Shift+C', 'Ctrl+Shift+E', 'Ctrl+Shift+M', 'Ctrl+Shift+P', 
            'Ctrl+Shift+O', 'Ctrl+Shift+K', 'Ctrl+P'
        ];

        const keyCombo = this.getKeyCombo(e);
        if (blockedKeys.includes(keyCombo)) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }

    getKeyCombo(e) {
        let combo = '';
        if (e.ctrlKey) combo += 'Ctrl+';
        if (e.shiftKey) combo += 'Shift+';
        if (e.altKey) combo += 'Alt+';
        if (e.metaKey) combo += 'Meta+';
        combo += e.key;
        return combo;
    }

    addWatermark() {
        // Add dynamic watermark with user info and timestamp
        const watermark = document.getElementById('watermarkOverlay');
        if (watermark) {
            setInterval(() => {
                const now = new Date();
                const user = '{{ auth()->user()->name ?? "User" }}';
                watermark.querySelector('.watermark-text').textContent =
                    `${user} - ${now.toLocaleDateString('ar-SA')} ${now.toLocaleTimeString('ar-SA')}`;
            }, 1000);
        }
    }


    startSecurityMonitoring() {
        // Continuous security monitoring
        setInterval(() => {
            this.checkVideoIntegrity();
        }, 5000);
    }

    checkVideoIntegrity() {
        if (this.video) {
            // Check if video source has been tampered with
            const currentSrc = this.video.currentSrc || this.video.src;
            const expectedSrc = '{{ route('student.secure.video', $currentLesson) }}';

            if (currentSrc !== expectedSrc) {
                console.warn('Video source tampering detected');
                this.video.pause();
                this.video.src = expectedSrc;
            }
        }
    }
}

// Initialize learning interface with current lesson data
document.addEventListener('DOMContentLoaded', function() {
    if (window.learningInterface) {
        window.learningInterface.currentLesson = {{ $currentLesson->id ?? 'null' }};
        window.learningInterface.nextLessonId = {{ $nextLesson->id ?? 'null' }};
        window.learningInterface.prevLessonId = {{ $prevLesson->id ?? 'null' }};
    }

    // Initialize video security system
    window.videoSecurity = new VideoSecurity();

    // Video initialization
    console.log('Learning interface loaded with security measures');

    // Check if video element exists and set up event listeners
    const video = document.getElementById('lessonVideo');
    if (video) {
        console.log('Video element found with security protection');

        // Add error handling
        video.addEventListener('error', function(e) {
            console.error('Video error occurred:', e);
            console.error('Video error details:', video.error);
        });

        // Add load event
        video.addEventListener('loadeddata', function() {
            console.log('Video data loaded successfully');
        });

        // Add canplay event
        video.addEventListener('canplay', function() {
            console.log('Video can start playing');
        });
    }
});

// Image modal function
function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

// Keyboard shortcuts info
document.addEventListener('keydown', function(e) {
    if (e.key === '?') {
        e.preventDefault();
        showKeyboardShortcuts();
    }
});

function showKeyboardShortcuts() {
    const shortcuts = [
        { key: 'Space', action: 'تشغيل/إيقاف الفيديو' },
        { key: '← →', action: 'الدرس السابق/التالي' },
        { key: 'M', action: 'كتم/إلغاء كتم الصوت' },
        { key: 'F', action: 'ملء الشاشة' },
        { key: '?', action: 'عرض اختصارات لوحة المفاتيح' }
    ];

    let message = 'اختصارات لوحة المفاتيح:\n\n';
    shortcuts.forEach(shortcut => {
        message += `${shortcut.key}: ${shortcut.action}\n`;
    });

    alert(message);
}

// Fullscreen toggle function
function toggleFullscreen() {
    const video = document.getElementById('lessonVideo');
    if (video) {
        if (document.fullscreenElement) {
            document.exitFullscreen();
        } else {
            video.requestFullscreen().catch(err => {
                console.error('Error attempting to enable fullscreen:', err);
            });
        }
    }
}

// Additional security measures
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        // Pause video when tab is not active
        const video = document.getElementById('lessonVideo');
        if (video && !video.paused) {
            video.pause();
        }
    }
});

// Prevent print
window.addEventListener('beforeprint', function(e) {
    e.preventDefault();
    alert('لا يُسمح بطباعة هذا المحتوى');
});

// Prevent save page
window.addEventListener('beforeunload', function(e) {
    // This will show a warning when trying to leave the page
    if (document.getElementById('lessonVideo') && !document.getElementById('lessonVideo').paused) {
        e.preventDefault();
        e.returnValue = 'هل أنت متأكد من أنك تريد مغادرة الصفحة؟';
    }
});

// Test function for security system
function testSecuritySystem() {
    if (window.videoSecurity) {
        console.log('Testing security system...');
        console.log('Video element:', window.videoSecurity.video);
        console.log('Last user interaction:', window.videoSecurity.lastUserInteraction);
        console.log('Current time:', Date.now());

        // Test security alert
        window.videoSecurity.showSecurityAlert('اختبار النظام الأمني - هذا مجرد اختبار');

        // Test danger page creation
        setTimeout(() => {
            window.videoSecurity.createDangerPage();
        }, 2000);
    } else {
        console.log('Security system not initialized');
        alert('النظام الأمني لم يتم تهيئته بعد');
    }
}
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/learning-interface.css') }}">
<style>
/* Enhanced Video Player Styles */
.enhanced-video-player {
    position: relative;
    background: #000;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
}

.enhanced-video-player .video-player {
    width: 100%;
    height: auto;
    min-height: 450px;
    display: block;
}

/* Security Overlay */
.security-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    background: linear-gradient(180deg, rgba(220, 53, 69, 0.9) 0%, transparent 30%);
    padding: 15px;
    z-index: 10;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.security-overlay:hover {
    opacity: 1;
}

.security-warning {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #fff;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.8);
}

.security-warning i {
    font-size: 1.2em;
    color: #ffc107;
}

/* Enhanced Security Alerts */
.security-alert {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    max-width: 400px;
    animation: slideInRight 0.3s ease-out;
}

.security-alert-content {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(220, 53, 69, 0.4);
    display: flex;
    align-items: center;
    gap: 12px;
    border-left: 4px solid #ffc107;
}

.security-alert-content i {
    font-size: 1.2em;
    color: #ffc107;
    flex-shrink: 0;
}

.security-alert-content span {
    flex: 1;
    font-weight: 500;
    line-height: 1.4;
}

.security-alert-content button {
    background: none;
    border: none;
    color: white;
    font-size: 1.5em;
    cursor: pointer;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background-color 0.2s ease;
}

.security-alert-content button:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

/* Danger Page Overlay */
.danger-page-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    animation: fadeIn 0.5s ease-out;
}

.danger-content {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 600px;
    width: 100%;
    overflow: hidden;
    animation: scaleIn 0.5s ease-out;
}

.danger-header {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    padding: 30px;
    text-align: center;
    position: relative;
}

.danger-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.danger-icon {
    font-size: 3em;
    color: #ffc107;
    margin-bottom: 15px;
    display: block;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.danger-title {
    font-size: 2em;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.danger-message {
    padding: 30px;
    color: #333;
}

.danger-message h2 {
    color: #dc3545;
    font-size: 1.5em;
    margin-bottom: 20px;
    text-align: center;
}

.danger-message p {
    font-size: 1.1em;
    line-height: 1.6;
    margin-bottom: 25px;
    text-align: center;
    color: #666;
}

.danger-details, .danger-consequences {
    margin-bottom: 25px;
}

.danger-details h3, .danger-consequences h3 {
    color: #dc3545;
    font-size: 1.2em;
    margin-bottom: 15px;
    border-bottom: 2px solid #f8f9fa;
    padding-bottom: 8px;
}

.danger-details ul, .danger-consequences ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.danger-details li, .danger-consequences li {
    padding: 8px 0;
    border-left: 3px solid #dc3545;
    padding-left: 15px;
    margin-bottom: 8px;
    background: #f8f9fa;
    border-radius: 0 4px 4px 0;
    position: relative;
}

.danger-details li::before, .danger-consequences li::before {
    content: '⚠️';
    margin-right: 10px;
    font-size: 0.9em;
}

.danger-actions {
    text-align: center;
    padding: 20px 30px 30px;
}

.danger-actions .btn {
    padding: 15px 30px;
    font-size: 1.1em;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.danger-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
}

.danger-footer {
    background: #f8f9fa;
    padding: 20px 30px;
    text-align: center;
    border-top: 1px solid #e9ecef;
}

.danger-footer p {
    margin: 0;
    color: #6c757d;
    font-size: 0.9em;
}

/* Watermark Overlay */
.watermark-overlay {
    position: absolute;
    bottom: 20px;
    right: 20px;
    z-index: 5;
    pointer-events: none;
}

.watermark-text {
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 0.9em;
    font-weight: 500;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(4px);
}

/* Animations */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes scaleIn {
    from {
        transform: scale(0.9);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .danger-content {
        margin: 20px;
        max-width: none;
    }

    .danger-header {
        padding: 20px;
    }

    .danger-title {
        font-size: 1.5em;
    }

    .danger-message {
        padding: 20px;
    }

    .security-alert {
        right: 10px;
        left: 10px;
        max-width: none;
    }
}

/* Enhanced Security Features */
.security-features {
    position: relative;
}

.security-features::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 49%, rgba(220, 53, 69, 0.1) 50%, transparent 51%);
    background-size: 20px 20px;
    pointer-events: none;
    z-index: 1;
}

/* Anti-copy protection */
.learning-interface {
    -webkit-user-select: text;
    -moz-user-select: text;
    -ms-user-select: text;
    user-select: text;
}

.learning-interface *:not(.learning-interface) {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

/* Enhanced button styles for security actions */
.security-btn {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}

.security-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
}

.security-btn:active {
    transform: translateY(0);
}

/* Enhanced Video Container */
.video-container {
    margin-bottom: 30px;
}

.video-wrapper {
    position: relative;
    width: 100%;
    background: #000;
    border-radius: 12px;
    overflow: hidden;
}

.video-player {
    width: 100%;
    height: auto;
    min-height: 450px;
}

/* Video Controls Overlay */
.video-controls-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0.7) 0%, transparent 30%);
    padding: 20px;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 15;
}

.enhanced-video-player:hover .video-controls-overlay {
    opacity: 1;
}

.video-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
}

.video-title {
    font-weight: 600;
    font-size: 1.1rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
}

.video-duration {
    background: rgba(0,0,0,0.6);
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.9rem;
    font-weight: 500;
}

.video-fallback {
    background: rgba(255,255,255,0.95);
    padding: 15px;
    margin-top: 15px;
    border-radius: 8px;
    text-align: center;
    border: 1px solid #e9ecef;
}

.fallback-text {
    margin: 0;
    color: #6c757d;
    font-size: 0.9rem;
}

.fallback-link {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
}

.fallback-link:hover {
    text-decoration: underline;
}

/* External Video Containers */
.external-video-container {
    position: relative;
    background: #f8f9fa;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e9ecef;
}

.external-video-container .video-player {
    width: 100%;
    height: 450px;
    border: none;
}

.external-video-info {
    background: #e9ecef;
    padding: 10px 15px;
    text-align: center;
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: 500;
}

/* Video Actions Bar */
.video-actions-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    margin-top: 20px;
    border-top: 1px solid #e9ecef;
}

.video-actions-left,
.video-actions-right {
    display: flex;
    gap: 10px;
}

.video-actions-bar .btn {
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.video-actions-bar .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Responsive Design */
@media (max-width: 768px) {
    .enhanced-video-player .video-player,
    .video-player {
        min-height: 300px;
    }

    .external-video-container .video-player {
        height: 300px;
    }

    .video-actions-bar {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }

    .video-actions-left,
    .video-actions-right {
        justify-content: center;
    }
}

/* Animation for video loading */
.video-player {
    transition: opacity 0.3s ease;
}

.video-player:not([src]) {
    opacity: 0.7;
}

/* Additional Security Features */
.enhanced-video-player::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 49%, rgba(220, 53, 69, 0.1) 50%, transparent 51%);
    background-size: 20px 20px;
    pointer-events: none;
    z-index: 1;
    opacity: 0.3;
}

.enhanced-video-player::after {
    content: '🔒';
    position: absolute;
    top: 10px;
    left: 10px;
    font-size: 1.5rem;
    color: #dc3545;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    z-index: 20;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Disable text selection on video container */
.enhanced-video-player {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

/* Hide video controls on mobile when not interacting */
@media (max-width: 768px) {
    .enhanced-video-player .video-player::-webkit-media-controls {
        opacity: 0;
        transition: opacity 0.3s;
    }

    .enhanced-video-player:hover .video-player::-webkit-media-controls {
        opacity: 1;
    }
}

/* Security indicator for external videos */
.external-video-container::before {
    content: '⚠️';
    position: absolute;
    top: 10px;
    left: 10px;
    font-size: 1.2rem;
    z-index: 10;
}

/* Enhanced button styles */
.btn-outline-primary {
    border-color: #007bff;
    color: #007bff;
}

.btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.btn-outline-secondary {
    border-color: #6c757d;
    color: #6c757d;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.content-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: white;
    border-radius: 8px;
    margin: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.content-container {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
}

.lesson-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e9ecef;
}

.lesson-title {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-size: 1.5rem;
}

.lesson-meta {
    display: flex;
    gap: 15px;
    color: #6c757d;
    font-size: 0.9rem;
}

.lesson-actions {
    display: flex;
    gap: 10px;
}

.action-btn {
    background: none;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 8px 12px;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.2s;
}

.action-btn:hover {
    background: #f8f9fa;
    color: #495057;
}

.file-container {
    margin-bottom: 20px;
}

.image-wrapper img {
    max-width: 100%;
    border-radius: 8px;
    cursor: pointer;
}

.pdf-wrapper {
    width: 100%;
    height: 600px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
}

.pdf-viewer {
    width: 100%;
    height: 100%;
}

.file-preview {
    text-align: center;
    padding: 40px;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
}

.file-actions {
    margin-top: 20px;
    display: flex;
    gap: 10px;
    justify-content: center;
}

.lesson-text-content {
    margin-top: 20px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.progress-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.progress-status .status {
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: 500;
}

.status.completed {
    color: #28a745;
}

.status.in-progress {
    color: #ffc107;
}

.lesson-navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.nav-btn {
    background: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.nav-btn:hover:not(:disabled) {
    background: #0056b3;
}

.nav-btn:disabled {
    background: #6c757d;
    cursor: not-allowed;
}

/* Sidebar Styles */
.course-sidebar {
    width: 350px;
    background: white;
    border-radius: 8px;
    margin: 20px 20px 20px 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    height: calc(100% - 40px);
    max-height: calc(100% - 40px);
    overflow: hidden;
}

.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
}

.course-info {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.course-thumbnail {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 6px;
}

.course-title {
    margin: 0 0 5px 0;
    font-size: 1rem;
}

.instructor-name {
    margin: 0;
    color: #6c757d;
    font-size: 0.9rem;
}

.progress-overview {
    text-align: center;
}

.progress-stats {
    margin-bottom: 10px;
}

.completed-lessons {
    font-size: 1.5rem;
    font-weight: bold;
    color: #007bff;
}

.total-lessons {
    color: #6c757d;
}

.progress-bar-wrapper {
    position: relative;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #007bff, #28a745);
    transition: width 0.3s;
}

.progress-percentage {
    position: absolute;
    top: -20px;
    right: 0;
    font-size: 0.8rem;
    color: #6c757d;
}

.sidebar-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
    overflow: hidden;
}

.content-tabs {
    display: flex;
    border-bottom: 1px solid #e9ecef;
}

.tab-btn {
    flex: 1;
    background: none;
    border: none;
    padding: 15px;
    cursor: pointer;
    transition: all 0.2s;
}

.tab-btn.active {
    background: #007bff;
    color: white;
}

.tab-content {
    display: none;
    flex: 1;
    padding: 20px;
    overflow-y: auto;
}

.tab-content.active {
    display: block;
}

/* Curriculum Styles */
.section-item {
    margin-bottom: 15px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.section-header:hover {
    background: #e9ecef;
}

.section-title {
    margin: 0;
    font-size: 1rem;
}

.section-lessons-count {
    font-size: 0.8rem;
    color: #6c757d;
}

.section-progress {
    display: flex;
    align-items: center;
    gap: 10px;
}

.mini-progress-bar {
    width: 60px;
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    overflow: hidden;
}

.mini-progress-fill {
    height: 100%;
    background: #28a745;
    transition: width 0.3s;
}

.section-lessons {
    padding: 10px 0;
}

.lesson-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    margin: 5px 0;
    border-radius: 6px;
    transition: all 0.2s;
    cursor: pointer;
}

.lesson-item:hover {
    background: #f8f9fa;
}

.lesson-item.active {
    background: #e3f2fd;
    border-left: 3px solid #007bff;
}

.lesson-item.completed {
    background: #f8fff9;
}

.lesson-item.completed .lesson-icon i {
    color: #28a745;
}

.lesson-content {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
}

.lesson-icon {
    width: 20px;
    text-align: center;
}

.lesson-title {
    margin: 0;
    font-size: 0.9rem;
}

.lesson-meta {
    display: flex;
    gap: 10px;
    font-size: 0.8rem;
    color: #6c757d;
}

.lesson-link {
    color: #6c757d;
    text-decoration: none;
}

.lesson-link:hover {
    color: #007bff;
}

/* Notes Styles */
.notes-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.notes-list {
    max-height: 300px;
    overflow-y: auto;
}

/* Resources Styles */
.resources-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.resource-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 6px;
}

/* Notifications */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 6px;
    color: white;
    z-index: 9999;
    transform: translateX(100%);
    transition: transform 0.3s;
}

.notification.show {
    transform: translateX(0);
}

.notification-success {
    background: #28a745;
}

.notification-error {
    background: #dc3545;
}

/* Completion Message */
.completion-message {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.completion-content {
    background: white;
    padding: 30px;
    border-radius: 8px;
    text-align: center;
    max-width: 400px;
}

.completion-content i {
    font-size: 3rem;
    margin-bottom: 15px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .learning-interface {
        flex-direction: column;
        height: auto;
    }

    .course-sidebar {
        width: 100%;
        margin: 0 20px 20px 20px;
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
    }

    .sidebar-content {
        height: auto !important;
        overflow: visible !important;
    }

    .tab-content {
        overflow-y: visible !important;
    }

    .lesson-header {
        flex-direction: column;
        gap: 15px;
    }

    .lesson-actions {
        align-self: flex-end;
    }
}
</style>
@endpush
