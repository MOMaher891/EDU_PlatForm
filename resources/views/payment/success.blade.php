@extends('layouts.app')

@section('title', 'تمت عملية الدفع بنجاح')

@section('content')
<script>
    if (window.self !== window.top) {
        window.top.location.href = window.self.location.href;
    }
</script>
<div class="payment-success-page py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center" data-aos="zoom-in">
                <!-- Animated Success Badge -->
                <div class="success-icon-wrapper mx-auto mb-4 bg-emerald-100 text-emerald-600 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="fas fa-check-circle fa-5x animate-bounce"></i>
                </div>

                <h1 class="fw-extrabold text-slate-900 mb-2">تهانينا! تمت عملية الدفع بنجاح 🎉</h1>
                <p class="text-slate-600 fs-5 mb-4">تم تأكيد اشتراكك وإتاحة المحتوى التعليمي في حسابك فوراً</p>

                <!-- Order Receipt Card -->
                <div class="card border-0 shadow-lg rounded-4 text-start overflow-hidden mb-4">
                    <div class="card-header bg-slate-900 text-white p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-0"><i class="fas fa-receipt text-emerald-400 me-2"></i> إيصال عملية الدفع الإلكترونية</h5>
                            <small class="opacity-75">رقم الطلب: {{ $order->order_number ?? ('#ORD-' . rand(1000, 9999)) }}</small>
                        </div>
                        <span class="badge bg-emerald-500 text-white py-2 px-3 rounded-pill fw-bold">
                            <i class="fas fa-shield-alt me-1"></i> مدفوع ومؤكد (PAID)
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Purchased Item -->
                            <div class="col-12 pb-3 border-bottom">
                                <label class="text-slate-400 small fw-bold">المحتوى المشتري:</label>
                                <h5 class="fw-bold text-slate-800 mb-0">
                                    <i class="fas fa-graduation-cap text-indigo-600 me-2"></i>
                                    {{ $order->payable->title ?? $order->payable->name ?? 'الكورس التعليمي' }}
                                </h5>
                            </div>

                            <!-- Amount & Currency -->
                            <div class="col-md-6">
                                <label class="text-slate-400 small fw-bold">المبلغ المدفوع الصافي:</label>
                                <div class="fs-4 fw-bold text-emerald-600">
                                    {{ number_format($order->total_amount ?? 0, 2) }} {{ $order->currency ?? 'EGP' }}
                                </div>
                            </div>

                            <!-- Timestamp -->
                            <div class="col-md-6">
                                <label class="text-slate-400 small fw-bold">تاريخ المعاملة:</label>
                                <div class="fw-semibold text-slate-700">
                                    {{ optional($order->created_at ?? now())->format('Y-m-d H:i:s A') }}
                                </div>
                            </div>

                            <!-- Student Info -->
                            <div class="col-md-6">
                                <label class="text-slate-400 small fw-bold">اسم الطالب:</label>
                                <div class="fw-semibold text-slate-800">
                                    {{ auth()->user()->name }}
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label class="text-slate-400 small fw-bold">البريد الإلكتروني:</label>
                                <div class="fw-semibold text-slate-800">
                                    {{ auth()->user()->email }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Call to Action Buttons -->
                <div class="d-flex justify-content-center gap-3">
                    @if(isset($order->payable) && method_exists($order->payable, 'getCourse'))
                        <a href="{{ route('student.courses.show', $order->payable->getCourse()) }}" class="btn btn-emerald btn-lg px-5 py-3 rounded-3 shadow">
                            <i class="fas fa-play-circle me-2"></i> ابدأ مشاهدة المحتوى الآن
                        </a>
                    @elseif(isset($order->payable_id))
                        <a href="{{ route('student.courses.show', $order->payable_id) }}" class="btn btn-emerald btn-lg px-5 py-3 rounded-3 shadow">
                            <i class="fas fa-play-circle me-2"></i> ابدأ مشاهدة الكورس الآن
                        </a>
                    @else
                        <a href="{{ route('student.courses.index') }}" class="btn btn-emerald btn-lg px-5 py-3 rounded-3 shadow">
                            <i class="fas fa-play-circle me-2"></i> الانتقال إلى دوراتي التعليمية
                        </a>
                    @endif

                    <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary btn-lg px-4 py-3 rounded-3">
                        <i class="fas fa-tachometer-alt me-2"></i> لوحة التحكم
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.payment-success-page {
    background-color: #f8fafc;
    min-height: 80vh;
}

.success-icon-wrapper {
    width: 110px;
    height: 110px;
    box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3);
}

.btn-emerald {
    background-color: #10b981;
    color: white;
    font-weight: 800;
    border: none;
    transition: all 0.25s ease;
}

.btn-emerald:hover {
    background-color: #059669;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4) !important;
}

.bg-emerald-500 { background-color: #10b981; }
.bg-emerald-100 { background-color: #d1fae5; }
.text-emerald-600 { color: #059669; }
.text-emerald-400 { color: #34d399; }
.bg-slate-900 { background-color: #0f172a; }
.text-slate-900 { color: #0f172a; }
.text-slate-800 { color: #1e293b; }
.text-slate-600 { color: #475569; }
</style>
@endpush
