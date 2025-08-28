@extends('layouts.app')

@php
    $stripeConfig = config('payment.gateways.stripe');
    $paypalConfig = config('payment.gateways.paypal');
    $paymobConfig = config('payment.gateways.paymob');
@endphp

@section('title', 'إتمام الشراء - ' . $course->title)

@section('content')
<div class="checkout-page">
    <!-- Header -->
    <div class="checkout-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="checkout-title">
                        <i class="fas fa-shopping-cart me-3"></i>
                        إتمام الشراء
                    </h1>
                    <p class="checkout-subtitle">أكمل عملية الشراء للوصول إلى الكورس</p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="security-badges">
                        <span class="security-badge">
                            <i class="fas fa-shield-alt me-1"></i>
                            دفع آمن
                        </span>
                        <span class="security-badge">
                            <i class="fas fa-lock me-1"></i>
                            مشفر SSL
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Payment Form -->
            <div class="col-lg-8">
                <div class="payment-form-section" data-aos="fade-right">
                    <!-- Progress Steps -->
                    <div class="payment-steps mb-4">
                        <div class="step active" data-step="1">
                            <div class="step-number">1</div>
                            <div class="step-label">معلومات الدفع</div>
                        </div>
                        <div class="step" data-step="2">
                            <div class="step-number">2</div>
                            <div class="step-label">مراجعة الطلب</div>
                        </div>
                        <div class="step" data-step="3">
                            <div class="step-number">3</div>
                            <div class="step-label">تأكيد الدفع</div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="card border-0 shadow-lg mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-credit-card me-2"></i>
                                اختر طريقة الدفع
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="payment-methods">
                                @if($stripeConfig['enabled'])
                                <div class="payment-method active" data-method="stripe" data-gateway="stripe">
                                    <div class="method-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="method-info">
                                        <h6>بطاقة ائتمان</h6>
                                        <p>Visa, Mastercard, American Express</p>
                                    </div>
                                    <div class="method-check">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                @endif

                                @if($paypalConfig['enabled'])
                                <div class="payment-method" data-method="paypal" data-gateway="paypal">
                                    <div class="method-icon">
                                        <i class="fab fa-paypal"></i>
                                    </div>
                                    <div class="method-info">
                                        <h6>PayPal</h6>
                                        <p>ادفع بأمان باستخدام PayPal</p>
                                    </div>
                                    <div class="method-check">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                @endif

                                @if($paymobConfig['enabled'])
                                <div class="payment-method" data-method="paymob" data-gateway="paymob">
                                    <div class="method-icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div class="method-info">
                                        <h6>PayMob</h6>
                                        <p>ادفع عبر المحفظة الإلكترونية</p>
                                    </div>
                                    <div class="method-check">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Payment Forms -->
                    <form id="paymentForm" method="POST" action="{{ route('payment.process', $course) }}">
                        @csrf
                        <input type="hidden" name="gateway" id="selectedGateway" value="stripe">
                        <input type="hidden" name="payment_method" id="selectedPaymentMethod" value="stripe">

                                                <!-- Stripe Credit Card Form -->
                        <div class="payment-form active" id="stripeForm">
                            <div class="card border-0 shadow-lg">
                                <div class="card-header bg-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-credit-card me-2"></i>
                                        معلومات البطاقة الائتمانية
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="card-element" class="mb-3">
                                        <!-- Stripe Elements will be inserted here -->
                                    </div>
                                    <div id="card-errors" class="text-danger mb-3" role="alert"></div>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">اسم حامل البطاقة</label>
                                            <input type="text" class="form-control" name="card_holder"
                                                   placeholder="الاسم كما هو مكتوب على البطاقة" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PayPal Form -->
                        <div class="payment-form" id="paypalForm">
                            <div class="card border-0 shadow-lg">
                                <div class="card-body text-center py-5">
                                    <div class="paypal-info">
                                        <i class="fab fa-paypal fa-4x text-primary mb-3"></i>
                                        <h5>الدفع عبر PayPal</h5>
                                        <p class="text-muted">سيتم توجيهك إلى PayPal لإتمام عملية الدفع بأمان</p>
                                        <div class="paypal-benefits">
                                            <div class="benefit-item">
                                                <i class="fas fa-shield-alt text-success me-2"></i>
                                                حماية المشتري
                                            </div>
                                            <div class="benefit-item">
                                                <i class="fas fa-lock text-success me-2"></i>
                                                تشفير متقدم
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <div id="paypal-button-container"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                                                <!-- PayMob Form -->
                        <div class="payment-form" id="paymobForm">
                            <div class="card border-0 shadow-lg">
                                <div class="card-header bg-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-mobile-alt me-2"></i>
                                        الدفع عبر PayMob
                                    </h5>
                                </div>
                                <div class="card-body text-center py-5">
                                    <div class="paymob-info">
                                        <i class="fas fa-mobile-alt fa-4x text-primary mb-3"></i>
                                        <h5>الدفع عبر المحفظة الإلكترونية</h5>
                                        <p class="text-muted">ادفع بسهولة عبر المحفظة الإلكترونية أو البطاقات</p>
                                        <div class="paymob-benefits">
                                            <div class="benefit-item">
                                                <i class="fas fa-shield-alt text-success me-2"></i>
                                                دفع آمن
                                            </div>
                                            <div class="benefit-item">
                                                <i class="fas fa-bolt text-success me-2"></i>
                                                معالجة سريعة
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <button type="button" class="btn btn-primary btn-lg" onclick="initiatePayMobPayment()">
                                                <i class="fas fa-mobile-alt me-2"></i>
                                                ادفع عبر PayMob
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Information -->
                        <div class="card border-0 shadow-lg mt-4">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-user me-2"></i>
                                    معلومات الفوترة
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
                                        <label class="form-label">رقم الهاتف</label>
                                        <input type="tel" class="form-control" name="phone" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الدولة</label>
                                        <select class="form-select" name="country" required>
                                            <option value="">اختر الدولة</option>
                                            <option value="SA">السعودية</option>
                                            <option value="AE">الإمارات</option>
                                            <option value="EG">مصر</option>
                                            <option value="JO">الأردن</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="order-summary-section" data-aos="fade-left">
                    <div class="card border-0 shadow-lg sticky-top" style="top: 100px;">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-receipt me-2"></i>
                                ملخص الطلب
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Course Info -->
                            <div class="course-summary">
                                <div class="course-image">
                                    <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80' }}"
                                         alt="{{ $course->title }}" class="img-fluid rounded">
                                </div>
                                <div class="course-details mt-3">
                                    <h6 class="course-title">{{ $course->title }}</h6>
                                    <p class="course-instructor">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $course->instructor->name }}
                                    </p>
                                    <div class="course-meta">
                                        <span class="meta-item">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $course->duration_hours }} ساعة
                                        </span>
                                        <span class="meta-item">
                                            <i class="fas fa-play-circle me-1"></i>
                                            {{ $course->getTotalLessons() }} درس
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Price Breakdown -->
                            <div class="price-breakdown">
                                <div class="price-item">
                                    <span>سعر الكورس</span>
                                    <span>${{ $course->price }}</span>
                                </div>

                                @if($course->discount_price)
                                    <div class="price-item discount">
                                        <span>الخصم ({{ $course->getDiscountPercentage() }}%)</span>
                                        <span>-${{ $course->price - $course->discount_price }}</span>
                                    </div>
                                @endif

                                <div class="price-item">
                                    <span>الضريبة (15%)</span>
                                    <span>${{ number_format($course->getEffectivePrice() * 0.15, 2) }}</span>
                                </div>

                                <hr>

                                <div class="price-item total">
                                    <span class="fw-bold">المجموع الكلي</span>
                                    <span class="fw-bold text-primary">${{ number_format($course->getEffectivePrice() * 1.15, 2) }}</span>
                                </div>
                            </div>

                            <hr>

                            <!-- Features -->
                            <div class="course-features">
                                <h6 class="features-title">ما ستحصل عليه:</h6>
                                <ul class="features-list">
                                    <li>
                                        <i class="fas fa-infinity text-success me-2"></i>
                                        وصول مدى الحياة
                                    </li>
                                    <li>
                                        <i class="fas fa-mobile-alt text-success me-2"></i>
                                        متاح على جميع الأجهزة
                                    </li>
                                    <li>
                                        <i class="fas fa-certificate text-success me-2"></i>
                                        شهادة إتمام
                                    </li>
                                    <li>
                                        <i class="fas fa-headset text-success me-2"></i>
                                        دعم فني
                                    </li>
                                </ul>
                            </div>

                            <hr>

                            <!-- Guarantee -->
                            <div class="guarantee-section text-center">
                                <div class="guarantee-badge">
                                    <i class="fas fa-shield-alt text-success fa-2x mb-2"></i>
                                    <h6 class="text-success">ضمان استرداد المال</h6>
                                    <p class="text-muted small">خلال 30 يوم من تاريخ الشراء</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <button type="submit" form="paymentForm" class="btn btn-primary btn-lg w-100 payment-btn">
                                <i class="fas fa-lock me-2"></i>
                                <span class="btn-text">إتمام الدفع الآمن</span>
                                <div class="btn-loader d-none">
                                    <div class="spinner-border spinner-border-sm me-2"></div>
                                    جاري المعالجة...
                                </div>
                            </button>
                            <p class="text-center text-muted small mt-2 mb-0">
                                <i class="fas fa-lock me-1"></i>
                                معلوماتك محمية بتشفير SSL 256-bit
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.checkout-page {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding-bottom: 50px;
}

