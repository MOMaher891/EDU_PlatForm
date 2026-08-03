@extends('layouts.app')

@php
    if (!isset($activeGateways)) {
        $activeGateways = \App\Models\PaymentGateway::where('is_active', true)->orderBy('sort_order', 'asc')->get();
    }

    $stripeConfig = config('payment.gateways.stripe');
    $paypalConfig = config('payment.gateways.paypal');
    $paymobConfig = config('payment.gateways.paymob');
    $kashierConfig = config('payment.gateways.kashier');
    
    // Get default currency
    $settings = \App\Models\Setting::getCached();
    $defaultCurrency = $settings->default_currency ?? 'EGP';

    // Handle course vs section
    $payable = isset($section) ? $section : $course;

    // Get effective price and calculate tax & commission
    $effectivePrice = $payable->getEffectivePrice();
    $taxRate = 0.00; // 0% VAT
    $taxAmount = $effectivePrice * $taxRate;
    
    $commissionRate = (float) ($settings->commission_rate ?? 0) / 100;
    $commissionAmount = $effectivePrice * $commissionRate;
    
    $totalAmount = $effectivePrice * (1 + $taxRate + $commissionRate);

    // Get dynamic exchange rates
    $exchangeRates = \App\Models\Setting::getRates();

    // Define process payment route dynamically
    $processRoute = isset($section) ? route('payment.section.process', [$course, $section]) : route('payment.process', $course);
@endphp

@section('title', 'إتمام الشراء - ' . $course->title)

