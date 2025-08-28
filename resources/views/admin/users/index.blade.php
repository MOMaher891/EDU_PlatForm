@extends('layouts.app')

@section('title', 'إدارة المستخدمين')

@section('content')
<div class="admin-users-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="page-title">
                        <i class="fas fa-users me-3"></i>
                        إدارة المستخدمين
                    </h1>
                    <p class="page-subtitle">إدارة وتنظيم جميع مستخدمي المنصة</p>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-plus me-2"></i>
                        إضافة مستخدم جديد
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-primary" data-aos="fade-up">
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $users->total() }}</h3>
                        <p class="stats-label">إجمالي المستخدمين</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-success" data-aos="fade-up" data-aos-delay="100">
                    <div class="stats-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $users->where('role', 'student')->count() }}</h3>
                        <p class="stats-label">الطلاب</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-warning" data-aos="fade-up" data-aos-delay="200">
                    <div class="stats-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $users->where('role', 'instructor')->count() }}</h3>
                        <p class="stats-label">المدربين</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-info" data-aos="fade-up" data-aos-delay="300">
                    <div class="stats-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $users->where('role', 'admin')->count() }}</h3>
                        <p class="stats-label">المديرين</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="filters-section mb-4" data-aos="fade-up">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">البحث</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" name="search"
                                       value="{{ request('search') }}"
                                       placeholder="البحث بالاسم أو البريد الإلكتروني">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">الدور</label>
                            <select class="form-select" name="role">
                                <option value="">جميع الأدوار</option>
                                <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>طالب</option>
                                <option value="instructor" {{ request('role') == 'instructor' ? 'selected' : '' }}>مدرب</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>مدير</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">الحالة</label>
                            <select class="form-select" name="is_active">
                                <option value="">جميع الحالات</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">الترتيب</label>
                            <select class="form-select" name="sort">
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>تاريخ التسجيل</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>الاسم</option>
                                <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>البريد الإلكتروني</option>
                                <option value="role" {{ request('sort') == 'role' ? 'selected' : '' }}>الدور</option>
                                <option value="is_active" {{ request('sort') == 'is_active' ? 'selected' : '' }}>الحاله</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="form-label">طريقه الترتيب</label>
                            <select name="sort_type" class="form-select">
                                <option value="desc" {{ request('sort_type') == 'desc' ? 'selected' : '' }}>تنازلي</option>
                                <option value="asc" {{ request('sort_type') == 'asc' ? 'selected' : '' }}>تصاعدي</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex" style="justify-content: space-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>
                                    تطبيق
                                </button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-danger">
                                    <i class="fa-regular fa-circle-xmark"></i>
                                    إلغاء
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="users-table-section" data-aos="fade-up">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>
                                قائمة المستخدمين
                            </h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="table-actions">
                                <button class="btn btn-outline-primary btn-sm" onclick="exportUsers()">
                                    <i class="fas fa-download me-1"></i>
                                    تصدير
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="bulkActions()">
                                    <i class="fas fa-tasks me-1"></i>
                                    إجراءات مجمعة
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                            </div>
                                        </th>
                                        <th>المستخدم</th>
                                        <th>الدور</th>
                                        <th>الكورسات</th>
                                        <th>تاريخ التسجيل</th>
                                        <th>الحالة</th>
                                        <th width="120">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr class="user-row" data-user-id="{{ $user->id }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input user-checkbox" type="checkbox" value="{{ $user->id }}">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="user-info">
                                                    <div class="user-avatar">
                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366f1&color=fff&size=40"
                                                             alt="{{ $user->name }}" class="rounded-circle">
                                                    </div>
                                                    <div class="user-details">
                                                        <h6 class="user-name">{{ $user->name }}</h6>
                                                        <p class="user-email">{{ $user->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="role-badge role-{{ $user->role }}">
                                                    @switch($user->role)
                                                        @case('admin')
                                                            <i class="fas fa-user-shield me-1"></i>
                                                            مدير
                                                            @break
                                                        @case('instructor')
                                                            <i class="fas fa-chalkboard-teacher me-1"></i>
                                                            مدرب
                                                            @break
                                                        @case('student')
                                                            <i class="fas fa-user-graduate me-1"></i>
                                                            طالب
                                                            @break
                                                    @endswitch
                                                </span>
                                            </td>
                                            <td>
                                                <div class="courses-stats">
                                                    @if($user->role == 'student')
                                                        <span class="stat-item">
                                                            <i class="fas fa-book text-primary me-1"></i>
                                                            {{ $user->enrollments_count }} مسجل
                                                        </span>
                                                    @elseif($user->role == 'instructor')
                                                        <span class="stat-item">
                                                            <i class="fas fa-chalkboard text-success me-1"></i>
                                                            {{ $user->instructed_courses_count }} كورس
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="date-info">
                                                    <span class="date">{{ $user->created_at->format('Y/m/d') }}</span>
                                                    <small class="text-muted d-block">{{ $user->created_at->diffForHumans() }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-{{ $user->deleted_at ? 'inactive' : 'active' }}">
                                                    @if($user->deleted_at)
                                                        <i class="fas fa-times-circle me-1"></i>
                                                        غير نشط
                                                    @else
                                                        <i class="fas fa-check-circle me-1"></i>
                                                        نشط
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('admin.users.show', $user) }}"
                                                       class="btn btn-sm btn-outline-primary" title="عرض">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.users.edit',$user) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($user->id !== auth()->id())
                                                        @if($user->deleted_at)
                                                            <a href="{{ route('admin.users.destroy',$user) }}"class="btn btn-sm btn-outline-success" title="إسترجاع">
                                                                <i class="fa-solid fa-rotate-right"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ route('admin.users.destroy',$user) }}"class="btn btn-sm btn-outline-danger" title="حذف">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="card-footer bg-white">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <p class="text-muted mb-0">
                                        عرض {{ $users->firstItem() }} إلى {{ $users->lastItem() }}
                                        من أصل {{ $users->total() }} مستخدم
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    {{ $users->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5>لا يوجد مستخدمين</h5>
                            <p class="text-muted">لم يتم العثور على أي مستخدمين بالمعايير المحددة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة مستخدم جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addUserForm" method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">الاسم الكامل</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">كلمة المرور</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تأكيد كلمة المرور</label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الدور</label>
                            <select class="form-select" name="role" required>
                                <option value="">اختر الدور</option>
                                <option value="student">طالب</option>
                                <option value="instructor">مدرب</option>
                                <option value="admin">مدير</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="tel" class="form-control" name="phone">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        إضافة المستخدم
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل المستخدم</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">الاسم الكامل</label>
                            <input type="text" class="form-control" name="name" id="editName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" name="email" id="editEmail" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">كلمة المرور الجديدة</label>
                            <input type="password" class="form-control" name="password">
                            <small class="text-muted">اتركه فارغاً إذا كنت لا تريد تغيير كلمة المرور</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تأكيد كلمة المرور</label>
                            <input type="password" class="form-control" name="password_confirmation">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الدور</label>
                            <select class="form-select" name="role" id="editRole" required>
                                <option value="student">طالب</option>
                                <option value="instructor">مدرب</option>
                                <option value="admin">مدير</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="tel" class="form-control" name="phone" id="editPhone">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.admin-users-page {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding-bottom: 50px;
}

.page-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 30px 0;
    margin-bottom: 30px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.page-title {
    color: white;
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.page-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    margin: 0;
}

.stats-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.stats-primary::before { background: linear-gradient(90deg, #6366f1, #8b5cf6); }
.stats-success::before { background: linear-gradient(90deg, #10b981, #34d399); }
.stats-warning::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.stats-info::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 20px;
}

.stats-primary .stats-icon { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
.stats-success .stats-icon { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.stats-warning .stats-icon { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.stats-info .stats-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }

.stats-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 5px 0;
}

.stats-label {
    color: #6b7280;
    font-weight: 500;
    margin: 0;
}

.filters-section .card,
.users-table-section .card {
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar img {
    width: 40px;
    height: 40px;
}

.user-name {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 4px 0;
}

.user-email {
    color: #6b7280;
    font-size: 0.9rem;
    margin: 0;
}

.role-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
}

.role-admin {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.role-instructor {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.role-student {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-active {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.status-inactive {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.courses-stats .stat-item {
    display: inline-flex;
    align-items: center;
    font-size: 0.9rem;
    color: #374151;
}

.date-info .date {
    font-weight: 500;
    color: #374151;
}

.action-buttons {
    display: flex;
    gap: 5px;
}

.action-buttons .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

.table-actions {
    display: flex;
    gap: 10px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.user-row {
    transition: all 0.3s ease;
}

.user-row:hover {
    background: rgba(99, 102, 241, 0.02);
    transform: translateX(2px);
}

@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
    }

    .stats-card {
        margin-bottom: 20px;
    }

    .table-responsive {
        font-size: 0.9rem;
    }

    .action-buttons {
        flex-direction: column;
    }

    .user-info {
        flex-direction: column;
        text-align: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Edit user function
function editUser(userId) {
    fetch(`/admin/users/${userId}/edit`)
        .then(response => response.json())
        .then(user => {
            document.getElementById('editName').value = user.name;
            document.getElementById('editEmail').value = user.email;
            document.getElementById('editRole').value = user.role;
            document.getElementById('editPhone').value = user.phone || '';
            document.getElementById('editUserForm').action = `/admin/users/${userId}`;

            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في تحميل بيانات المستخدم');
        });
}


// Export users function
function exportUsers() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    window.location.href = `{{ route('admin.users.index') }}?${params.toString()}`;
}

// Bulk actions function
function bulkActions() {
    const selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:checked'))
        .map(checkbox => checkbox.value);

    if (selectedUsers.length === 0) {
        alert('يرجى اختيار مستخدم واحد على الأقل');
        return;
    }

    const action = prompt('اختر الإجراء:\n1. تفعيل\n2. إلغاء تفعيل\n3. حذف');

    if (action && ['1', '2', '3'].includes(action)) {
        // Implement bulk actions
        console.log('Bulk action:', action, 'Users:', selectedUsers);
    }
}

// Form validation
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    const password = this.querySelector('[name="password"]').value;
    const confirmPassword = this.querySelector('[name="password_confirmation"]').value;

    if (password !== confirmPassword) {
        e.preventDefault();
        alert('كلمة المرور وتأكيد كلمة المرور غير متطابقتين');
    }
});

document.getElementById('editUserForm').addEventListener('submit', function(e) {
    const password = this.querySelector('[name="password"]').value;
    const confirmPassword = this.querySelector('[name="password_confirmation"]').value;

    if (password && password !== confirmPassword) {
        e.preventDefault();
        alert('كلمة المرور وتأكيد كلمة المرور غير متطابقتين');
    }
});

// AOS Animation
AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true
});
</script>
@endpush
@endsection