.checkout-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 30px 0;
    margin-bottom: 30px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.checkout-title {
    color: white;
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.checkout-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    margin: 0;
}

.security-badges {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
}

.security-badge {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.payment-steps {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-bottom: 40px;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

.step::after {
    content: '';
    position: absolute;
    top: 15px;
    left: 50px;
    width: 80px;
    height: 2px;
    background: #e5e7eb;
    z-index: -1;
}

.step:last-child::after {
    display: none;
}

.step.active::after {
    background: #6366f1;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e5e7eb;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.step.active .step-number {
    background: #6366f1;
    color: white;
}

.step-label {
    font-size: 0.9rem;
    color: #6b7280;
    font-weight: 500;
}

.step.active .step-label {
    color: #6366f1;
    font-weight: 600;
}

.payment-methods {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.payment-method {
    display: flex;
    align-items: center;
    padding: 20px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-method:hover {
    border-color: #6366f1;
    background: rgba(99, 102, 241, 0.02);
}

.payment-method.active {
    border-color: #6366f1;
    background: rgba(99, 102, 241, 0.05);
}

.method-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: rgba(99, 102, 241, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #6366f1;
    margin-right: 15px;
}

.method-info {
    flex: 1;
}

.method-info h6 {
    margin: 0 0 5px 0;
    font-weight: 600;
    color: #1f2937;
}

.method-info p {
    margin: 0;
    color: #6b7280;
    font-size: 0.9rem;
}

.method-check {
    color: #6366f1;
    font-size: 1.2rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.payment-method.active .method-check {
    opacity: 1;
}

.payment-form {
    display: none;
}

.payment-form.active {
    display: block;
}

.card-input {
    font-family: 'Courier New', monospace;
    font-size: 1.1rem;
    letter-spacing: 2px;
}

.paypal-info {
    padding: 20px;
}

.paypal-benefits {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-top: 20px;
}

.benefit-item {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
    color: #374151;
}

.bank-details {
    background: #f8fafc;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #e5e7eb;
}

.detail-item:last-child {
    border-bottom: none;
}

.course-summary {
    text-align: center;
}

.course-image img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.course-title {
    font-weight: 600;
    color: #1f2937;
    margin: 15px 0 8px 0;
}

.course-instructor {
    color: #6b7280;
    margin: 0 0 10px 0;
    font-size: 0.9rem;
}

.course-meta {
    display: flex;
    justify-content: space-around;
    font-size: 0.8rem;
    color: #6b7280;
}

.price-breakdown {
    margin: 20px 0;
}

.price-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
}

.price-item.discount {
    color: #10b981;
}

.price-item.total {
    font-size: 1.1rem;
    padding: 15px 0;
    border-top: 2px solid #e5e7eb;
    margin-top: 10px;
}

.features-title {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 15px;
}

.features-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.features-list li {
    padding: 8px 0;
    color: #374151;
    font-size: 0.9rem;
}

.guarantee-badge {
    padding: 20px;
    background: rgba(16, 185, 129, 0.05);
    border-radius: 12px;
}

.payment-btn {
    position: relative;
    overflow: hidden;
    font-weight: 600;
    font-size: 1.1rem;
    padding: 15px;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.payment-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
}

.payment-btn.loading .btn-text {
    display: none;
}

.payment-btn.loading .btn-loader {
    display: flex !important;
    align-items: center;
    justify-content: center;
}

@media (max-width: 768px) {
    .checkout-title {
        font-size: 2rem;
    }

    .security-badges {
        justify-content: center;
        margin-top: 20px;
    }

    .payment-steps {
        gap: 15px;
    }

    .step::after {
        width: 40px;
        left: 35px;
    }

    .payment-methods {
        gap: 10px;
    }

    .payment-method {
        padding: 15px;
    }

    .method-icon {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }

    .paypal-benefits {
        flex-direction: column;
        gap: 15px;
    }

    .course-meta {
        flex-direction: column;
        gap: 5px;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script src="https://www.paypal.com/sdk/js?client-id={{ $paypalConfig['client_id'] ?? 'test' }}&currency={{ $paypalConfig['currency'] ?? 'USD' }}"></script>
<script>
// Initialize Stripe
const stripe = Stripe('{{ $stripeConfig['public_key'] ?? 'pk_test_placeholder' }}');
const elements = stripe.elements();

// Create card element
const cardElement = elements.create('card', {
    style: {
        base: {
            fontSize: '16px',
            color: '#424770',
            '::placeholder': {
                color: '#aab7c4',
            },
        },
        invalid: {
            color: '#9e2146',
        },
    },
});

// Mount card element
cardElement.mount('#card-element');

// Handle card errors
cardElement.on('change', function(event) {
    const displayError = document.getElementById('card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
});

// Payment method selection
document.querySelectorAll('.payment-method').forEach(method => {
    method.addEventListener('click', function() {
        // Remove active class from all methods
        document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('active'));
        document.querySelectorAll('.payment-form').forEach(f => f.classList.remove('active'));

        // Add active class to selected method
        this.classList.add('active');

                // Show corresponding form and update gateway
        const methodType = this.dataset.method;
        const gateway = this.dataset.gateway;
        document.getElementById(methodType + 'Form').classList.add('active');
        document.getElementById('selectedGateway').value = gateway;
        document.getElementById('selectedPaymentMethod').value = gateway;

        // Initialize payment method specific features
        if (gateway === 'paypal') {
            initializePayPal();
        }
    });
});



// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
        toast.style.zIndex = '1060';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check me-2"></i>
                    تم نسخ النص بنجاح!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        setTimeout(() => {
            document.body.removeChild(toast);
        }, 3000);
    });
}

// Initialize PayPal
function initializePayPal() {
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '{{ $course->getEffectivePrice() }}'
                    },
                    description: '{{ $course->title }}'
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                // Payment completed successfully
                processPayment('paypal', { order_id: data.orderID });
            });
        },
        onError: function(err) {
            console.error('PayPal error:', err);
            showError('حدث خطأ في معالجة الدفع عبر PayPal');
        }
    }).render('#paypal-button-container');
}

