@extends('layouts.app')

@section('title', 'دروس القسم - ' . $section->title)

@section('content')
<div class="admin-lessons-index-page">
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
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ $section->title }}
                            </li>
                        </ol>
                    </nav>
                    <h1 class="page-title">
                        <i class="fas fa-play me-3"></i>
                        دروس القسم - {{ $section->title }}
                    </h1>
                </div>
                <div class="col-md-6 text-end">
                    <div class="header-actions">
                        <a href="{{ route('admin.sections.index', $section->course) }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-right me-1"></i>
                            العودة للأقسام
                        </a>
                        <a href="{{ route('admin.lessons.create', $section) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            إضافة درس جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="stat-icon bg-primary">
                                    <i class="fas fa-play text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="card-title mb-0">إجمالي الدروس</h6>
                                <h3 class="text-primary mb-0">{{ $lessons->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="stat-icon bg-success">
                                    <i class="fas fa-video text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="card-title mb-0">دروس الفيديو</h6>
                                <h3 class="text-success mb-0">{{ $lessons->where('file_type', 'video')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="stat-icon bg-info">
                                    <i class="fas fa-file-alt text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="card-title mb-0">المستندات</h6>
                                <h3 class="text-info mb-0">{{ $lessons->where('file_type', 'document')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="stat-icon bg-warning">
                                    <i class="fas fa-gift text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="card-title mb-0">الدروس المجانية</h6>
                                <h3 class="text-warning mb-0">{{ $lessons->where('is_free', true)->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" data-aos="fade-up">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-play me-2"></i>
                            قائمة الدروس
                        </h5>
                        <div class="header-actions">
                            <button class="btn btn-outline-primary btn-sm" onclick="toggleView()">
                                <i class="fas fa-th-large me-1"></i>
                                تبديل العرض
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($lessons->count() > 0)
                            <!-- Card View -->
                            <div id="cardView" class="row">
                                @foreach($lessons as $lesson)
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="lesson-card card border-0 shadow-sm h-100">
                                            <div class="card-header bg-gradient-primary text-white">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="card-title mb-0">
                                                        <i class="{{ $lesson->file_icon }} me-2"></i>
                                                        {{ $lesson->title }}
                                                    </h6>
                                                    <div class="dropdown">
                                                        <button class="btn btn-link text-white p-0" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item" href="{{ route('admin.lessons.show', $lesson) }}">
                                                                <i class="fas fa-eye me-2"></i>عرض
                                                            </a></li>
                                                            <li><a class="dropdown-item" href="{{ route('admin.lessons.edit', $lesson) }}">
                                                                <i class="fas fa-edit me-2"></i>تعديل
                                                            </a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الدرس؟')">
                                                                        <i class="fas fa-trash me-2"></i>حذف
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="lesson-meta mb-3">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <small class="text-muted">نوع الملف</small>
                                                            <div class="fw-bold">
                                                                @switch($lesson->file_type)
                                                                    @case('video')
                                                                        <span class="badge bg-primary">فيديو</span>
                                                                        @break
                                                                    @case('pdf')
                                                                        <span class="badge bg-danger">PDF</span>
                                                                        @break
                                                                    @case('document')
                                                                        <span class="badge bg-success">مستند</span>
                                                                        @break
                                                                    @case('image')
                                                                        <span class="badge bg-info">صورة</span>
                                                                        @break
                                                                    @case('quiz')
                                                                        <span class="badge bg-warning">اختبار</span>
                                                                        @break
                                                                    @default
                                                                        <span class="badge bg-secondary">{{ $lesson->file_type }}</span>
                                                                @endswitch
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">الترتيب</small>
                                                            <div class="fw-bold">{{ $lesson->order_index }}</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if($lesson->video_duration)
                                                    <div class="lesson-duration mb-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock me-1"></i>
                                                            مدة الفيديو: {{ gmdate('H:i:s', $lesson->video_duration) }}
                                                        </small>
                                                    </div>
                                                @endif

                                                @if($lesson->file_size)
                                                    <div class="lesson-size mb-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-file me-1"></i>
                                                            حجم الملف: {{ $lesson->file_size_human }}
                                                        </small>
                                                    </div>
                                                @endif

                                                <div class="lesson-status">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            @if($lesson->is_free)
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-gift me-1"></i>مجاني
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary">
                                                                    <i class="fas fa-lock me-1"></i>مدفوع
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="col-6">
                                                            @if($lesson->is_active)
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-check me-1"></i>نشط
                                                                </span>
                                                            @else
                                                                <span class="badge bg-danger">
                                                                    <i class="fas fa-times me-1"></i>غير نشط
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-light">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        {{ $lesson->created_at->format('Y-m-d') }}
                                                    </small>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('admin.lessons.show', $lesson) }}" class="btn btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.lessons.edit', $lesson) }}" class="btn btn-outline-secondary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Table View (Hidden by default) -->
                            <div id="tableView" class="d-none">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>العنوان</th>
                                                <th>نوع الملف</th>
                                                <th>مدة الفيديو</th>
                                                <th>الترتيب</th>
                                                <th>مجاني</th>
                                                <th>الحالة</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lessons as $lesson)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="{{ $lesson->file_icon }} me-2 text-muted"></i>
                                                            {{ $lesson->title }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @switch($lesson->file_type)
                                                            @case('video')
                                                                <span class="badge bg-primary">فيديو</span>
                                                                @break
                                                            @case('pdf')
                                                                <span class="badge bg-danger">PDF</span>
                                                                @break
                                                            @case('document')
                                                                <span class="badge bg-success">مستند</span>
                                                                @break
                                                            @case('image')
                                                                <span class="badge bg-info">صورة</span>
                                                                @break
                                                            @case('quiz')
                                                                <span class="badge bg-warning">اختبار</span>
                                                                @break
                                                            @default
                                                                <span class="badge bg-secondary">{{ $lesson->file_type }}</span>
                                                        @endswitch
                                                    </td>
                                                    <td>
                                                        @if($lesson->video_duration)
                                                            {{ gmdate('H:i:s', $lesson->video_duration) }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>{{ $lesson->order_index }}</td>
                                                    <td>
                                                        @if($lesson->is_free)
                                                            <span class="badge bg-success">نعم</span>
                                                        @else
                                                            <span class="badge bg-secondary">لا</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($lesson->is_active)
                                                            <span class="badge bg-success">نشط</span>
                                                        @else
                                                            <span class="badge bg-danger">غير نشط</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('admin.lessons.show', $lesson) }}" class="btn btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admin.lessons.edit', $lesson) }}" class="btn btn-outline-secondary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الدرس؟')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="empty-state">
                                    <i class="fas fa-play-circle fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">لا توجد دروس لهذا القسم</h5>
                                    <p class="text-muted">ابدأ بإضافة أول درس لهذا القسم</p>
                                    <a href="{{ route('admin.lessons.create', $section) }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>
                                        إضافة درس جديد
                                    </a>
                                </div>
                            </div>
                        @endif
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

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.lesson-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.lesson-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.lesson-meta {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 1rem;
}

.lesson-status {
    margin-top: 1rem;
}

.empty-state {
    padding: 3rem 1rem;
}

.empty-state i {
    color: #dee2e6;
}

@media (max-width: 768px) {
    .header-actions {
        margin-top: 1rem;
        text-align: center !important;
    }

    .lesson-card {
        margin-bottom: 1rem;
    }
}
</style>

<script>
function toggleView() {
    const cardView = document.getElementById('cardView');
    const tableView = document.getElementById('tableView');
    const toggleBtn = document.querySelector('.header-actions .btn');

    if (cardView.classList.contains('d-none')) {
        cardView.classList.remove('d-none');
        tableView.classList.add('d-none');
        toggleBtn.innerHTML = '<i class="fas fa-th-large me-1"></i>تبديل العرض';
    } else {
        cardView.classList.add('d-none');
        tableView.classList.remove('d-none');
        toggleBtn.innerHTML = '<i class="fas fa-list me-1"></i>تبديل العرض';
    }
}
</script>
@endsection