@section('content')
<div class="checkout-page">
    <div class="container py-4">
        <!-- Modern Glassmorphic Header -->
        <div class="checkout-header mb-4" data-aos="fade-down">
            <div class="row align-items-center">
                <div class="col-md-7 text-start">
                    <h1 class="checkout-title">
                        <i class="fas fa-wallet text-indigo-600 me-2"></i>
                        إتمام الشراء والدفع الآمن
                    </h1>
                    <p class="checkout-subtitle">أكمل عمليتك التعليمية والتحق بالدورة التدريبية الآن</p>
                </div>
                <div class="col-md-5 text-end mt-3 mt-md-0">
                    <div class="security-badges">
                        <span class="security-badge">
                            <i class="fas fa-shield-alt text-emerald-500"></i>
                            دفع آمن 100%
                        </span>
                        <span class="security-badge">
                            <i class="fas fa-lock text-indigo-500"></i>
                            تشفير SSL 256-bit
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Timeline Steps -->
        <div class="payment-steps mb-5" data-aos="fade-down" data-aos-delay="100">
            <div class="step active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-label">طريقة الدفع</div>
            </div>
            <div class="step active" data-step="2">
                <div class="step-number">2</div>
                <div class="step-label">تفاصيل الفوترة</div>
            </div>
            <div class="step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-label">الالتحاق والتعلم</div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Side: Forms -->
            <div class="col-lg-8" data-aos="fade-right" data-aos-delay="200">
                <div class="payment-form-section">
                    
                    <!-- Interactive Payment Select Grid -->
                    <div class="card border-0 mb-4 shadow-sm">
                        <div class="card-header py-3">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-credit-card text-indigo-600"></i>
                                اختر وسيلة الدفع المفضلة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="payment-methods-grid">
                                @forelse($activeGateways as $index => $gw)
                                @php
                                     $gwCurrency = $gw->credentials['currency'] ?? (in_array($gw->code, ['stripe', 'paypal']) ? 'USD' : 'EGP');
                                     $gwAmount = \App\Models\Setting::convert($totalAmount, $defaultCurrency, $gwCurrency);
                                @endphp
                                <div class="payment-method-card {{ $index === 0 ? 'active' : '' }}"
                                     data-method="{{ $gw->code }}"
                                     data-gateway="{{ $gw->code }}"
                                     data-currency="{{ $gwCurrency }}"
                                     data-amount="{{ number_format($gwAmount, 2, '.', '') }}">
                                    <div class="method-check-dot"></div>
                                    <div class="method-icon-wrap">
                                        @if($gw->code === 'kashier')
                                            <i class="fas fa-shield-alt text-emerald-500"></i>
                                        @elseif($gw->code === 'paymob')
                                            <i class="fas fa-mobile-alt text-violet-500"></i>
                                        @elseif($gw->code === 'stripe')
                                            <i class="fab fa-stripe-s text-indigo-600"></i>
                                        @elseif($gw->code === 'paypal')
                                            <i class="fab fa-paypal text-blue-500"></i>
                                        @else
                                            <i class="fas fa-credit-card text-slate-500"></i>
                                        @endif
                                    </div>
                                    <div class="method-details">
                                        <h6>{{ $gw->name }}</h6>
                                        <p>
                                            @if($gw->code === 'kashier')
                                                بطاقات بنكية ومحفظة إلكترونية
                                            @elseif($gw->code === 'paymob')
                                                محافظ إلكترونية وبطاقات محلية
                                            @elseif($gw->code === 'stripe')
                                                Visa / MasterCard / AMEX
                                            @elseif($gw->code === 'paypal')
                                                دفع آمن وسريع عبر حساب PayPal
                                            @else
                                                دفع إلكتروني مباشر
                                            @endif
                                        </p>
                                    </div>
                                    <span class="currency-tag {{ in_array($gw->code, ['kashier', 'paymob', 'fawry']) ? 'local' : '' }}">
                                        {{ $gw->credentials['currency'] ?? (in_array($gw->code, ['stripe', 'paypal']) ? 'USD' : 'EGP') }}
                                    </span>
                                </div>
                                @empty
                                <div class="alert alert-warning border-0 rounded-3 w-100 py-3 text-center my-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    عذراً، لا توجد بوابات دفع مفعلة حالياً في المنصة. يرجى تفعيل البوابات من لوحة التحكم.
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details Input Form Wrapper -->
                    <form id="paymentForm" method="POST" action="{{ $processRoute }}">
                        @csrf
                        @php $firstGw = $activeGateways->first(); @endphp
                        <input type="hidden" name="gateway" id="selectedGateway" value="{{ $firstGw->code ?? '' }}">
                        <input type="hidden" name="payment_method" id="selectedPaymentMethod" value="{{ $firstGw->code ?? '' }}">

                        <!-- Stripe Credit Card Form -->
                        <div class="payment-form-box {{ ($firstGw->code ?? '') === 'stripe' ? 'active' : '' }}" id="stripeForm">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-credit-card text-indigo-600"></i>
                                        تفاصيل بطاقة الدفع (Stripe)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label class="form-label">رقم البطاقة وتفاصيل الأمان</label>
                                        <div id="card-element">
                                            <!-- Stripe Elements will be inserted here -->
                                        </div>
                                        <div id="card-errors" class="text-danger mt-2 small" role="alert"></div>
                                    </div>
                                    
                                    <div class="mb-1">
                                        <label class="form-label">اسم حامل البطاقة</label>
                                        <div class="input-group-custom">
                                            <span class="input-icon"><i class="fas fa-user-circle"></i></span>
                                            <input type="text" class="form-control-custom" name="card_holder"
                                                   placeholder="الاسم الكامل كما هو مكتوب على البطاقة">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kashier Form Panel -->
                        <div class="payment-form-box {{ ($firstGw->code ?? '') === 'kashier' ? 'active' : '' }}" id="kashierForm">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-shield-alt text-emerald-600 me-2"></i>
                                        الدفع الإلكتروني عبر بوابة Kashier
                                    </h5>
                                </div>
                                <div class="card-body text-center py-5">
                                    <div class="kashier-info-panel">
                                        <div class="kashier-badge-icon mb-4" style="width:72px; height:72px; background:rgba(16, 185, 129, 0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto;">
                                            <i class="fas fa-shield-alt text-emerald-600 fa-2x"></i>
                                        </div>
                                        <h5 class="fw-bold mb-2">نافذة Kashier للدفع الإلكتروني الآمنة</h5>
                                        <p class="text-slate-500 max-w-md mx-auto mb-4">
                                            عند النقر على "إتمام الدفع عبر بوابة Kashier"، ستُفتح نافذة Kashier الرسمية الآمنة لإدخال بيانات بطاقتك البنكية (Visa / MasterCard / Meeza) أو محفظتك المحمولة بأمان تام.
                                        </p>
                                        <div class="alert alert-emerald border-0 rounded-3 max-w-md mx-auto py-2.5 text-start small bg-emerald-50 text-emerald-800">
                                            <i class="fas fa-lock text-emerald-600 me-1"></i>
                                            المعاملة مشفرة بتقنية HMAC-SHA256 ومعتمدة من البنك المركزي المصري.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PayPal Checkout Form -->
                        <div class="payment-form-box {{ ($firstGw->code ?? '') === 'paypal' ? 'active' : '' }}" id="paypalForm">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center py-5">
                                    <div class="paypal-info-panel">
                                        <div class="paypal-badge-icon mb-4">
                                            <i class="fab fa-paypal text-blue-600"></i>
                                        </div>
                                        <h5 class="mb-2">تسجيل الدخول عبر PayPal</h5>
                                        <p class="text-slate-500 max-w-md mx-auto mb-4">سيتم فتح نافذة آمنة تماماً لإدخال بيانات حساب PayPal الخاص بك والتأكيد الفوري.</p>
                                        <div class="paypal-bullets d-flex justify-content-center gap-3 mb-4">
                                            <span class="bullet-item"><i class="fas fa-check-circle text-emerald-500 me-1"></i>حماية المشتري</span>
                                            <span class="bullet-item"><i class="fas fa-check-circle text-emerald-500 me-1"></i>دعم الدفع المباشر</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PayMob Gateway Form -->
                        <div class="payment-form-box {{ ($firstGw->code ?? '') === 'paymob' ? 'active' : '' }}" id="paymobForm">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-mobile-alt text-violet-600"></i>
                                        الدفع المحلي عبر PayMob
                                    </h5>
                                </div>
                                <div class="card-body text-center py-5">
                                    <div class="paymob-info-panel">
                                        <div class="paymob-badge-icon mb-4">
                                            <i class="fas fa-wallet text-violet-600"></i>
                                        </div>
                                        <h5 class="mb-2">بوابة الدفع المحلية الآمنة</h5>
                                        <p class="text-slate-500 max-w-md mx-auto mb-4">
                                            ادفع باستخدام فودافون كاش، اتصالات كاش، أورنج كاش، أو أي بطاقة بنكية محلية في مصر.
                                        </p>
                                        <div class="alert alert-info border-0 rounded-3 max-w-md mx-auto py-2 text-start small">
                                            <i class="fas fa-info-circle me-1"></i>
                                            سيتم إعادة توجيهك إلى صفحة الدفع الآمنة الخاصة بـ PayMob لإتمام العملية.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Premium Billing Information Panel -->
                        <div class="card border-0 shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-map-marker-alt text-indigo-600"></i>
                                    معلومات المشتري والفوترة
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">الاسم الأول</label>
                                        <input type="text" class="form-control" name="first_name"
                                               value="{{ explode(' ', Auth::user()->name)[0] }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الاسم الأخير</label>
                                        <input type="text" class="form-control" name="last_name"
                                               value="{{ explode(' ', Auth::user()->name)[1] ?? '' }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">البريد الإلكتروني</label>
                                        <input type="email" class="form-control" name="email"
                                               value="{{ Auth::user()->email }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">رقم الهاتف المحمول</label>
                                        <input type="tel" class="form-control" name="phone" placeholder="مثال: +201000000000" value="{{ Auth::user()->phone ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الدولة</label>
                                        <select class="form-select" name="country" required>
                                            <option value="">اختر الدولة</option>
                                            <option value="EG" selected>مصر (EG)</option>
                                            <option value="SA">السعودية (SA)</option>
                                            <option value="AE">الإمارات (AE)</option>
                                            <option value="JO">الأردن (JO)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Button under form -->
                        <div class="mt-4" id="leftSubmitBtnContainer">
                            @if($activeGateways->count() > 0)
                                <button type="submit" class="{{ ($firstGw->code ?? '') === 'kashier' ? 'btn btn-emerald-custom' : (($firstGw->code ?? '') === 'paymob' ? 'btn btn-violet-custom' : 'btn btn-indigo') }} btn-lg w-100 py-3 d-flex align-items-center justify-content-center gap-2" id="submitPaymentBtn">
                                    <i class="fas fa-lock me-1"></i>
                                    <span class="btn-text">
                                        @if(($firstGw->code ?? '') === 'kashier')
                                            ادفع الآن عبر بوابة Kashier المشفرة
                                        @elseif(($firstGw->code ?? '') === 'paymob')
                                            ادفع الآن عبر PayMob بالعملة المحلية
                                        @elseif(($firstGw->code ?? '') === 'paypal')
                                            ادفع الآن عبر حساب PayPal
                                        @else
                                            إتمام الدفع بالبطاقة الائتمانية
                                        @endif
                                    </span>
                                    <div class="btn-loader d-none">
                                        <div class="spinner-border spinner-border-sm text-white"></div>
                                        جاري توجيهك بأمان...
                                    </div>
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary btn-lg w-100 py-3 text-white" disabled>
                                    <i class="fas fa-ban me-1"></i> لا توجد وسائل دفع متاحة حالياً
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Side: Order Summary -->
            <div class="col-lg-4" data-aos="fade-left" data-aos-delay="200">
                <div class="order-summary-section sticky-top" style="top: 24px;">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-slate-900 text-white py-3">
                            <h5 class="card-title text-white mb-0">
                                <i class="fas fa-receipt me-2 text-indigo-400"></i>
                                ملخص الفاتورة
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Course Summary Header -->
                            <div class="course-summary-card mb-4">
                                <div class="course-thumbnail-wrap">
                                    <img src="{{ $course->thumbnail_url }}"
                                         alt="{{ $course->title }}" class="img-fluid">
                                </div>
                                <div class="course-brief mt-3">
                                    <h6>{{ $course->title }}</h6>
                                    <p class="text-slate-500 mb-2">
                                        <i class="fas fa-chalkboard-teacher me-1"></i>
                                        المحاضر: {{ $course->instructor->name }}
                                    </p>
                                    <div class="course-badges">
                                        <span class="badge bg-slate-100 text-slate-700">
                                            <i class="fas fa-clock me-1 text-indigo-600"></i>
                                            {{ $course->duration_hours }} ساعة
                                        </span>
                                        <span class="badge bg-slate-100 text-slate-700">
                                            <i class="fas fa-video me-1 text-indigo-600"></i>
                                            {{ $course->getTotalLessons() }} درس
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-slate-200 my-3">

                            <!-- Receipt Breakdown Table -->
                            <div class="receipt-breakdown mb-4">
                                <div class="receipt-row">
                                    <span>{{ isset($section) ? 'قيمة القسم: ' . $section->title : 'قيمة الدورة التدريبية' }}</span>
                                    <span class="price-val" data-base-amount="{{ $payable->price }}">{{ \App\Models\Setting::formatPrice($payable->price) }}</span>
                                </div>

                                @if($payable->discount_price)
                                <div class="receipt-row discount">
                                    <span>الخصم والتخفيض</span>
                                    <span class="price-val text-emerald-600" data-base-amount="-{{ $payable->price - $payable->discount_price }}">-{{ \App\Models\Setting::formatPrice($payable->price - $payable->discount_price) }}</span>
                                </div>
                                @endif

                                @if($taxRate > 0)
                                <div class="receipt-row">
                                    <span>ضريبة القيمة المضافة ({{ $taxRate * 100 }}%)</span>
                                    <span class="price-val" data-base-amount="{{ $taxAmount }}">{{ \App\Models\Setting::formatPrice($taxAmount) }}</span>
                                </div>
                                @endif

                                @if($commissionRate > 0)
                                <div class="receipt-row">
                                    <span>رسوم إضافية ({{ $commissionRate * 100 }}%)</span>
                                    <span class="price-val" data-base-amount="{{ $commissionAmount }}">{{ \App\Models\Setting::formatPrice($commissionAmount) }}</span>
                                </div>
                                @endif

                                <div class="receipt-row total mt-3 pt-3">
                                    <span>المبلغ المستحق للدفع</span>
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="total-val" data-base-amount="{{ $totalAmount }}" data-default-currency="{{ $defaultCurrency }}" id="totalPriceDisplay">{{ \App\Models\Setting::formatPrice($totalAmount) }}</span>
                                        <span class="exchange-notice d-none" id="exchangeDisplay"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="exchange-rate-tip d-none mb-3" id="exchangeRateTip">
                                <i class="fas fa-info-circle text-violet-600 me-1"></i>
                                تم حساب السعر بالعملة المحلية EGP (بمعدل صرف تقريبي 1$ = 50 ج.م)
                            </div>

                            <hr class="border-slate-200 my-3">

                            <!-- Core Features Granted List -->
                            <div class="benefits-check-list mb-4">
                                <p class="list-title mb-2">مميزات العضوية الفورية:</p>
                                <ul class="list-unstyled mb-0">
                                    <li><i class="fas fa-check text-emerald-500"></i>وصول فوري ومدى الحياة للمحتوى</li>
                                    <li><i class="fas fa-check text-emerald-500"></i>مشاهدة على الهواتف والأجهزة اللوحية</li>
                                    <li><i class="fas fa-check text-emerald-500"></i>شهادة إتمام معتمدة فورية</li>
                                </ul>
                            </div>

                            <!-- Guarantee Shield -->
                            <div class="guarantee-box text-center p-3 rounded-3 mb-2">
                                <i class="fas fa-shield-alt text-emerald-600 fa-2x mb-2"></i>
                                <h6 class="text-emerald-800 font-bold mb-1">ضمان استرداد الأموال</h6>
                                <p class="text-slate-500 small mb-0">استرد قيمة اشتراكك بالكامل خلال 30 يوماً</p>
                            </div>
                        </div>

                        <div class="card-footer bg-slate-50 border-top border-slate-100 p-3">
                            <div class="text-center text-slate-500 small mb-0">
                                <i class="fas fa-user-shield text-indigo-500 me-1"></i>
                                اتصالك بالبوابة مشفر بالكامل وآمن
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<!-- Kashier Embedded iFrame Modal (Keep Student Inside Platform) -->
<div class="modal fade" id="kashierIframeModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-slate-900 text-white p-3">
                <h5 class="modal-title fw-bold fs-6 d-flex align-items-center gap-2">
                    <i class="fas fa-shield-alt text-emerald-400"></i>
                    الدفع الإلكتروني الآمن عبر بوابة Kashier
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="toggleBtnLoading(document.getElementById('submitPaymentBtn'), false)"></button>
            </div>
            <div class="modal-body p-0" style="min-height: 540px; background: #ffffff;">
                <iframe id="kashierIframeContainer" src="" style="width: 100%; height: 560px; border: none;" allow="payment"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap');

