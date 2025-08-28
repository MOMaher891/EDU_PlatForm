@extends('layouts.app')

@section('title', 'تصفح الكورسات')

@section('content')
<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-5" data-aos="fade-up">
        <div class="col-12">
            <div class="page-header text-center">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                    <i class="fas fa-book me-1"></i>
                    مكتبة الكورسات
                </span>
                <h1 class="display-4 fw-bold mb-3">اكتشف عالم المعرفة</h1>
                <p class="lead text-muted">أكثر من {{ $courses->total() }} كورس في مختلف المجالات</p>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="row mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('student.courses.index') }}" id="filterForm">
                        <div class="row g-3">
                            <!-- Search -->
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-search me-1"></i>
                                    البحث
                                </label>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control"
                                           placeholder="ابحث في الكورسات..."
                                           value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-folder me-1"></i>
                                    التصنيف
                                </label>
                                <select name="category" class="form-select">
                                    <option value="">جميع التصنيفات</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Level -->
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-layer-group me-1"></i>
                                    المستوى
                                </label>
                                <select name="level" class="form-select">
                                    <option value="">جميع المستويات</option>
                                    <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>مبتدئ</option>
                                    <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>متوسط</option>
                                    <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>متقدم</option>
                                </select>
                            </div>

                            <!-- Price Range -->
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-dollar-sign me-1"></i>
                                    السعر
                                </label>
                                <select name="price" class="form-select">
                                    <option value="">جميع الأسعار</option>
                                    <option value="free" {{ request('price') == 'free' ? 'selected' : '' }}>مجاني</option>
                                    <option value="0-50" {{ request('price') == '0-50' ? 'selected' : '' }}>$0 - $50</option>
                                    <option value="50-100" {{ request('price') == '50-100' ? 'selected' : '' }}>$50 - $100</option>
                                    <option value="100+" {{ request('price') == '100+' ? 'selected' : '' }}>$100+</option>
                                </select>
                            </div>

                            <!-- Sort -->
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-sort me-1"></i>
                                    ترتيب حسب
                                </label>
                                <select name="sort" class="form-select">
                                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>الأحدث</option>
                                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>الاسم</option>
                                    <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>السعر</option>
                                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>الأكثر شعبية</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>
                                        بحث
                                    </button>
                                    <a href="{{ route('student.courses.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-refresh me-1"></i>
                                        إعادة تعيين
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Header -->
    <div class="row mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="results-info">
                    <h5 class="mb-1">
                        عرض {{ $courses->firstItem() ?? 0 }}-{{ $courses->lastItem() ?? 0 }}
                        من أصل {{ $courses->total() }} كورس
                    </h5>
                    @if(request()->hasAny(['search', 'category', 'level', 'price']))
                        <div class="active-filters mt-2">
                            @if(request('search'))
                                <span class="badge bg-primary me-1">
                                    البحث: {{ request('search') }}
                                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="text-white ms-1">×</a>
                                </span>
                            @endif
                            @if(request('category'))
                                <span class="badge bg-success me-1">
                                    التصنيف: {{ $categories->find(request('category'))->name ?? '' }}
                                    <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="text-white ms-1">×</a>
                                </span>
                            @endif
                            @if(request('level'))
                                <span class="badge bg-warning me-1">
                                    المستوى: {{ request('level') == 'beginner' ? 'مبتدئ' : (request('level') == 'intermediate' ? 'متوسط' : 'متقدم') }}
                                    <a href="{{ request()->fullUrlWithQuery(['level' => null]) }}" class="text-white ms-1">×</a>
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="view-options">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="view" id="grid-view" checked>
                        <label class="btn btn-outline-secondary" for="grid-view" title="عرض شبكي">
                            <i class="fas fa-th"></i>
                        </label>
                        <input type="radio" class="btn-check" name="view" id="list-view">
                        <label class="btn btn-outline-secondary" for="list-view" title="عرض قائمة">
                            <i class="fas fa-list"></i>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Grid -->
    <div class="courses-container" id="courses-container">
        <div class="row g-4" id="courses-grid">
            @forelse($courses as $index => $course)
                <div class="col-lg-4 col-md-6 course-item" data-aos="fade-up" data-aos-delay="{{ ($index % 6 + 1) * 100 }}">
                    <div class="card course-card h-100 border-0 shadow-sm">
                        <div class="position-relative overflow-hidden">
                            <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80' }}"
                                 class="card-img-top" alt="{{ $course->title }}" style="height: 250px; object-fit: cover;">

                            <!-- Course Level Badge -->
                            <span class="badge position-absolute top-0 start-0 m-3
                                {{ $course->level == 'beginner' ? 'bg-success' : ($course->level == 'intermediate' ? 'bg-warning' : 'bg-danger') }}">
                                {{ $course->level == 'beginner' ? 'مبتدئ' : ($course->level == 'intermediate' ? 'متوسط' : 'متقدم') }}
                            </span>

                            <!-- Discount Badge -->
                            @if($course->discount_price)
                                <span class="badge bg-danger position-absolute top-0 end-0 m-3">
                                    خصم {{ round((($course->price - $course->discount_price) / $course->price) * 100) }}%
                                </span>
                            @endif

                            <!-- Wishlist Button -->
                            <button class="btn btn-light btn-sm position-absolute" style="top: 50%; right: 15px; transform: translateY(-50%); opacity: 0; transition: all 0.3s ease;" data-wishlist="{{ $course->id }}">
                                <i class="fas fa-heart"></i>
                            </button>

                            <!-- Quick Preview -->
                            <div class="position-absolute bottom-0 start-0 end-0 p-3 bg-gradient-to-t from-black/80 to-transparent text-white opacity-0 transition-opacity">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small>
                                        <i class="fas fa-play-circle me-1"></i>
                                        {{ rand(15, 50) }} درس
                                    </small>
                                    <small>
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $course->duration_hours }}ساعة
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $course->category->name }}</span>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $i <= 4 ? '' : ' opacity-25' }} small"></i>
                                    @endfor
                                    <small class="text-muted ms-1">(4.{{ rand(5, 9) }})</small>
                                </div>
                            </div>

                            <h5 class="card-title fw-bold mb-3 line-clamp-2">{{ $course->title }}</h5>
                            <p class="card-text text-muted mb-3 line-clamp-3">
                                {{ Str::limit($course->short_description, 120) }}
                            </p>

                            <div class="course-meta mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($course->instructor->name) }}&background=6366f1&color=fff"
                                         class="rounded-circle me-2" width="24" height="24" alt="{{ $course->instructor->name }}">
                                    <small class="text-muted">{{ $course->instructor->name }}</small>
                                </div>
                                <div class="d-flex justify-content-between text-muted small">
                                    <span>
                                        <i class="fas fa-users me-1"></i>
                                        {{ rand(50, 500) }} طالب
                                    </span>
                                    <span>
                                        <i class="fas fa-language me-1"></i>
                                        العربية
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="price">
                                    @if($course->getEffectivePrice() == 0)
                                        <span class="text-success fw-bold fs-5">مجاني</span>
                                    @else
                                        <span class="text-primary fw-bold fs-5">${{ $course->getEffectivePrice() }}</span>
                                        @if($course->discount_price)
                                            <span class="text-muted text-decoration-line-through ms-2 small">${{ $course->price }}</span>
                                        @endif
                                    @endif
                                </div>
                                <div class="course-actions">
                                    <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="tooltip" title="إضافة للمفضلة">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="مشاركة">
                                        <i class="fas fa-share"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid">
                                <a href="{{ route('student.courses.show', $course) }}" class="btn btn-primary">
                                    <i class="fas fa-eye me-2"></i>
                                    عرض التفاصيل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12" data-aos="fade-up">
                    <div class="text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-search fa-4x text-muted mb-4"></i>
                            <h4 class="fw-bold mb-3">لا توجد كورسات</h4>
                            <p class="text-muted mb-4">لم يتم العثور على كورسات تطابق معايير البحث الخاصة بك</p>
                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                <a href="{{ route('student.courses.index') }}" class="btn btn-primary">
                                    <i class="fas fa-refresh me-1"></i>
                                    عرض جميع الكورسات
                                </a>
                                <button class="btn btn-outline-secondary" onclick="document.getElementById('filterForm').reset(); document.getElementById('filterForm').submit();">
                                    <i class="fas fa-times me-1"></i>
                                    مسح الفلاتر
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($courses->hasPages())
        <div class="row mt-5" data-aos="fade-up">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    <nav aria-label="صفحات الكورسات">
                        {{ $courses->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </nav>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Filters (Categories) -->
    <div class="row mt-5" data-aos="fade-up">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-filter me-1"></i>
                        تصفح حسب التصنيف
                    </h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('student.courses.index') }}"
                           class="btn btn-sm {{ !request('category') ? 'btn-primary' : 'btn-outline-primary' }}">
                            جميع التصنيفات <span class="badge bg-light text-dark ms-1">{{ $courses->count() }}</span>
                        </a>
                        @foreach($categories->take(8) as $category)
                            <a href="{{ route('student.courses.index', ['category' => $category->id]) }}"
                               class="btn btn-sm {{ request('category') == $category->id ? 'btn-primary' : 'btn-outline-primary' }}">
                                {{ $category->name }}
                                <span class="badge bg-light text-dark ms-1">{{ $category->courses->count() }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .course-card {
        transition: all 0.3s ease;
        border-radius: 16px !important;
    }

    .course-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
    }

    .course-card:hover .position-absolute button,
    .course-card:hover .position-absolute > div {
        opacity: 1 !important;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .list-view .course-item {
        margin-bottom: 1rem;
    }

    .list-view .course-card {
        flex-direction: row;
    }

    .list-view .course-card img {
        width: 200px;
        height: 150px;
        object-fit: cover;
    }

    .empty-state {
        padding: 3rem 1rem;
    }

    .active-filters .badge {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }

    .bg-gradient-to-t {
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
    }

    @media (max-width: 768px) {
        .course-card:hover {
            transform: none;
        }

        .results-info h5 {
            font-size: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // View Toggle
    document.getElementById('list-view').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('courses-container').classList.add('list-view');
            document.getElementById('courses-grid').className = 'row g-2';
            document.querySelectorAll('.course-item').forEach(item => {
                item.className = 'col-12 course-item';
            });
        }
    });

    document.getElementById('grid-view').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('courses-container').classList.remove('list-view');
            document.getElementById('courses-grid').className = 'row g-4';
            document.querySelectorAll('.course-item').forEach(item => {
                item.className = 'col-lg-4 col-md-6 course-item';
            });
        }
    });

    // Clear Search
    document.getElementById('clearSearch').addEventListener('click', function() {
        document.querySelector('input[name="search"]').value = '';
        document.getElementById('filterForm').submit();
    });

    // Auto-submit form on select change
    document.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });

    // Wishlist functionality
    document.querySelectorAll('[data-wishlist]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const courseId = this.getAttribute('data-wishlist');
            const icon = this.querySelector('i');

            // Toggle heart icon
            if (icon.classList.contains('fas')) {
                icon.classList.remove('fas');
                icon.classList.add('far');
                this.classList.remove('btn-danger');
                this.classList.add('btn-light');
            } else {
                icon.classList.remove('far');
                icon.classList.add('fas');
                this.classList.remove('btn-light');
                this.classList.add('btn-danger');
            }

            // Here you would make an AJAX call to add/remove from wishlist
            console.log('Toggle wishlist for course:', courseId);
        });
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Smooth scroll to results after filter
    if (window.location.search) {
        setTimeout(() => {
            document.getElementById('courses-container').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }, 100);
    }
</script>
@endpush
@endsection
