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
        @if(Session::has('open_modal'))
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">تم التقديم بنجاح</h5>
                    </div>
                    <div class="modal-body">
                        <p style="font-weight: 500;" class="mb-0">
                            <span class="mb-2 d-block">
                                تم تقديم بياناتك بنجاح. لتأكيد الحجز برجاء التوجه إلى السنتر وإعلامنا بالكود الخاص بك. <br> أو الإتصال على: 
                            </span>
                            <span class="d-block">رقم الهاتف: <span>{{ $centerPhoneNumber }}</span></span>
                            <span class="d-block">كود الطالب: <strong>{{ Session::get('student_code') }}</strong></span>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">موافق</button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($appliactionStatus != 0)
        <form
        action="{{ route('studentsApplication.store', ['back_to' => 'students_application']) }}"
        method="post">
            @csrf
            <input type="hidden" name="confirm" value="1">
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
                            <label class="form-label" for="mobile" class="form-label">رقم ولي الأمر</label>
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
                <div class="col-md-6 col-12 mb-3 mb-md-0">
                    <label class="form-label" for="edu_type">نوع التعليم</label>
                    <select name="edu_type" id="edu_type" class="form-control">
                        <option value="0" {{ old('edu_type') === '0' ? 'selected' : '' }}>تعليم عربي</option>
                        <option value="1" {{ old('edu_type') === '1' ? 'selected' : '' }}>تعليم لغات</option>
                    </select>
                    @error('edu_type')
                    <small class="invalid-feedback d-block">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6 col-12 mb-3 mb-md-0" id="divisionSelect" style="display: none;">
                    <label for="division" class="form-label">الشعبة</label>
                    <select name="division" id="division" class="form-control">
                        <option value="NULL" selected disabled>إختر الشعبة</option>
                        <option value="0" {{ old('division') === '0' ? 'selected' : '' }}>الشعبة العلمية</option>
                        <option value="1" {{ old('division') === '1' ? 'selected' : '' }}>الشعبة الأدبية</option>
                    </select>
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

@section('js')
<script src="{{ asset('dist/js/students_application.js') }}"></script>
@if(Session::has('open_modal'))
<script>
    let successModal = new bootstrap.Modal(document.getElementById('successModal'));

    successModal.show();
</script>
@endif
@endsection