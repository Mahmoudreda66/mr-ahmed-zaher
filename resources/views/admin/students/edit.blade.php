@extends('admin.layouts.app', ['activePage' => 'students.index', 'titlePage' => "تعديل بيانات الطالب " . $student->name])

@section('title')
تعديل بيانات الطالب {{ $student->name }}
@endsection

@section('content')

<?php

use App\Models\Admin\StudentTeachers;

function getTeacher($subject, $student)
{
    $teacherInfo = StudentTeachers::where([
        ['student_id', $student]
    ])
    ->select('teachers')
    ->first();

    if($teacherInfo){
        $info = $teacherInfo->toArray();
        if (isset($info['teachers'][$subject])) {
            return $info['teachers'][$subject];
        }else{
            return null;
        }
    }else{
        return null;
    }
}
?>

<div class="content">
    @if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    <div class="container-fluid bg-white p-4">
        <form action="{{ route('students.update', $student->id) }}" id="create-form" class="mb-0" method="post" autocomplete="off">
            @csrf
            {{ method_field('PUT') }}
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="name" class="form-label">الإسم</label>
                    <input type="text" name="name" autofocus placeholder="إسم الطالب" id="name" class="form-control" value="{{ old('name', $student->name) }}">
                    @error('name')
                    <small class="invalid-feedback d-block">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6 col-12 mb-3">
                    <label for="level" class="form-label">الصف</label>
                    <select name="level" id="level" class="form-control">
                        <option disabled selected value="NULL">إختر الصف</option>
                        @foreach ($levels as $level)
                        <option {{ old('level', $student->level_id) == $level->id ? 'selected' : '' }} value="{{ $level->id }}">
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
                            <label for="mobile" class="form-label">رقم الهاتف</label>
                            <input type="number" value="{{ old('mobile', $student->mobile) }}" name="mobile" placeholder="رقم الهاتف" id="mobile" class="form-control">
                            @error('mobile')
                            <small class="invalid-feedback d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-4 mb-3">
                            <label for="mobile2" class="form-label">رقم هاتف آخر</label>
                            <input type="number" value="{{ old('mobile2', $student->mobile2) }}" name="mobile2" placeholder="رقم هاتف آخر" id="mobile2" class="form-control">
                            @error('mobile2')
                            <small class="invalid-feedback d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-4 mb-3">
                            <label for="student_mobile" class="form-label">رقم هاتف الطالب</label>
                            <input type="number" value="{{ old('student_mobile', $student->student_mobile) }}" name="student_mobile" placeholder="رقم هاتف الطالب" id="student_mobile" class="form-control">
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
                        {{ old('gender', $student->gender) === 0 || old('gender', $student->gender) === false ? 'selected' : '' }}
                        value="0">ذكر</option>
                        <option
                        {{ old('gender', $student->gender) === 1 || old('gender', $student->gender) === true ? 'selected' : '' }}
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
                        <option
                        {{ old('edu_type', $student->edu_type) === 0 ? 'selected' : '' }}
                        value="0"
                        {{ old('edu_type') === '0' ? 'selected' : '' }}>تعليم عربي</option>
                        <option
                        {{ old('edu_type', $student->edu_type) === 1 ? 'selected' : '' }}
                        value="1"
                        {{ old('edu_type') === '1' ? 'selected' : '' }}>تعليم لغات</option>
                    </select>
                    @error('edu_type')
                    <small class="invalid-feedback d-block">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6 col-12 mb-3">
                    <label for="given_money">دفع عند الحجز</label>
                    <input type="number" class="form-control" id="given_money" name="given_money" placeholder="المقدم" value="{{ old('given_money', $student->given_money) }}">
                </div>
            </div>
            <div class="row my-3">
                <div class="col-12">
                    <input type="checkbox" name="edit_secondary_data" id="edit_secondary_data" value="1">
                    <label for="edit_secondary_data">تعديل البيانات الفرعية</label>
                </div>
            </div>
            <div id="secondary-data-container" class="d-none-light">
                <div class="d-none-light disvision mb-3" id="division-container">
                    <div class="row">
                        <div class="col-12">
                            <label for="division" class="form-label">الشعبة</label>
                            <select name="division" id="division" class="form-control">
                                <option value="" selected>إختر الشعبة</option>
                                <option
                                {{ old('division', $student->division) === 0 || old('division', $student->division) === false ? 'selected' : '' }}
                                value="0">الشعبة العلمية</option>
                                <option
                                {{ old('division', $student->division) === 1 || old('division', $student->division) === true ? 'selected' : '' }}
                                value="1">الشعبة الأدبية</option>
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
                                {{ old('sub_language', $student->sub_language) === 0 || old('sub_language', $student->sub_language) === false ? 'selected' : '' }}
                                value="0">اللغة الفرنسية</option>
                                <option
                                {{ old('sub_language', $student->sub_language) === 1 || old('sub_language', $student->sub_language) === true ? 'selected' : '' }}
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
                            <select data-division="{{ $subject->division ?? 'NULL'}}" data-level="{{ $subject->level }}" name="{{ $subject->name_en }}" id="{{ $subject->name_en }}_id" class="form-control">
                                <option selected value="NULL">
                                    إختر المعلم
                                </option>
                                @foreach ($subject->teachers as $i => $teacher)
                                <option {{ getTeacher($subject->name_en, $student->id) == $teacher->id ? 'selected' : '' }} data-levels="@foreach(json_decode($teacher->levels) as $level){{ $level }},@endforeach" value="{{ $teacher->id }}" class="t-opt-s">
                                    {{ $teacher->profile->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            <button class="btn btn-success">حفظ</button>
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
<script src="{{ asset('/dist/js/filter_inputs.js') }}"></script>
@endif

<script src="{{ asset('dist/js/edit-student.js') }}"></script>
@endsection