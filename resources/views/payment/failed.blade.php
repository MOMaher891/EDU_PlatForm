@extends('layouts.app')

@section('title', 'تعذر إتمام عملية الدفع')

@section('content')
<div class="payment-failed-page py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center" data-aos="zoom-in">
                <!-- Warning Icon -->
                <div class="failed-icon-wrapper mx-auto mb-4 bg-rose-100 text-rose-600 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="fas fa-times-circle fa-5x"></i>
                </div>

                <h2 class="fw-extrabold text-slate-900 mb-2">عذراً، لم تكتمل عملية الدفع ⚠️</h2>
                <p class="text-slate-600 fs-5 mb-4">
                    {{ $message ?? 'حدث خطأ أو تم إلغاء المعاملة من قبل البوابة الإلكترونية. لم يتم خصم أي مبالغ مالية.' }}
                </p>

                <!-- Failure Details Box -->
                <div class="card border-0 shadow-sm rounded-4 text-start p-4 mb-4 bg-white">
                    <h6 class="fw-bold text-slate-800 mb-2"><i class="fas fa-info-circle text-rose-600 me-2"></i> نصائح وحلول سريعة:</h6>
                    <ul class="text-slate-600 small mb-0 ps-3">
                        <li class="mb-1">تأكد من وجود رصيد كافٍ في البطاقة الائتمانية أو المحفظة الإلكترونية.</li>
                        <li class="mb-1">تأكد من تفعيل الشراء عبر الإنترنت في بطاقتك البنكية.</li>
                        <li class="mb-1">يمكنك استخدام بوابة دفع بديلة (مثل Paymob أو Kashier).</li>
                    </ul>
                </div>

                <!-- Retry Buttons -->
                <div class="d-flex justify-content-center gap-3">
                    <a href="javascript:history.back()" class="btn btn-indigo btn-lg px-5 py-3 rounded-3 shadow">
                        <i class="fas fa-redo me-2"></i> إعادة المحاولة الآن
                    </a>

                    <a href="{{ route('student.courses.index') }}" class="btn btn-outline-secondary btn-lg px-4 py-3 rounded-3">
                        <i class="fas fa-book me-2"></i> تصفح الكورسات
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.payment-failed-page {
    background-color: #f8fafc;
    min-height: 80vh;
}

.failed-icon-wrapper {
    width: 110px;
    height: 110px;
    box-shadow: 0 10px 25px -5px rgba(225, 29, 72, 0.3);
}

.btn-indigo {
    background-color: #4f46e5;
    color: white;
    font-weight: 800;
    border: none;
}

.btn-indigo:hover {
    background-color: #4338ca;
    color: white;
}

.bg-rose-100 { background-color: #ffe4e6; }
.text-rose-600 { color: #e11d48; }
.text-slate-900 { color: #0f172a; }
.text-slate-800 { color: #1e293b; }
.text-slate-600 { color: #475569; }
</style>
@endpush
