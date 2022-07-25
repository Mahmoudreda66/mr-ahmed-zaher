@extends('admin.layouts.app', ['activePage' => 'students.create', 'titlePage' => "إضافة طالب"])

@section('title')
إضافة طالب
@endsection

@section('content')

<div class="content">
    @if(Session::has('print'))
    <script>
        window.open('{{ route("students.print", Session::get("print")) }}', 'طباعة بيانات الطالب', 'fullscreen=no,height=450,left=0,resizable=no,status=no,width=400,titlebar=yes,menubar=no');
    </script>
    @endif

    @if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif
    
    <div class="container-fluid bg-white p-4">
        <form action="{{ route('students.store') }}" id="create-form" class="mb-0" method="post" autocomplete="off">
            @csrf
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="name" class="form-label">الإسم</label>
                    <input type="text" name="name" placeholder="إسم الطالب" id="name" class="form-control" value="{{ old('name') }}">
                    @error('name')
                    <small class="invalid-feedback d-block">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6 col-12 mb-3">
                    <label for="level" class="form-label">الصف</label>
                    <select name="level" id="level" class="form-control">
                        <option disabled selected value="NULL">إختر الصف</option>
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
            <div class="row mt-3">
                <div class="col-md-6 col-12 mb-3">
                    <div class="row">
                        <div class="col-4 mb-3">
                            <label for="mobile" class="form-label">رقم هاتف ولي الأمر</label>
                            <input type="number" value="{{ old('mobile') }}" name="mobile" placeholder="رقم الهاتف" id="mobile" class="form-control">
                            @error('mobile')
                            <small class="invalid-feedback d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-4 mb-3">
                            <label for="mobile2" class="form-label">رقم هاتف آخر</label>
                            <input type="number" value="{{ old('mobile2') }}" name="mobile2" placeholder="رقم هاتف آخر" id="mobile2" class="form-control">
                            @error('mobile2')
                            <small class="invalid-feedback d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-4 mb-3">
                            <label for="student_mobile" class="form-label">رقم هاتف الطالب</label>
                            <input type="number" value="{{ old('student_mobile') }}" name="student_mobile" placeholder="رقم هاتف الطالب" id="student_mobile" class="form-control">
                            @error('student_mobile')
                            <small class="invalid-feedback d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12 mb-3">
                    <label for="gender" class="form-label">الجنس</label>
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
            <div class="row mt-3">
                <div class="col-md-6 col-12 mb-3">
                    <label for="edu_type">نوع التعليم</label>
                    <select name="edu_type" id="edu_type" class="form-control">
                        <option value="0" {{ old('edu_type') === '0' ? 'selected' : '' }}>تعليم عربي</option>
                        <option value="1" {{ old('edu_type') === '1' ? 'selected' : '' }}>تعليم لغات</option>
                    </select>
                    @error('edu_type')
                    <small class="invalid-feedback d-block">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6 col-12 mb-3">
                    <label for="given_money">دفع عند الحجز</label>
                    <input type="number" class="form-control" id="given_money" name="given_money" placeholder="المقدم">
                </div>
            </div>
            <div class="d-none-light disvision mb-3" id="division-container">
                <div class="row">
                    <div class="col-12">
                        <label for="division" class="form-label">الشعبة</label>
                        <select name="division" id="division" class="form-control">
                            <option value="" selected>إختر الشعبة</option>
                            <option value="0" {{ old('division') === '0' ? 'selected' : '' }}>الشعبة العلمية</option>
                            <option value="1" {{ old('division') === '1' ? 'selected' : '' }}>الشعبة الأدبية</option>
                        </select>
                        @error('division')
                        <small class="invalid-feedback d-block">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="d-none-light sub_language mb-3" id="sub_language-container">
                <div class="row">
                    <div class="col-12">
                        <label for="sub_language" class="form-label">اللغة الثانية</label>
                        <select name="sub_language" id="sub_language" class="form-control">
                            <option value="" selected>إختر اللغة الثانية</option>
                            <option
                            {{ old('sub_language') === '0' ? 'selected' : '' }}
                            value="0">اللغة الفرنسية</option>
                            <option
                            {{ old('sub_language') === '1' ? 'selected' : '' }}
                            value="1">اللغة الألمانية</option>
                        </select>
                        @error('sub_language')
                        <small class="invalid-feedback d-block">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
            @if($choosing_teachers)
            <div class="teachers-container d-none-light border-top pt-2 mt-2" id="teachers-container">
                <hr>
                <div class="row">
                    @foreach ($subjects as $subject)
                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                        <label for="{{ $subject->name_en }}_id" class="form-label">
                            ال{{ $subject->name_ar }}
                        </label>
                        <select
                        data-division="{{ $subject->division ?? 'NULL'}}"
                        data-level="{{ $subject->level }}"
                        name="{{ $subject->name_en }}"
                        id="{{ $subject->name_en }}_id"
                        class="form-control">
                            <option selected value="NULL">
                                إختر المعلم
                            </option>
                            @foreach ($subject->teachers as $i => $teacher)
                            <option
                            {{ $teacher->id == old($subject->name_en) ? 'selected' : '' }}
                            data-levels="@foreach(json_decode($teacher->levels) as $level){{ $level }},@endforeach"
                            value="{{ $teacher->id }}" class="t-opt-s">
                                {{ $teacher->profile->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endforeach
                </div>  
            </div>
            @endif
            <button class="btn btn-success">إضافة</button>
        </form>
    </div>
</div>
@endsection

@section('js')

@if($errors->any())
@foreach($errors->all() as $error)
<script>
    $.notify("{{ $error }}", "error");
</script>
@endforeach
@endif

<script src="{{ asset('dist/js/create_student.js') }}"></script>
@if($choosing_teachers)
<script src="{{ asset('dist/js/filter_inputs.js') }}"></script>
@endif
@endsection