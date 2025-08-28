@extends('layouts.app')

@section('title', 'إعدادات النظام')

@section('content')
<div class="admin-settings-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="page-title">
                        <i class="fas fa-cog me-3"></i>
                        إعدادات النظام
                    </h1>
                    <p class="page-subtitle">إدارة إعدادات المنصة وتكوينها</p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="resetSettings()">
                            <i class="fas fa-undo me-2"></i>
                            إعادة تعيين
                        </button>
                        <button type="submit" form="settingsForm" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            حفظ الإعدادات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <form id="settingsForm" method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- General Settings -->
                <div class="col-lg-8">
                    <div class="settings-section mb-4" data-aos="fade-up">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0 p-4">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-sliders-h me-2"></i>
                                    الإعدادات العامة
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">اسم المنصة</label>
                                            <input type="text" class="form-control" name="platform_name"
                                                   value="{{ old('platform_name', 'منصة التعلم الإلكتروني') }}"
                                                   placeholder="اسم المنصة">
                                            <small class="form-text text-muted">اسم المنصة الذي سيظهر في العنوان والهيدر</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">شعار المنصة</label>
                                            <input type="file" class="form-control" name="platform_logo" accept="image/*">
                                            <small class="form-text text-muted">شعار المنصة (PNG, JPG, SVG)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">البريد الإلكتروني للدعم</label>
                                            <input type="email" class="form-control" name="support_email"
                                                   value="{{ old('support_email', 'support@example.com') }}"
                                                   placeholder="support@example.com">
                                            <small class="form-text text-muted">البريد الإلكتروني للدعم الفني</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">رقم الهاتف للدعم</label>
                                            <input type="tel" class="form-control" name="support_phone"
                                                   value="{{ old('support_phone', '+966 50 123 4567') }}"
                                                   placeholder="+966 50 123 4567">
                                            <small class="form-text text-muted">رقم الهاتف للدعم الفني</small>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">وصف المنصة</label>
                                            <textarea class="form-control" name="platform_description" rows="3"
                                                      placeholder="وصف مختصر عن المنصة">{{ old('platform_description', 'منصة تعليمية متكاملة تقدم دورات تعليمية عالية الجودة') }}</textarea>
                                            <small class="form-text text-muted">وصف مختصر عن المنصة يظهر في الصفحة الرئيسية</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Settings -->
                    <div class="settings-section mb-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0 p-4">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-book me-2"></i>
                                    إعدادات الكورسات
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">الحد الأقصى للكورسات لكل مدرس</label>
                                            <input type="number" class="form-control" name="max_courses_per_instructor"
                                                   value="{{ old('max_courses_per_instructor', 10) }}" min="1" max="100">
                                            <small class="form-text text-muted">الحد الأقصى لعدد الكورسات التي يمكن للمدرس إنشاؤها</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">الحد الأقصى للدروس لكل كورس</label>
                                            <input type="number" class="form-control" name="max_lessons_per_course"
                                                   value="{{ old('max_lessons_per_course', 50) }}" min="1" max="200">
                                            <small class="form-text text-muted">الحد الأقصى لعدد الدروس في كل كورس</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">الحد الأقصى لحجم الملف</label>
                                            <select class="form-select" name="max_file_size">
                                                <option value="5" {{ old('max_file_size', '10') == '5' ? 'selected' : '' }}>5 ميجابايت</option>
                                                <option value="10" {{ old('max_file_size', '10') == '10' ? 'selected' : '' }}>10 ميجابايت</option>
                                                <option value="25" {{ old('max_file_size', '10') == '25' ? 'selected' : '' }}>25 ميجابايت</option>
                                                <option value="50" {{ old('max_file_size', '10') == '50' ? 'selected' : '' }}>50 ميجابايت</option>
                                            </select>
                                            <small class="form-text text-muted">الحد الأقصى لحجم الملفات المرفوعة</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">أنواع الملفات المسموحة</label>
                                            <input type="text" class="form-control" name="allowed_file_types"
                                                   value="{{ old('allowed_file_types', 'pdf,doc,docx,ppt,pptx,mp4,avi,mov') }}"
                                                   placeholder="pdf,doc,docx,ppt,pptx,mp4,avi,mov">
                                            <small class="form-text text-muted">أنواع الملفات المسموحة (مفصولة بفواصل)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Settings -->
                    <div class="settings-section mb-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0 p-4">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-credit-card me-2"></i>
                                    إعدادات الدفع
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">العملة الافتراضية</label>
                                            <select class="form-select" name="default_currency">
                                                <option value="USD" {{ old('default_currency', 'USD') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                                                <option value="SAR" {{ old('default_currency', 'USD') == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                                                <option value="EUR" {{ old('default_currency', 'USD') == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                                                <option value="GBP" {{ old('default_currency', 'USD') == 'GBP' ? 'selected' : '' }}>جنيه إسترليني (GBP)</option>
                                            </select>
                                            <small class="form-text text-muted">العملة الافتراضية للمنصة</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">نسبة العمولة (%)</label>
                                            <input type="number" class="form-control" name="commission_rate"
                                                   value="{{ old('commission_rate', 10) }}" min="0" max="50" step="0.1">
                                            <small class="form-text text-muted">نسبة العمولة التي تأخذها المنصة من كل عملية دفع</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">الحد الأدنى للسحب</label>
                                            <input type="number" class="form-control" name="minimum_withdrawal"
                                                   value="{{ old('minimum_withdrawal', 50) }}" min="0" step="0.01">
                                            <small class="form-text text-muted">الحد الأدنى لمبلغ السحب للمدرسين</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">فترة معالجة الدفع (أيام)</label>
                                            <input type="number" class="form-control" name="payment_processing_days"
                                                   value="{{ old('payment_processing_days', 7) }}" min="1" max="30">
                                            <small class="form-text text-muted">عدد الأيام المطلوبة لمعالجة المدفوعات</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Settings -->
                    <div class="settings-section mb-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0 p-4">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-envelope me-2"></i>
                                    إعدادات البريد الإلكتروني
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">مزود البريد الإلكتروني</label>
                                            <select class="form-select" name="mail_provider">
                                                <option value="smtp" {{ old('mail_provider', 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                                <option value="mailgun" {{ old('mail_provider', 'smtp') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                                <option value="sendgrid" {{ old('mail_provider', 'smtp') == 'sendgrid' ? 'selected' : '' }}>SendGrid</option>
                                            </select>
                                            <small class="form-text text-muted">مزود خدمة البريد الإلكتروني</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">عنوان البريد الإلكتروني المرسل</label>
                                            <input type="email" class="form-control" name="from_email"
                                                   value="{{ old('from_email', 'noreply@example.com') }}"
                                                   placeholder="noreply@example.com">
                                            <small class="form-text text-muted">عنوان البريد الإلكتروني الذي سيظهر كمرسل</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">اسم المرسل</label>
                                            <input type="text" class="form-control" name="from_name"
                                                   value="{{ old('from_name', 'منصة التعلم') }}"
                                                   placeholder="منصة التعلم">
                                            <small class="form-text text-muted">اسم المرسل الذي سيظهر في رسائل البريد الإلكتروني</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">تفعيل إشعارات البريد الإلكتروني</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="email_notifications"
                                                       value="1" {{ old('email_notifications', true) ? 'checked' : '' }}>
                                                <label class="form-check-label">تفعيل إشعارات البريد الإلكتروني</label>
                                            </div>
                                            <small class="form-text text-muted">تفعيل إرسال إشعارات البريد الإلكتروني للمستخدمين</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Settings -->
                <div class="col-lg-4">
                    <!-- System Status -->
                    <div class="system-status-card mb-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0 p-4">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-server me-2"></i>
                                    حالة النظام
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="status-item">
                                    <div class="status-icon bg-success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="status-content">
                                        <h6 class="mb-1">قاعدة البيانات</h6>
                                        <small class="text-success">متصل</small>
                                    </div>
                                </div>
                                <div class="status-item">
                                    <div class="status-icon bg-success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="status-content">
                                        <h6 class="mb-1">خدمة البريد الإلكتروني</h6>
                                        <small class="text-success">متصل</small>
                                    </div>
                                </div>
                                <div class="status-item">
                                    <div class="status-icon bg-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="status-content">
                                        <h6 class="mb-1">خدمة التخزين</h6>
                                        <small class="text-warning">تحتاج مراجعة</small>
                                    </div>
                                </div>
                                <div class="status-item">
                                    <div class="status-icon bg-success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="status-content">
                                        <h6 class="mb-1">خدمة الدفع</h6>
                                        <small class="text-success">متصل</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="quick-actions-card mb-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0 p-4">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-bolt me-2"></i>
                                    إجراءات سريعة
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-primary" onclick="clearCache()">
                                        <i class="fas fa-broom me-2"></i>
                                        مسح الكاش
                                    </button>
                                    <button type="button" class="btn btn-outline-warning" onclick="backupDatabase()">
                                        <i class="fas fa-database me-2"></i>
                                        نسخ احتياطي
                                    </button>
                                    <button type="button" class="btn btn-outline-info" onclick="testEmail()">
                                        <i class="fas fa-envelope me-2"></i>
                                        اختبار البريد الإلكتروني
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="viewLogs()">
                                        <i class="fas fa-file-alt me-2"></i>
                                        عرض السجلات
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Information -->
                    <div class="system-info-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0 p-4">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    معلومات النظام
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="info-item">
                                    <span class="info-label">إصدار النظام</span>
                                    <span class="info-value">v1.0.0</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">إصدار PHP</span>
                                    <span class="info-value">{{ phpversion() }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">إصدار Laravel</span>
                                    <span class="info-value">{{ app()->version() }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">البيئة</span>
                                    <span class="info-value">{{ config('app.env') }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">وضع التصحيح</span>
                                    <span class="info-value">{{ config('app.debug') ? 'مفعل' : 'معطل' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.admin-settings-page {
    background-color: #f8f9fa;
    min-height: 100vh;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    opacity: 0.9;
    margin-bottom: 0;
}

.settings-section,
.system-status-card,
.quick-actions-card,
.system-info-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    color: #495057;
    margin-bottom: 0.5rem;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.status-item:last-child {
    margin-bottom: 0;
}

.status-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.status-content {
    flex-grow: 1;
}

.status-content h6 {
    margin-bottom: 0.25rem;
    font-weight: 600;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #495057;
}

.info-value {
    color: #6c757d;
    font-family: monospace;
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

@media print {
    .page-header,
    .btn,
    .quick-actions-card {
        display: none !important;
    }

    .admin-settings-page {
        background-color: white !important;
    }

    .card {
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
    }
}
</style>

<script>
function resetSettings() {
    if (confirm('هل أنت متأكد من إعادة تعيين جميع الإعدادات؟')) {
        // Reset form to default values
        document.getElementById('settingsForm').reset();
    }
}

function clearCache() {
    if (confirm('هل أنت متأكد من مسح الكاش؟')) {
        // Add cache clearing logic here
        alert('تم مسح الكاش بنجاح');
    }
}

function backupDatabase() {
    if (confirm('هل تريد إنشاء نسخة احتياطية من قاعدة البيانات؟')) {
        // Add backup logic here
        alert('تم إنشاء النسخة الاحتياطية بنجاح');
    }
}

function testEmail() {
    if (confirm('هل تريد إرسال رسالة اختبار للبريد الإلكتروني؟')) {
        // Add email test logic here
        alert('تم إرسال رسالة الاختبار بنجاح');
    }
}

function viewLogs() {
    // Add logs viewing logic here
    alert('سيتم فتح صفحة السجلات في نافذة جديدة');
}
</script>
@endsection
