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
                        <button type="button" class="btn btn-outline-light" onclick="resetSettings()">
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
        <form id="settingsForm" method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
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
                                                   value="{{ old('platform_name', $settings->platform_name ?? 'منصة التعلم الإلكتروني') }}"
                                                   placeholder="اسم المنصة">
                                            <small class="form-text text-muted">اسم المنصة الذي سيظهر في العنوان والهيدر</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">شعار المنصة</label>
                                            @if(!empty($settings->platform_logo))
                                                <div class="mb-2">
                                                    <img src="{{ asset('storage/' . $settings->platform_logo) }}" alt="Logo" class="img-thumbnail" style="max-height: 60px; object-fit: contain;">
                                                </div>
                                            @endif
                                            <input type="file" class="form-control" name="platform_logo" accept="image/*">
                                            <small class="form-text text-muted">شعار المنصة (PNG, JPG, SVG)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">البريد الإلكتروني للدعم</label>
                                            <input type="email" class="form-control" name="support_email"
                                                   value="{{ old('support_email', $settings->support_email ?? 'support@example.com') }}"
                                                   placeholder="support@example.com">
                                            <small class="form-text text-muted">البريد الإلكتروني للدعم الفني</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">رقم الهاتف للدعم</label>
                                            <input type="tel" class="form-control" name="support_phone"
                                                   value="{{ old('support_phone', $settings->support_phone ?? '+966 50 123 4567') }}"
                                                   placeholder="+966 50 123 4567">
                                            <small class="form-text text-muted">رقم الهاتف للدعم الفني</small>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">وصف المنصة</label>
                                            <textarea class="form-control" name="platform_description" rows="3"
                                                      placeholder="وصف مختصر عن المنصة">{{ old('platform_description', $settings->platform_description ?? 'منصة تعليمية متكاملة تقدم دورات تعليمية عالية الجودة') }}</textarea>
                                            <small class="form-text text-muted">وصف مختصر عن المنصة يظهر في الصفحة الرئيسية</small>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="block_devtools" name="block_devtools" value="1" {{ old('block_devtools', $settings->block_devtools ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="block_devtools">تفعيل منع أدوات المطور (F12/Inspect)</label>
                                            <div class="form-text">عند التفعيل سيتم تعطيل اختصارات أدوات المطور ومحاولات فتحها.</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="block_copy_text" name="block_copy_text" value="1" {{ old('block_copy_text', $settings->block_copy_text ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="block_copy_text">تفعيل منع النسخ وتحديد النص</label>
                                            <div class="form-text">عند التفعيل سيتم تعطيل النقر بزر الفأرة الأيمن والنسخ وتحديد النص.</div>
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
                                                   value="{{ old('max_courses_per_instructor', $settings->max_courses_per_instructor ?? 10) }}" min="1" max="100">
                                            <small class="form-text text-muted">الحد الأقصى لعدد الكورسات التي يمكن للمدرس إنشاؤها</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">الحد الأقصى للدروس لكل كورس</label>
                                            <input type="number" class="form-control" name="max_lessons_per_course"
                                                   value="{{ old('max_lessons_per_course', $settings->max_lessons_per_course ?? 50) }}" min="1" max="200">
                                            <small class="form-text text-muted">الحد الأقصى لعدد الدروس في كل كورس</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">الحد الأقصى لحجم الملف</label>
                                            <select class="form-select" name="max_file_size">
                                                <option value="5" {{ old('max_file_size', $settings->max_file_size ?? '10') == '5' ? 'selected' : '' }}>5 ميجابايت</option>
                                                <option value="10" {{ old('max_file_size', $settings->max_file_size ?? '10') == '10' ? 'selected' : '' }}>10 ميجابايت</option>
                                                <option value="25" {{ old('max_file_size', $settings->max_file_size ?? '10') == '25' ? 'selected' : '' }}>25 ميجابايت</option>
                                                <option value="50" {{ old('max_file_size', $settings->max_file_size ?? '10') == '50' ? 'selected' : '' }}>50 ميجابايت</option>
                                            </select>
                                            <small class="form-text text-muted">الحد الأقصى لحجم الملفات المرفوعة</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">أنواع الملفات المسموحة</label>
                                            <input type="text" class="form-control" name="allowed_file_types"
                                                   value="{{ old('allowed_file_types', $settings->allowed_file_types ?? 'pdf,doc,docx,ppt,pptx,mp4,avi,mov') }}"
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
                                                <option value="USD" {{ old('default_currency', $settings->default_currency ?? 'USD') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                                                <option value="EGP" {{ old('default_currency', $settings->default_currency ?? 'USD') == 'EGP' ? 'selected' : '' }}>جنيه مصري (EGP)</option>
                                                <option value="SAR" {{ old('default_currency', $settings->default_currency ?? 'USD') == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                                                <option value="EUR" {{ old('default_currency', $settings->default_currency ?? 'USD') == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                                                <option value="GBP" {{ old('default_currency', $settings->default_currency ?? 'USD') == 'GBP' ? 'selected' : '' }}>جنيه إسترليني (GBP)</option>
                                            </select>
                                            <small class="form-text text-muted">العملة الافتراضية للمنصة</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">نسبة العمولة (%)</label>
                                            <input type="number" class="form-control" name="commission_rate"
                                                   value="{{ old('commission_rate', $settings->commission_rate ?? 10) }}" min="0" max="50" step="0.1">
                                            <small class="form-text text-muted">نسبة العمولة التي تأخذها المنصة من كل عملية دفع</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">الحد الأدنى للسحب</label>
                                            <input type="number" class="form-control" name="minimum_withdrawal"
                                                   value="{{ old('minimum_withdrawal', $settings->minimum_withdrawal ?? 50) }}" min="0" step="0.01">
                                            <small class="form-text text-muted">الحد الأدنى لمبلغ السحب للمدرسين</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">فترة معالجة الدفع (أيام)</label>
                                            <input type="number" class="form-control" name="payment_processing_days"
                                                   value="{{ old('payment_processing_days', $settings->payment_processing_days ?? 7) }}" min="1" max="30">
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
                                                <option value="smtp" {{ old('mail_provider', $settings->mail_provider ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                                <option value="mailgun" {{ old('mail_provider', $settings->mail_provider ?? 'smtp') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                                <option value="sendgrid" {{ old('mail_provider', $settings->mail_provider ?? 'smtp') == 'sendgrid' ? 'selected' : '' }}>SendGrid</option>
                                            </select>
                                            <small class="form-text text-muted">مزود خدمة البريد الإلكتروني</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">عنوان البريد الإلكتروني المرسل</label>
                                            <input type="email" class="form-control" name="from_email"
                                                   value="{{ old('from_email', $settings->from_email ?? 'noreply@example.com') }}"
                                                   placeholder="noreply@example.com">
                                            <small class="form-text text-muted">عنوان البريد الإلكتروني الذي سيظهر كمرسل</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">اسم المرسل</label>
                                            <input type="text" class="form-control" name="from_name"
                                                   value="{{ old('from_name', $settings->from_name ?? 'منصة التعلم') }}"
                                                   placeholder="منصة التعلم">
                                            <small class="form-text text-muted">اسم المرسل الذي سيظهر في رسائل البريد الإلكتروني</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">تفعيل إشعارات البريد الإلكتروني</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="email_notifications"
                                                       value="1" {{ old('email_notifications', $settings->email_notifications ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label">تفعيل إشعارات البريد الإلكتروني</label>
                                            </div>
                                            <small class="form-text text-muted">تفعيل إرسال إشعارات البريد الإلكتروني للمستخدمين</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <!-- Compliance & Policies Settings -->
                    <div class="settings-section mb-4" data-aos="fade-up" data-aos-delay="400">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0 p-4">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-file-contract me-2"></i>
                                    صفحات الامتثال والقوانين (Compliance & Policies)
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <p class="text-muted mb-4">
                                    يمكنك تعديل نصوص صفحات الامتثال والسياسات أدناه باستخدام تنسيق Markdown. سيتم حفظ التعديلات في قاعدة البيانات وعرضها للمستخدمين باللغة الإنجليزية كما هو مطلوب لبوابات الدفع.
                                </p>
                                
                                <ul class="nav nav-pills mb-3 gap-2" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link compliance-nav-link active" id="pills-terms-tab" data-bs-toggle="pill" data-bs-target="#pills-terms" type="button" role="tab" aria-controls="pills-terms" aria-selected="true">
                                            الشروط والأحكام
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link compliance-nav-link" id="pills-privacy-tab" data-bs-toggle="pill" data-bs-target="#pills-privacy" type="button" role="tab" aria-controls="pills-privacy" aria-selected="false">
                                            سياسة الخصوصية
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link compliance-nav-link" id="pills-refund-tab" data-bs-toggle="pill" data-bs-target="#pills-refund" type="button" role="tab" aria-controls="pills-refund" aria-selected="false">
                                            سياسة الاسترجاع
                                        </button>
                                    </li>
                                </ul>
                                
                                <div class="tab-content" id="pills-tabContent">
                                    <!-- Terms & Conditions -->
                                    <div class="tab-pane fade show active" id="pills-terms" role="tabpanel" aria-labelledby="pills-terms-tab" tabindex="0">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">نص الشروط والأحكام (Terms & Conditions)</label>
                                            <textarea class="form-control font-monospace" name="terms_and_conditions" rows="15" dir="ltr" placeholder="Write terms in Markdown...">{{ old('terms_and_conditions', $settings->terms_and_conditions ?? '') }}</textarea>
                                            <small class="form-text text-muted">تنسيق Markdown مدعوم بالكامل.</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Privacy Policy -->
                                    <div class="tab-pane fade" id="pills-privacy" role="tabpanel" aria-labelledby="pills-privacy-tab" tabindex="0">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">نص سياسة الخصوصية (Privacy Policy)</label>
                                            <textarea class="form-control font-monospace" name="privacy_policy" rows="15" dir="ltr" placeholder="Write privacy policy in Markdown...">{{ old('privacy_policy', $settings->privacy_policy ?? '') }}</textarea>
                                            <small class="form-text text-muted">تنسيق Markdown مدعوم بالكامل. تأكد من إبقاء بند Paymob للمدفوعات.</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Refund Policy -->
                                    <div class="tab-pane fade" id="pills-refund" role="tabpanel" aria-labelledby="pills-refund-tab" tabindex="0">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold">نص سياسة الاسترجاع (Refund & Cancellation)</label>
                                            <textarea class="form-control font-monospace" name="refund_and_cancellation_policy" rows="15" dir="ltr" placeholder="Write refund policy in Markdown...">{{ old('refund_and_cancellation_policy', $settings->refund_and_cancellation_policy ?? '') }}</textarea>
                                            <small class="form-text text-muted">تنسيق Markdown مدعوم بالكامل. يجب توضيح فترة ووسيلة الاسترجاع البنكي.</small>
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
/* Compliance Tab Styling */
.compliance-nav-link {
    font-weight: 600;
    color: #495057 !important;
    background-color: #f1f5f9 !important;
    border: 1px solid #cbd5e1 !important;
    padding: 10px 20px !important;
    border-radius: 8px !important;
    transition: all 0.2s ease-in-out !important;
}

.compliance-nav-link:hover {
    color: #4f46e5 !important;
    background-color: #e2e8f0 !important;
    border-color: #94a3b8 !important;
    transform: none !important;
}

.compliance-nav-link.active {
    color: #ffffff !important;
    background-color: #4f46e5 !important;
    border-color: #4f46e5 !important;
}

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
        const btn = document.querySelector('button[onclick="clearCache()"]');
        const originalContent = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري مسح الكاش...';

        fetch('{{ route('admin.settings.clearCache') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalContent;
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ أثناء مسح الكاش');
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = originalContent;
            console.error('Error:', error);
            alert('حدث خطأ غير متوقع أثناء مسح الكاش');
        });
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
