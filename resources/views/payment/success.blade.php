@extends('layouts.app')

@section('title', 'تم الدفع بنجاح')

@section('content')
<div class="payment-success-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="success-card" data-aos="zoom-in">
                    <!-- Success Animation -->
                    <div class="success-animation">
                        <div class="checkmark-circle">
                            <div class="checkmark"></div>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <div class="success-content text-center">
                        <h1 class="success-title">تم الدفع بنجاح!</h1>
                        <p class="success-subtitle">
                            @if(session('section'))
                                تهانينا! لقد تم شراء القسم بنجاح ويمكنك الآن الوصول إليه
                            @else
                                تهانينا! لقد تم تسجيلك في الكورس بنجاح ويمكنك الآن البدء في التعلم
                            @endif
                        </p>

                        <!-- Order Details -->
                        <div class="order-details">
                            <div class="detail-item">
                                <span class="detail-label">رقم الطلب:</span>
                                <span class="detail-value">#{{ rand(100000, 999999) }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">تاريخ الدفع:</span>
                                <span class="detail-value">{{ now()->format('Y/m/d H:i') }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">المبلغ المدفوع:</span>
                                <span class="detail-value">${{ session('payment_amount', '99.99') }}</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            @if(session('section'))
                                <a href="{{ route('student.courses.show', session('course')) }}"
                                   class="btn btn-primary btn-lg">
                                    <i class="fas fa-play me-2"></i>
                                    عرض القسم المشترى
                                </a>
                            @else
                                <a href="{{ route('student.courses.learn', session('course')) }}"
                                   class="btn btn-primary btn-lg">
                                    <i class="fas fa-play me-2"></i>
                                    ابدأ التعلم الآن
                                </a>
                            @endif
                            <a href="{{ route('student.dashboard') }}"
                               class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                لوحة التحكم
                            </a>
                        </div>

                        <!-- Additional Info -->
                        <div class="additional-info">
                            <div class="info-item">
                                <i class="fas fa-envelope text-primary"></i>
                                <p>تم إرسال إيصال الدفع إلى بريدك الإلكتروني</p>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-headset text-success"></i>
                                <p>يمكنك التواصل مع الدعم الفني في أي وقت</p>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-certificate text-warning"></i>
                                <p>ستحصل على شهادة معتمدة عند إتمام الكورس</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.payment-success-page {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    padding: 50px 0;
}

.success-card {
    background: white;
    border-radius: 30px;
    padding: 60px 40px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    text-align: center;
    position: relative;
    overflow: hidden;
}

.success-card::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.05) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.success-animation {
    margin-bottom: 40px;
    position: relative;
    z-index: 1;
}

.checkmark-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #34d399);
    margin: 0 auto;
    position: relative;
    animation: scaleIn 0.6s ease-out;
}

@keyframes scaleIn {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.checkmark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 30px;
    height: 60px;
    border: solid white;
    border-width: 0 6px 6px 0;
    transform: translate(-50%, -60%) rotate(45deg);
    animation: checkmarkDraw 0.4s ease-out 0.6s both;
}

@keyframes checkmarkDraw {
    0% {
        height: 0;
        width: 0;
        opacity: 0;
    }
    50% {
        height: 0;
        width: 30px;
        opacity: 1;
    }
    100% {
        height: 60px;
        width: 30px;
        opacity: 1;
    }
}

.success-content {
    position: relative;
    z-index: 1;
}

.success-title {
    font-size: 3rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 20px;
    animation: fadeInUp 0.8s ease-out 0.3s both;
}

.success-subtitle {
    font-size: 1.2rem;
    color: #6b7280;
    margin-bottom: 40px;
    line-height: 1.6;
    animation: fadeInUp 0.8s ease-out 0.5s both;
}

.order-details {
    background: #f8fafc;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 40px;
    animation: fadeInUp 0.8s ease-out 0.7s both;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #e5e7eb;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 500;
    color: #6b7280;
}

.detail-value {
    font-weight: 600;
    color: #1f2937;
}

.action-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin-bottom: 50px;
    animation: fadeInUp 0.8s ease-out 0.9s both;
}

.action-buttons .btn {
    padding: 15px 30px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.action-buttons .btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.additional-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    animation: fadeInUp 0.8s ease-out 1.1s both;
}

.info-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 20px;
    border-radius: 12px;
    background: rgba(99, 102, 241, 0.05);
    transition: all 0.3s ease;
}

.info-item:hover {
    transform: translateY(-5px);
    background: rgba(99, 102, 241, 0.1);
}

.info-item i {
    font-size: 2rem;
    margin-bottom: 15px;
}

.info-item p {
    margin: 0;
    color: #374151;
    font-weight: 500;
}

@keyframes fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .success-card {
        padding: 40px 20px;
        border-radius: 20px;
    }

    .success-title {
        font-size: 2.5rem;
    }

    .success-subtitle {
        font-size: 1.1rem;
    }

    .action-buttons {
        flex-direction: column;
        align-items: center;
    }

    .action-buttons .btn {
        width: 100%;
        max-width: 300px;
    }

    .additional-info {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .checkmark-circle {
        width: 100px;
        height: 100px;
    }

    .checkmark {
        width: 25px;
        height: 50px;
        border-width: 0 5px 5px 0;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Confetti animation
function createConfetti() {
    const colors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
    const confettiCount = 50;

    for (let i = 0; i < confettiCount; i++) {
        const confetti = document.createElement('div');
        confetti.style.position = 'fixed';
        confetti.style.width = '10px';
        confetti.style.height = '10px';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.left = Math.random() * 100 + 'vw';
        confetti.style.top = '-10px';
        confetti.style.zIndex = '1000';
        confetti.style.borderRadius = '50%';
        confetti.style.pointerEvents = 'none';

        document.body.appendChild(confetti);

        const animation = confetti.animate([
            {
                transform: 'translateY(-10px) rotate(0deg)',
                opacity: 1
            },
            {
                transform: `translateY(100vh) rotate(${Math.random() * 360}deg)`,
                opacity: 0
            }
        ], {
            duration: Math.random() * 3000 + 2000,
            easing: 'cubic-bezier(0.5, 0, 0.5, 1)'
        });

        animation.onfinish = () => {
            confetti.remove();
        };
    }
}

// Trigger confetti after page load
setTimeout(() => {
    createConfetti();
}, 1000);

// Send analytics event
if (typeof gtag !== 'undefined') {
    gtag('event', 'purchase', {
        'transaction_id': '{{ rand(100000, 999999) }}',
        'value': {{ session('payment_amount', 99.99) }},
        'currency': 'USD',
        'items': [{
            'item_id': '{{ session("course_id", 1) }}',
            'item_name': '{{ session("course_title", "Course") }}',
            'category': 'Course',
            'quantity': 1,
            'price': {{ session('payment_amount', 99.99) }}
        }]
    });
}

// AOS Animation
AOS.init({
    duration: 800,
    easing: 'ease-out',
    once: true
});
</script>
@endpush
@endsection
