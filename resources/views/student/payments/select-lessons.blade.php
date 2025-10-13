@extends('layouts.app')

@section('title', 'اختيار دروس للدفع - ' . $course->title)

@section('content')
<div class="container py-4">
    <h2 class="mb-4">اختيار دروس من الكورس: {{ $course->title }}</h2>

    <form action="{{ route('student.courses.lessons.pay.store', $course) }}" method="POST" enctype="multipart/form-data" id="lessonPaymentForm">
        @csrf

        <div class="accordion" id="sectionsAccordion">
            @foreach($course->sections as $section)
                <div class="accordion-item mb-2">
                    <h2 class="accordion-header" id="heading-{{ $section->id }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $section->id }}" aria-expanded="false" aria-controls="collapse-{{ $section->id }}">
                            {{ $section->title }}
                        </button>
                    </h2>
                    <div id="collapse-{{ $section->id }}" class="accordion-collapse collapse show" aria-labelledby="heading-{{ $section->id }}" data-bs-parent="#sectionsAccordion">
                        <div class="accordion-body">
                            <ul class="list-group">
                                @foreach($section->lessons as $lesson)
                                    @php $isPaid = isset($paidLessonIds) && in_array($lesson->id, $paidLessonIds); @endphp
                                    @if($isPaid)
                                        @continue
                                    @endif
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <input class="form-check-input me-2 lesson-checkbox" type="checkbox" value="{{ $lesson->id }}" id="lesson-{{ $lesson->id }}" name="lessons[]" data-price="{{ $lesson->price ?? 0 }}" {{ in_array($lesson->id, (array) request()->input('lessons', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lesson-{{ $lesson->id }}">{{ $lesson->title }}</label>
                                        </div>
                                        <span class="badge bg-secondary">{{ number_format($lesson->price ?? 0, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card mt-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <label class="form-label">إجمالي المبلغ</label>
                    <div class="h4 mb-0" id="totalAmount">0.00</div>
                </div>
                <div class="ms-3 flex-grow-1">
                    <label for="attachment" class="form-label">مرفق الدفع (مطلوب)</label>
                    <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment" required>
                    @error('attachment')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary ms-3">إرسال طلب الدفع</button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.lesson-checkbox');
    const totalEl = document.getElementById('totalAmount');

    function updateTotal() {
        let total = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) {
                total += parseFloat(cb.getAttribute('data-price') || '0');
            }
        });
        totalEl.textContent = total.toFixed(2);
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateTotal));
    // Initialize total based on preselected lessons from query
    updateTotal();
});
</script>
@endsection


