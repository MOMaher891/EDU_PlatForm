@extends('layouts.app')

@section('title', 'طلبات دفع الدروس')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">طلبات دفع الدروس</h2>
    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-3" method="GET" action="{{ route('admin.lesson-payments.index') }}">
                <div class="col-md-3">
                    <label class="form-label">الطالب</label>
                    <input type="text" name="student" value="{{ $filters['student'] ?? '' }}" class="form-control" placeholder="الاسم أو البريد">
                </div>
                <div class="col-md-3">
                    <label class="form-label">الكورس</label>
                    <input type="text" name="course" value="{{ $filters['course'] ?? '' }}" class="form-control" placeholder="عنوان الكورس">
                </div>
                <div class="col-md-2">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="">الكل</option>
                        <option value="0" {{ (string)($filters['status'] ?? '') === '0' ? 'selected' : '' }}>قيد المراجعة</option>
                        <option value="1" {{ (string)($filters['status'] ?? '') === '1' ? 'selected' : '' }}>مقبول</option>
                        <option value="2" {{ (string)($filters['status'] ?? '') === '2' ? 'selected' : '' }}>مرفوض</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control">
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary me-2" type="submit">تصفية</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.lesson-payments.index') }}">إعادة ضبط</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الطالب</th>
                        <th>الكورس</th>
                        <th>الدروس</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>{{ $payment->student->name ?? '-' }}</td>
                            <td>{{ $payment->course->title ?? '-' }}</td>
                            <td>{{ $payment->lessons_ids }}</td>
                            <td>{{ number_format($payment->total_cost, 2) }}</td>
                            <td>
                                @php $status = (int) $payment->status; @endphp
                                <span class="badge {{ $status === 0 ? 'bg-warning' : ($status === 1 ? 'bg-success' : 'bg-danger') }}">
                                    {{ $status === 0 ? 'قيد المراجعة' : ($status === 1 ? 'مقبول' : 'مرفوض') }}
                                </span>
                            </td>
                            <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.lesson-payments.show', $payment) }}" class="btn btn-sm btn-primary">عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4">لا توجد طلبات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $payments->links() }}
        </div>
    </div>

</div>
@endsection


