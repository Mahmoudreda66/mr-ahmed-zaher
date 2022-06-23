@extends('studentsApplication.layouts.app', ['nav_transparent' => false])

@section('title')
التقديم الإلكتروني للطالب
@endsection

@section('css')
<style>
    button.btn.btn-success{
        background-color: #1ebb72;
        border-color: #1ebb72;
    }

    button.btn.btn-success:hover {
        background-color: #1ca364;
        border-color: #1ca364;
    }
</style>
@endsection

@section('content')
<div class="container mt-5">
	<div class="bg-white p-3">
        @if($appliactionStatus != 0)
        <form action="{{ route('students.store', ['back_to' => 'parents']) }}" method="post">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6 col-12 mb-3 mb-md-0">
                    <label class="form-label" for="name" class="form-label">الإسم</label>
                    <input type="text" name="name" autofocus placeholder="إسم الطالب" id="name" class="form-control" value="{{ old('name') }}">
                    @error('name')
                    <small class="invalid-feedback d-block">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6 col-12">
                    <label class="form-label" for="level" class="form-label">المرحلة</label>
                    <select name="level" id="level" class="form-control">
                        <option disabled selected value="NULL">إختر المرحلة</option>
                        @foreach ($levels as $level)
                        <option
                        {{ old('level') == $level->id ? 'selected' : '' }}
                        value="{{ $level->id }}">
                            {{ $level->name_ar }}
                        </option>
                        @endforeach
                    </select>
                    @error('level')
                    <small class="invalid-feedback d-block">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-12 mb-3 mb-md-0">
                    <div class="row">
                        <div class="col-md-4 col-12 mb-3">
                            <label class="form-label" for="mobile" class="form-label">رقم الهاتف</label>
                            <input type="number" value="{{ old('mobile') }}" name="mobile" placeholder="رقم ولي الأمر" id="mobile" class="form-control">
                            @error('mobile')
                            <small class="invalid-feedback d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4 col-12 mb-3">
                            <label class="form-label" for="mobile2" class="form-label">رقم هاتف آخر</label>
                            <input type="number" value="{{ old('mobile2') }}" name="mobile2" placeholder="رقم ولي أمر آخر" id="mobile2" class="form-control">
                            @error('mobile2')
                            <small class="invalid-feedback d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4 col-12">
                            <label class="form-label" for="student_mobile" class="form-label">رقم هاتف الطالب</label>
                            <input type="number" value="{{ old('student_mobile') }}" name="student_mobile" placeholder="رقم هاتف الطالب" id="student_mobile" class="form-control">
                            @error('student_mobile')
                            <small class="invalid-feedback d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12 mb-3 mb-md-0">
                    <label class="form-label" for="gender" class="form-label">الجنس</label>
                    <select name="gender" id="gender" class="form-control">
                        <option disabled selected value="NULL">إختر الجنس</option>
                        <option
                        {{ old('gender') === "0" ? 'selected' : '' }}
                        value="0">ذكر</option>
                        <option
                        {{ old('gender') === "1" ? 'selected' : '' }}
                        value="1">أنثى</option>
                    </select>
                    @error('gender')
                    <small class="invalid-feedback d-block">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label" for="edu_type">نوع التعليم</label>
                    <select name="edu_type" id="edu_type" class="form-control">
                        <option value="0" {{ old('edu_type') === '0' ? 'selected' : '' }}>تعليم عربي</option>
                        <option value="1" {{ old('edu_type') === '1' ? 'selected' : '' }}>تعليم لغات</option>
                    </select>
                    @error('edu_type')
                    <small class="invalid-feedback d-block">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="d-grid gap-2">
                <button class="btn btn-success">
                    <i class="fas fa-key"></i>
                    تقديم
                </button>
            </div>
        </form>
        @else
        <div class="alert alert-info text-center">
            <i class="fas fa-ban"></i>
            عذراً! التقديم الإلكتروني غير متاح حالياً.
        </div>
        <div>
            <span>رقم الهاتف: </span>
            <span>{{ $centerPhoneNumber }}</span>
        </div>
        @endif
	</div>
</div>
@endsection