// Initialize PayMob payment
function initiatePayMobPayment() {
    const submitBtn = document.querySelector('.payment-btn');
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;

    // Process PayMob payment
    processPayment('paymob', {});
}

// Form submission for Stripe
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const gateway = document.getElementById('selectedGateway').value;
    console.log('Form submitted with gateway:', gateway);

    if (gateway === 'stripe') {
        console.log('Processing Stripe payment...');
        processStripePayment();
    } else {
        console.log('Processing payment with gateway:', gateway);
        processPayment(gateway, {});
    }
});

// Process Stripe payment
async function processStripePayment() {
    const submitBtn = document.querySelector('.payment-btn');
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;

    // Validate form
    if (!validateForm()) {
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
        return;
    }

    try {
        // Create payment intent on server
        const formData = new FormData();
        formData.append('gateway', 'stripe');
        formData.append('payment_method', 'stripe');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        const response = await fetch('{{ route("payment.process", $course) }}', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        console.log('Payment result:', result);

        if (result.success) {
            // Confirm payment with Stripe
            const { error, paymentIntent } = await stripe.confirmCardPayment(result.client_secret, {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        name: document.querySelector('[name="card_holder"]').value
                    }
                }
            });

            if (error) {
                console.error('Stripe error:', error);
                showError(error.message || 'حدث خطأ في تأكيد الدفع');
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            } else {
                // Payment successful, confirm with server
                await confirmStripePayment(result.payment_intent_id);
            }
        } else {
            console.error('Server error:', result);
            showError(result.message || 'حدث خطأ في معالجة الدفع');
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
        }
    } catch (error) {
        console.error('Payment error:', error);
        showError('حدث خطأ في معالجة الدفع: ' + error.message);
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
    }
}