.checkout-page {
    background-color: #f8fafc;
    color: #1e293b;
    min-height: 100vh;
    font-family: 'Cairo', 'Outfit', system-ui, -apple-system, sans-serif;
    direction: rtl;
    text-align: right;
}

/* Colors & Utilities */
.text-indigo-600 { color: #4f46e5; }
.text-indigo-500 { color: #6366f1; }
.text-emerald-500 { color: #10b981; }
.text-emerald-600 { color: #059669; }
.text-slate-500 { color: #64748b; }
.text-slate-400 { color: #94a3b8; }
.bg-slate-900 { background-color: #0f172a; }
.bg-slate-100 { background-color: #f1f5f9; }
.border-slate-200 { border-color: #e2e8f0; }

/* Premium Header styling */
.checkout-header {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 24px;
    padding: 24px 32px;
    box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.03);
}

.checkout-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
}

.checkout-subtitle {
    font-size: 0.95rem;
    color: #64748b;
    margin: 4px 0 0 0;
}

.security-badges {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.security-badge {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569;
    padding: 8px 16px;
    border-radius: 999px;
    font-size: 0.82rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* Timeline Steps styling */
.payment-steps {
    display: flex;
    justify-content: space-between;
    position: relative;
    max-width: 600px;
    margin: 0 auto;
}

.payment-steps::before {
    content: '';
    position: absolute;
    top: 18px;
    left: 20px;
    right: 20px;
    height: 2px;
    background: #e2e8f0;
    z-index: 0;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    z-index: 1;
    background: #f8fafc;
    padding: 0 12px;
    position: relative;
}

.step-number {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: white;
    border: 2px solid #cbd5e1;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-family: 'Outfit', sans-serif;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.step.active .step-number {
    background: #4f46e5;
    border-color: #4f46e5;
    color: white;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
}

.step-label {
    font-size: 0.85rem;
    color: #64748b;
    margin-top: 8px;
    font-weight: 600;
}

.step.active .step-label {
    color: #4f46e5;
    font-weight: 700;
}

/* Grid Layout for Payment Options */
.payment-methods-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
}

.payment-method-card {
    border: 2px solid #e2e8f0;
    background: white;
    border-radius: 16px;
    padding: 20px;
    cursor: pointer;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.payment-method-card:hover {
    border-color: #cbd5e1;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.04);
}

.payment-method-card.active {
    border-color: #4f46e5;
    background: rgba(79, 70, 229, 0.02);
    box-shadow: 0 12px 20px -8px rgba(79, 70, 229, 0.12);
}

.method-check-dot {
    position: absolute;
    top: 14px;
    right: 14px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    background: white;
    transition: all 0.2s ease;
}

.payment-method-card.active .method-check-dot {
    border-color: #4f46e5;
    background: #4f46e5;
    box-shadow: inset 0 0 0 3px white;
}

.method-icon-wrap {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    background: #f8fafc;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    margin-bottom: 12px;
    transition: all 0.2s ease;
}

.payment-method-card.active .method-icon-wrap {
    background: #4f46e5;
    color: white;
}

.method-details h6 {
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 4px 0;
}

.method-details p {
    font-size: 0.78rem;
    color: #64748b;
    margin: 0;
    line-height: 1.4;
}

.currency-tag {
    position: absolute;
    bottom: 12px;
    left: 12px;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
    background: #f1f5f9;
    color: #64748b;
    font-family: 'Outfit', sans-serif;
}

.currency-tag.local {
    background: rgba(139, 92, 246, 0.1);
    color: #7c3aed;
}

/* Premium Form Elements */
.card {
    border: 1px solid #e2e8f0 !important;
    border-radius: 20px !important;
    overflow: hidden;
    background: white;
}

.card-header {
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 16px 24px !important;
}

.card-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-body {
    padding: 24px !important;
}

.form-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 8px;
}

.form-control, .form-select {
    border: 1.5px solid #cbd5e1;
    border-radius: 12px;
    padding: 10px 16px;
    font-size: 0.92rem;
    color: #0f172a;
    background-color: white;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.08);
    outline: 0;
}

/* Custom icon group for Stripe name input */
.input-group-custom {
    display: flex;
    align-items: center;
    position: relative;
}

.input-icon {
    position: absolute;
    right: 16px;
    color: #94a3b8;
    font-size: 1.1rem;
    pointer-events: none;
}

.form-control-custom {
    border: 1.5px solid #cbd5e1;
    border-radius: 12px;
    padding: 10px 16px 10px 48px;
    font-size: 0.92rem;
    color: #0f172a;
    width: 100%;
    transition: all 0.2s ease;
    padding-right: 48px; /* space for icon */
}

.form-control-custom:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.08);
    outline: 0;
}

/* Stripe form elements styling */
#card-element {
    border: 1.5px solid #cbd5e1;
    border-radius: 12px;
    padding: 12px 16px;
    background: white;
    transition: all 0.2s ease;
}

#card-element.StripeElement--focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.08);
}

/* PayPal & Paymob panels */
.paypal-badge-icon, .paymob-badge-icon {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    margin: 0 auto;
}

.paypal-badge-icon {
    background: rgba(37, 99, 235, 0.08);
}

.paymob-badge-icon {
    background: rgba(124, 58, 237, 0.08);
}

.bullet-item {
    font-size: 0.85rem;
    font-weight: 600;
    color: #475569;
}

.payment-form-box {
    display: none;
}

.payment-form-box.active {
    display: block;
}

/* Order Summary & Receipt styling */
.course-summary-card {
    display: flex;
    gap: 16px;
}

.course-thumbnail-wrap {
    width: 100px;
    height: 100px;
    border-radius: 12px;
    overflow: hidden;
    flex-shrink: 0;
    border: 1px solid #e2e8f0;
}

.course-thumbnail-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.course-brief h6 {
    font-size: 1rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.4;
    margin-bottom: 4px;
}

.course-brief p {
    font-size: 0.85rem;
}

.course-badges {
    display: flex;
    gap: 8px;
}

.receipt-breakdown {
    background: #f8fafc;
    padding: 16px 20px;
    border-radius: 16px;
    border: 1px dashed #cbd5e1;
}

.receipt-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.88rem;
    color: #475569;
    margin-bottom: 10px;
}

.receipt-row.discount {
    color: #059669;
    font-weight: 600;
}

.receipt-row.total {
    border-top: 1.5px solid #cbd5e1;
    margin-bottom: 0;
}

.receipt-row.total span {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
}

.total-val {
    font-size: 1.4rem;
    font-weight: 900;
    color: #4f46e5;
    font-family: 'Outfit', sans-serif;
}

.exchange-notice {
    font-size: 0.82rem;
    font-weight: 700;
    color: #7c3aed;
    margin-top: 2px;
    font-family: 'Outfit', sans-serif;
}

.exchange-rate-tip {
    font-size: 0.78rem;
    color: #6d28d9;
    background: rgba(124, 58, 237, 0.05);
    border-radius: 8px;
    padding: 8px 12px;
}

.benefits-check-list li {
    font-size: 0.82rem;
    color: #475569;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.guarantee-box {
    background: rgba(16, 185, 129, 0.05);
    border: 1px solid rgba(16, 185, 129, 0.1);
}

/* Buttons and Loading states */
.btn-indigo {
    background-color: #4f46e5;
    color: white;
    border: none;
    border-radius: 14px;
    font-weight: 700;
    font-size: 1rem;
    transition: all 0.2s ease;
}

.btn-indigo:hover {
    background-color: #4338ca;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 8px 20px -4px rgba(79, 70, 229, 0.35);
}

.btn-indigo:active {
    transform: translateY(0);
}

.btn-indigo:disabled {
    background-color: #818cf8;
    color: rgba(255, 255, 255, 0.8);
    cursor: not-allowed;
}

.btn-violet-custom {
    background-color: #7c3aed;
    color: white;
    border: none;
    border-radius: 14px;
    font-weight: 700;
    font-size: 1rem;
    transition: all 0.2s ease;
}

.btn-violet-custom:hover {
    background-color: #6d28d9;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 8px 20px -4px rgba(124, 58, 237, 0.35);
}

.btn-violet-custom:active {
    transform: translateY(0);
}

.btn-violet-custom:disabled {
    background-color: #c084fc;
    color: rgba(255, 255, 255, 0.8);
    cursor: not-allowed;
}

.btn-emerald-custom {
    background-color: #10b981;
    color: white;
    border: none;
    border-radius: 14px;
    font-weight: 700;
    font-size: 1rem;
    transition: all 0.2s ease;
}

.btn-emerald-custom:hover {
    background-color: #059669;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 8px 20px -4px rgba(16, 185, 129, 0.35);
}

.btn-emerald-custom:disabled {
    background-color: #6ee7b7;
    color: rgba(255, 255, 255, 0.8);
    cursor: not-allowed;
}

.spinner-border-sm {
    width: 1.1rem;
    height: 1.1rem;
    border-width: 0.15em;
}

/* Animations */
[data-aos] {
    pointer-events: none;
}
.aos-animate {
    pointer-events: auto;
}

@media (max-width: 768px) {
    .checkout-title {
        font-size: 1.5rem;
    }

    .security-badges {
        justify-content: center;
    }

    .payment-steps {
        gap: 8px;
    }

    .payment-steps::before {
        display: none;
    }

    .step {
        padding: 0;
    }

    .payment-methods-grid {
        grid-template-columns: 1fr;
    }
}

#card-element {
    direction: ltr !important;
    text-align: left !important;
}

/* =========================================================
   DARK MODE OVERRIDES FOR STUDENT CHECKOUT PAGE
   ========================================================= */
[data-bs-theme="dark"] .checkout-page {
    background-color: #0f172a !important;
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .checkout-header {
    background: #1e293b !important;
    border-color: #334155 !important;
}

[data-bs-theme="dark"] .checkout-title {
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .checkout-subtitle {
    color: #cbd5e1 !important;
}

[data-bs-theme="dark"] .security-badge {
    background: #0f172a !important;
    border-color: #334155 !important;
    color: #cbd5e1 !important;
}

[data-bs-theme="dark"] .payment-steps::before {
    background: #334155 !important;
}

[data-bs-theme="dark"] .step {
    background: transparent !important;
}

[data-bs-theme="dark"] .step-number {
    background: #1e293b !important;
    border-color: #475569 !important;
    color: #94a3b8 !important;
}

[data-bs-theme="dark"] .step.active .step-number {
    background: #6366f1 !important;
    border-color: #6366f1 !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.25) !important;
}

[data-bs-theme="dark"] .step-label {
    color: #94a3b8 !important;
}

[data-bs-theme="dark"] .step.active .step-label {
    color: #818cf8 !important;
}

[data-bs-theme="dark"] .payment-method-card {
    background: #1e293b !important;
    border-color: #334155 !important;
}

[data-bs-theme="dark"] .payment-method-card:hover {
    border-color: #475569 !important;
    background: #334155 !important;
}

[data-bs-theme="dark"] .payment-method-card.active {
    background: #312e81 !important;
    border-color: #6366f1 !important;
    box-shadow: 0 12px 24px -8px rgba(99, 102, 241, 0.4) !important;
}

[data-bs-theme="dark"] .payment-method-card .method-details h6 {
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .payment-method-card .method-details p {
    color: #cbd5e1 !important;
}

[data-bs-theme="dark"] .method-icon-wrap {
    background: #0f172a !important;
    color: #94a3b8 !important;
}

[data-bs-theme="dark"] .payment-method-card.active .method-icon-wrap {
    background: #6366f1 !important;
    color: #ffffff !important;
}

[data-bs-theme="dark"] .currency-tag {
    background: #0f172a !important;
    color: #cbd5e1 !important;
}

[data-bs-theme="dark"] .currency-tag.local {
    background: rgba(139, 92, 246, 0.2) !important;
    color: #c084fc !important;
}

[data-bs-theme="dark"] .card {
    background: #1e293b !important;
    border-color: #334155 !important;
}

[data-bs-theme="dark"] .card-header {
    background: #0f172a !important;
    border-bottom-color: #334155 !important;
}

[data-bs-theme="dark"] .card-title {
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .form-label {
    color: #cbd5e1 !important;
}

[data-bs-theme="dark"] .form-control,
[data-bs-theme="dark"] .form-select,
[data-bs-theme="dark"] .form-control-custom,
[data-bs-theme="dark"] #card-element {
    background-color: #0f172a !important;
    color: #f8fafc !important;
    border-color: #475569 !important;
}

[data-bs-theme="dark"] .receipt-breakdown {
    background: #0f172a !important;
    border-color: #334155 !important;
}

[data-bs-theme="dark"] .receipt-row {
    color: #cbd5e1 !important;
}

[data-bs-theme="dark"] .receipt-row.total {
    border-top-color: #334155 !important;
}

[data-bs-theme="dark"] .receipt-row.total span {
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .course-brief h6 {
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .course-brief p {
    color: #94a3b8 !important;
}

[data-bs-theme="dark"] .course-badges .badge {
    background-color: #0f172a !important;
    color: #cbd5e1 !important;
}

[data-bs-theme="dark"] .benefits-check-list li {
    color: #cbd5e1 !important;
}

[data-bs-theme="dark"] .guarantee-box {
    background: rgba(16, 185, 129, 0.1) !important;
    border-color: rgba(16, 185, 129, 0.2) !important;
}

[data-bs-theme="dark"] .guarantee-box h6 {
    color: #34d399 !important;
}

[data-bs-theme="dark"] .guarantee-box p {
    color: #a7f3d0 !important;
}

[data-bs-theme="dark"] .card-footer {
    background-color: #0f172a !important;
    .card-footer {
        background-color: #0f172a !important;
        border-top-color: #334155 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script src="https://www.paypal.com/sdk/js?client-id={{ $paypalConfig['client_id'] ?? 'test' }}&currency={{ $paypalConfig['currency'] ?? 'USD' }}"></script>
<script src="https://checkout.kashier.io/kashier-checkout.js"></script>
<script>
// Dynamic exchange rate configuration
const EXCHANGE_RATES = @json($exchangeRates);

// Initialize Stripe
const stripe = Stripe('{{ $stripeConfig['public_key'] ?? 'pk_test_placeholder' }}');
const elements = stripe.elements();

const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';

// Create premium styled card element
const cardElement = elements.create('card', {
    style: {
        base: {
            fontSize: '16px',
            color: isDarkMode ? '#f8fafc' : '#1e293b',
            fontFamily: 'Cairo, Outfit, system-ui, -apple-system, sans-serif',
            '::placeholder': {
                color: isDarkMode ? '#64748b' : '#94a3b8',
            },
        },
        invalid: {
            color: '#ef4444',
        },
    },
});

// Mount Stripe card element
cardElement.mount('#card-element');

// Handle live validation error messages
cardElement.on('change', function(event) {
    const displayError = document.getElementById('card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
});

// Interactive Payment Method Switcher
document.querySelectorAll('.payment-method-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.payment-method-card').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.payment-form-box').forEach(f => f.classList.remove('active'));

        this.classList.add('active');

        const methodType = this.dataset.method;
        const gateway = this.dataset.gateway;
        const currency = this.dataset.currency;

        document.getElementById(methodType + 'Form').classList.add('active');
        document.getElementById('selectedGateway').value = gateway;
        document.getElementById('selectedPaymentMethod').value = gateway;

        // Dynamic currency display logic
        updateCurrencyDisplay(currency);

        // Update button text and visibility dynamically based on active gateway
        const submitBtn = document.getElementById('submitPaymentBtn');
        if (submitBtn) {
            const submitBtnText = submitBtn.querySelector('.btn-text');
            if (gateway === 'paypal') {
                submitBtn.classList.add('d-none');
            } else {
                submitBtn.classList.remove('d-none');
                if (gateway === 'paymob') {
                    submitBtnText.innerHTML = '<i class="fas fa-mobile-alt me-1"></i> ادفع الآن عبر PayMob بالعملة المحلية';
                    submitBtn.className = "btn btn-violet-custom btn-lg w-100 py-3 d-flex align-items-center justify-content-center gap-2";
                } else if (gateway === 'kashier') {
                    submitBtnText.innerHTML = '<i class="fas fa-shield-alt me-1"></i> ادفع الآن عبر بوابة Kashier المشفرة';
                    submitBtn.className = "btn btn-emerald-custom btn-lg w-100 py-3 d-flex align-items-center justify-content-center gap-2";
                } else {
                    submitBtnText.innerHTML = '<i class="fas fa-lock me-1"></i> إتمام الدفع بالبطاقة الائتمانية';
                    submitBtn.className = "btn btn-indigo btn-lg w-100 py-3 d-flex align-items-center justify-content-center gap-2";
                }
            }
        }

        if (gateway === 'paypal') {
            initializePayPal();
        }
    });
});

// Live exchange rate display handler
const defaultCurrency = "{{ $defaultCurrency }}";

function formatPriceJs(amount, currency) {
    if (currency === 'USD') return '$' + amount.toFixed(2);
    if (currency === 'EGP') return amount.toFixed(2) + ' ج.م';
    if (currency === 'SAR') return amount.toFixed(2) + ' ر.س';
    if (currency === 'EUR') return '€' + amount.toFixed(2);
    if (currency === 'GBP') return '£' + amount.toFixed(2);
    return currency + ' ' + amount.toFixed(2);
}

function updateCurrencyDisplay(currency) {
    const totalPriceDisplay = document.getElementById('totalPriceDisplay');
    const exchangeDisplay = document.getElementById('exchangeDisplay');
    const exchangeRateTip = document.getElementById('exchangeRateTip');
    const baseAmount = parseFloat(totalPriceDisplay.dataset.baseAmount);

    const activeCard = document.querySelector('.payment-method-card.active');
    if (!activeCard) return;
    
    const convertedAmount = parseFloat(activeCard.dataset.amount);

    if (currency !== defaultCurrency) {
        totalPriceDisplay.textContent = formatPriceJs(baseAmount, defaultCurrency);
        exchangeDisplay.textContent = `(${formatPriceJs(convertedAmount, currency)})`;
        exchangeDisplay.classList.remove('d-none');
        
        const fromRate = EXCHANGE_RATES[defaultCurrency];
        const toRate = EXCHANGE_RATES[currency];
        if (fromRate && toRate) {
            const oneFromInTo = (1 / fromRate) * toRate;
            exchangeRateTip.innerHTML = `<i class="fas fa-info-circle text-violet-600 me-1"></i> تم حساب السعر بعملة الدفع ${currency} (معدل الصرف تقريباً 1 ${defaultCurrency} = ${oneFromInTo.toFixed(4)} ${currency})`;
            exchangeRateTip.classList.remove('d-none');
        } else {
            exchangeRateTip.classList.add('d-none');
        }
    } else {
        totalPriceDisplay.textContent = formatPriceJs(baseAmount, defaultCurrency);
        exchangeDisplay.classList.add('d-none');
        exchangeRateTip.classList.add('d-none');
    }
}


// Set initial currency view
document.addEventListener('DOMContentLoaded', () => {
    const activeCard = document.querySelector('.payment-method-card.active');
    if (activeCard) {
        updateCurrencyDisplay(activeCard.dataset.currency);
    }
});

// Initialize PayPal SDK button container
function initializePayPal() {
    // Prevent double rendering
    const container = document.getElementById('paypal-button-container');
    if (container && container.children.length > 0) return;

    paypal.Buttons({
        createOrder: function(data, actions) {
            const activeCard = document.querySelector('.payment-method-card[data-gateway="paypal"]');
            const paypalAmount = activeCard ? activeCard.dataset.amount : '{{ $totalAmount }}';
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: paypalAmount
                    },
                    description: '{{ $course->title }}'
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                processPayment('paypal', { order_id: data.orderID });
            });
        },
        onError: function(err) {
            console.error('PayPal error:', err);
            showError('حدث خطأ في معالجة الدفع عبر PayPal');
        }
    }).render('#paypal-button-container');
}

// Click trigger for Paymob local redirect creation
function initiatePayMobPayment() {
    const submitBtn = document.getElementById('submitPaymentBtn');
    toggleBtnLoading(submitBtn, true);
    processPayment('paymob', {});
}

let isSubmitting = false;

// Payment form submission interceptor (primarily for Stripe, Paymob, Kashier)
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (isSubmitting) {
        console.warn('Checkout submission already in progress, ignoring double click.');
        return false;
    }

    const gateway = document.getElementById('selectedGateway').value;
    const submitBtn = document.getElementById('submitPaymentBtn');

    if (!validateForm()) {
        return false;
    }

    isSubmitting = true;
    toggleBtnLoading(submitBtn, true);

    console.log('Payment form request initiated:', gateway);

    if (gateway === 'stripe') {
        processStripePayment();
    } else if (gateway === 'paymob') {
        initiatePayMobPayment();
    } else if (gateway === 'kashier') {
        processPayment('kashier', {});
    } else {
        processPayment(gateway, {});
    }
});

