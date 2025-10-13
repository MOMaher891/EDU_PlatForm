@extends('layouts.app')

@section('title', 'جميع الأنشطة')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>جميع الأنشطة</h5>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-right me-2"></i>عودة للوحة التحكم</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>الوقت</th>
                            <th>المستخدم</th>
                            <th>النوع</th>
                            <th>الوصف</th>
                            <th>الكورس/الدرس</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                            <tr>
                                <td>{{ $activity->created_at->diffForHumans() }}</td>
                                <td>{{ $activity->user->name ?? 'نظام' }}</td>
                                <td><span class="badge bg-light text-dark">{{ $activity->type }}</span></td>
                                <td>{{ $activity->description }}</td>
                                <td>
                                    @if($activity->course)
                                        كورس: {{ $activity->course->title }}
                                    @elseif($activity->lesson)
                                        درس: {{ $activity->lesson->title }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center p-4">لا توجد أنشطة بعد</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
</div>
@endsection