// Confirm Stripe payment with server
async function confirmStripePayment(paymentIntentId) {
    try {
        const response = await fetch('{{ route("payment.confirm.stripe") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                payment_intent_id: paymentIntentId
            })
        });

        const result = await response.json();

        if (result.success) {
            // Redirect to success page
            window.location.href = '{{ route("payment.success") }}';
        } else {
            showError(result.message);
            const submitBtn = document.querySelector('.payment-btn');
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
        }
    } catch (error) {
        console.error('Confirmation error:', error);
        showError('حدث خطأ في تأكيد الدفع');
        const submitBtn = document.querySelector('.payment-btn');
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
    }
}

// Process payment for other gateways
async function processPayment(gateway, data) {
    try {
        console.log('Processing payment with gateway:', gateway, 'data:', data);

        const formData = new FormData();
        formData.append('gateway', gateway);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        // Add additional data
        Object.keys(data).forEach(key => {
            formData.append(key, data[key]);
        });

        const response = await fetch('{{ route("payment.process", $course) }}', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        console.log('Payment result:', result);

        if (result.success) {
            if (result.data && result.data.redirect_url) {
                console.log('Redirecting to:', result.data.redirect_url);
                window.location.href = result.data.redirect_url;
            } else {
                console.log('Redirecting to success page');
                window.location.href = '{{ route("payment.success") }}';
            }
        } else {
            console.error('Payment failed:', result.message);
            showError(result.message || 'حدث خطأ في معالجة الدفع');
            const submitBtn = document.querySelector('.payment-btn');
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
        }
    } catch (error) {
        console.error('Payment error:', error);
        showError('حدث خطأ في معالجة الدفع: ' + error.message);
        const submitBtn = document.querySelector('.payment-btn');
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
    }
}

// Form validation
function validateForm() {
    const gateway = document.getElementById('selectedGateway').value;
    let isValid = true;

    if (gateway === 'stripe') {
        const cardHolder = document.querySelector('[name="card_holder"]').value;

        if (!cardHolder.trim()) {
            showError('اسم حامل البطاقة مطلوب');
            isValid = false;
        }
    }

    return isValid;
}

function showError(message) {
    console.error('Error displayed:', message);

    // Create error alert
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3';
    errorDiv.style.zIndex = '1060';
    errorDiv.innerHTML = `
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>خطأ:</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(errorDiv);

    // Auto-remove after 10 seconds
    setTimeout(() => {
        if (document.body.contains(errorDiv)) {
            document.body.removeChild(errorDiv);
        }
    }, 10000);

    // Also show in console for debugging
    console.error('Payment Error:', message);
}

// AOS Animation
AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true
});
</script>
@endpush
@endsection