// Stripe Payment flow
async function processStripePayment() {
    const submitBtn = document.getElementById('submitPaymentBtn');
    toggleBtnLoading(submitBtn, true);

    if (!validateForm()) {
        toggleBtnLoading(submitBtn, false);
        return;
    }

    try {
        const formData = new FormData();
        formData.append('gateway', 'stripe');
        formData.append('payment_method', 'stripe');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        const response = await fetch('{{ $processRoute }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            },
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        console.log('Stripe client secret generated:', result);

        if (result.success && result.data && result.data.client_secret) {
            // Confirm the card charge with Stripe
            const { error, paymentIntent } = await stripe.confirmCardPayment(result.data.client_secret, {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        name: document.querySelector('[name="card_holder"]').value,
                        email: document.querySelector('[name="email"]').value,
                        phone: document.querySelector('[name="phone"]').value
                    }
                }
            });

            if (error) {
                showError(error.message || 'حدث خطأ في تأكيد الدفع');
                toggleBtnLoading(submitBtn, false);
            } else {
                // Confirm webhook / callback status with educational server
                await confirmStripePayment(result.data.payment_intent_id);
            }
        } else {
            showError(result.message || 'حدث خطأ في إنشاء عملية الدفع');
            toggleBtnLoading(submitBtn, false);
        }
    } catch (error) {
        showError('عذراً، فشل الدفع: ' + error.message);
        toggleBtnLoading(submitBtn, false);
    }
}

