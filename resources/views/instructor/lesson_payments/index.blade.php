@extends('layouts.app')

@section('title', 'طلبات دفع الدروس - المدرس')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">طلبات دفع الدروس لكورساتي</h2>
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
                                <a href="{{ route('instructor.lesson-payments.show', $payment) }}" class="btn btn-sm btn-primary">عرض</a>
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


