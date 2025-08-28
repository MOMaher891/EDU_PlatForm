<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('تفاصيل الدرس') }} - {{ $lesson->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Lesson Details -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">معلومات الدرس</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">العنوان</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $lesson->title }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">نوع الملف</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @switch($lesson->file_type)
                                            @case('video')
                                                <span class="text-blue-600">فيديو</span>
                                                @break
                                            @case('pdf')
                                                <span class="text-red-600">PDF</span>
                                                @break
                                            @case('document')
                                                <span class="text-green-600">مستند</span>
                                                @break
                                            @case('quiz')
                                                <span class="text-purple-600">اختبار</span>
                                                @break
                                            @default
                                                {{ $lesson->file_type }}
                                        @endswitch
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">الترتيب</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $lesson->order_index }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">الحالة</label>
                                    <p class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $lesson->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $lesson->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">مجاني</label>
                                    <p class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $lesson->is_free ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $lesson->is_free ? 'نعم' : 'لا' }}
                                        </span>
                                    </p>
                                </div>

                                @if($lesson->video_duration)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">مدة الفيديو</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ gmdate('H:i:s', $lesson->video_duration) }}</p>
                                </div>
                                @endif

                                @if($lesson->video_url)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">رابط الفيديو</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <a href="{{ $lesson->video_url }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                            {{ $lesson->video_url }}
                                        </a>
                                    </p>
                                </div>
                                @endif

                                @if($lesson->file_path)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">مسار الملف</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <a href="{{ $lesson->file_path }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                            {{ $lesson->file_path }}
                                        </a>
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Section and Course Info -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">معلومات القسم والكورس</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">القسم</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $lesson->section->title }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">الكورس</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $lesson->section->course->title }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">المدرب</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $lesson->section->course->instructor->name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">تاريخ الإنشاء</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $lesson->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">آخر تحديث</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $lesson->updated_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    @if($lesson->content)
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">محتوى الدرس</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="prose max-w-none">
                                {!! nl2br(e($lesson->content)) !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex items-center justify-end mt-8 space-x-2 space-x-reverse">
                        <a href="{{ route('admin.lessons.index', $lesson->section) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            العودة إلى الدروس
                        </a>
                        <a href="{{ route('admin.lessons.edit', $lesson) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            تعديل الدرس
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