// Confirm Stripe status API call
async function confirmStripePayment(paymentIntentId) {
    try {
        const response = await fetch('{{ route("payment.confirm.stripe") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                payment_intent_id: paymentIntentId
            })
        });

        const result = await response.json();

        if (result.success) {
            window.location.href = '{{ route("payment.success") }}';
        } else {
            showError(result.message);
            toggleBtnLoading(document.getElementById('submitPaymentBtn'), false);
        }
    } catch (error) {
        showError('فشل تأكيد عملية الدفع خادومياً');
        toggleBtnLoading(document.getElementById('submitPaymentBtn'), false);
    }
}

// Generic AJAX payment handler (for Redirect gateways like Paymob / PayPal)
async function processPayment(gateway, data) {
    const submitBtn = document.getElementById('submitPaymentBtn');
    toggleBtnLoading(submitBtn, true);

    try {
        const formData = new FormData();
        formData.append('gateway', gateway);
        formData.append('phone', document.querySelector('[name="phone"]').value);
        formData.append('country', document.querySelector('[name="country"]').value);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        Object.keys(data).forEach(key => {
            formData.append(key, data[key]);
        });

        const response = await fetch('{{ $processRoute }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            },
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        console.log('AJAX Payment response resolved:', result);

        if (result.success) {
            const kashierData = result.data || result;
            if (gateway === 'kashier') {
                const kashierModalEl = document.getElementById('kashierIframeModal');
                const iframeEl = document.getElementById('kashierIframeContainer');
                // Always load Kashier payment session in the secure embedded iframe modal to keep the student on the platform

                // Embedded iFrame Modal directly in the platform (Keeps student inside platform)
                if (kashierData.redirect_url && iframeEl && kashierModalEl) {
                    iframeEl.src = kashierData.redirect_url;
                    
                    // Direct parent redirect on success/failed page loaded inside iframe
                    iframeEl.onload = function() {
                        try {
                            const iframeUrl = iframeEl.contentWindow.location.href;
                            if (iframeUrl.includes('/payment/success') || iframeUrl.includes('/payment/failed')) {
                                window.location.href = iframeUrl;
                            }
                        } catch (e) {
                            console.log('Iframe is currently on external domain (kashier.io)');
                        }
                    };

                    const bsModal = new bootstrap.Modal(kashierModalEl);
                    bsModal.show();
                    toggleBtnLoading(submitBtn, false);
                } else if (kashierData.redirect_url) {
                    window.location.href = kashierData.redirect_url;
                } else {
                    window.location.href = '{{ url("/payment/success") }}/' + (kashierData.order_id || '');
                }
            } else if (result.data && result.data.redirect_url) {
                window.location.href = result.data.redirect_url;
            } else {
                window.location.href = '{{ route("payment.success") }}';
            }
        } else {
            showError(result.message || 'حدث خطأ غير متوقع أثناء الدفع');
            toggleBtnLoading(submitBtn, false);
        }
    } catch (error) {
        showError('عذراً، فشل الدفع: ' + error.message);
        toggleBtnLoading(submitBtn, false);
    }
}

function validateForm() {
    const gateway = document.getElementById('selectedGateway').value;
    
    // Validate phone for all
    const phone = document.querySelector('[name="phone"]').value;
    if (!phone || phone.trim() === '') {
        showError('رقم الهاتف مطلوب لإتمام الفوترة');
        return false;
    }

    if (gateway === 'stripe') {
        const cardHolder = document.querySelector('[name="card_holder"]').value;
        if (!cardHolder.trim()) {
            showError('اسم حامل البطاقة مطلوب لإتمام الفوترة');
            return false;
        }
    }
    return true;
}

function toggleBtnLoading(btn, isLoading) {
    if (!btn) return;
    if (isLoading) {
        btn.classList.add('loading');
        btn.disabled = true;
        btn.setAttribute('disabled', 'disabled');
        btn.querySelector('.btn-text')?.classList.add('d-none');
        btn.querySelector('.btn-loader')?.classList.remove('d-none');
    } else {
        isSubmitting = false;
        btn.classList.remove('loading');
        btn.disabled = false;
        btn.removeAttribute('disabled');
        btn.querySelector('.btn-text')?.classList.remove('d-none');
        btn.querySelector('.btn-loader')?.classList.add('d-none');
    }
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed bottom-0 start-0 m-4 rounded-3 shadow-lg text-white';
    errorDiv.style.cssText = 'z-index: 99999; background-color: #dc2626 !important; font-family: "Cairo", sans-serif; min-width: 280px;';
    errorDiv.innerHTML = `
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-exclamation-circle fs-5"></i>
            <div><strong>تنبيه:</strong> ${message}</div>
            <button type="button" class="btn-close btn-close-white me-auto" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    `;

    document.body.appendChild(errorDiv);

    setTimeout(() => {
        if (document.body.contains(errorDiv)) {
            errorDiv.remove();
        }
    }, 7000);
}
</script>
@endpush
