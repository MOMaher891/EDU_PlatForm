@extends('layouts.app')

@section('title', 'طلب دفع #' . $payment->id)

@section('content')
<div class="container py-4">
    <h2 class="mb-4">طلب دفع دروس #{{ $payment->id }}</h2>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-2"><strong>الطالب:</strong> {{ $payment->student->name ?? '-' }} ({{ $payment->student->email ?? '-' }})</div>
                    <div class="mb-2"><strong>الكورس:</strong> {{ $payment->course->title ?? '-' }}</div>
                    <div class="mb-2"><strong>الدروس المختارة:</strong> {{ $payment->lessons_ids }}</div>
                    <div class="mb-2"><strong>الإجمالي:</strong> {{ number_format($payment->total_cost, 2) }}</div>
                    <div class="mb-2">
                        <strong>الحالة:</strong>
                        @php $status = (int) $payment->status; @endphp
                        <span class="badge {{ $status === 0 ? 'bg-warning' : ($status === 1 ? 'bg-success' : 'bg-danger') }}">
                            {{ $status === 0 ? 'قيد المراجعة' : ($status === 1 ? 'مقبول' : 'مرفوض') }}
                        </span>
                    </div>
                    <div class="mb-2"><strong>التاريخ:</strong> {{ $payment->created_at->format('Y-m-d H:i') }}</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">المرفق</div>
                <div class="card-body">
                    <a class="btn btn-outline-secondary" href="{{ asset('storage/' . ltrim($payment->attachment_path, '/')) }}" target="_blank">عرض / تنزيل المرفق</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">تحديث الحالة</div>
                <div class="card-body">
                    <form action="{{ route('admin.lesson-payments.status', $payment) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">الحالة</label>
                            <select class="form-select" name="status">
                                <option value="0" {{ (int)$payment->status === 0 ? 'selected' : '' }}>قيد المراجعة</option>
                                <option value="1" {{ (int)$payment->status === 1 ? 'selected' : '' }}>مقبول</option>
                                <option value="2" {{ (int)$payment->status === 2 ? 'selected' : '' }}>مرفوض</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">حفظ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


