@extends('layouts.app')

@section('title', 'الملف الشخصي - المسؤول')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>الملف الشخصي (مسؤول)</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">الاسم</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <hr>
                        <div class="mb-3">
                            <label class="form-label">كلمة المرور الجديدة</label>
                            <input type="password" class="form-control" name="password" placeholder="اتركها فارغة إن لم ترغب في التغيير">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">تأكيد كلمة المرور</label>
                            <input type="password" class="form-control" name="password_confirmation" placeholder="أعد إدخال كلمة المرور">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


