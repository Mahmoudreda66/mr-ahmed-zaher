@extends('admin.layouts.app', ['activePage' => 'settings.index', 'titlePage' => "الإعدادات"])

@section('title')
الإعدادات
@endsection

@section('css')
<link href="{{ asset('/dist/css/kothing-editor.min.css') }}" rel="stylesheet" />
<script src="{{ asset('/dist/js/kothing-editor.min.js') }}"></script>
@endsection

@section('content')

@php
function getSettings($name)
{
	return \App\Models\Admin\Settings::where('name', $name)->first()['value'];
}

function getLevelName($name)
{
	return \App\Models\Admin\Level::where('name_en', $name)->first()['name_ar'];
}
@endphp

<div class="content">

	@if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    <div class="container-fluid p-4 bg-white">
    	<form action="{{ route('settings.update') }}" method="post" id="expenses-form" class="mb-0" enctype="multipart/form-data">
    		@csrf
    		{{ method_field('PUT') }}
	    	<h5 class="font-weight-bold">مصروفات الصفوف</h5>
	    	<div class="row">
	    		@foreach(json_decode(getSettings('expenses'), true) as $key => $value)
	    		<div class="col-md-4 col-sm-6 col-12 mb-3">
	    			<label for="{{ $key }}">
	    				{{ getLevelName($key) }}
	    			</label>
	    			<input
	    			placeholder="مصروفات {{ getLevelName($key) }}"
	    			type="text" name="{{ $key }}" id="{{ $key }}" value="{{ $value }}" class="form-control @error($key)
	    			is-invalid
	    			@enderror">
	    			@error($key)
	    			<small class="d-block invalid-feedback">{{ $message }}</small>
	    			@enderror
	    		</div>
	    		@endforeach
	    	</div>
	    	<hr>
	    	<h5 class="font-weight-bold">إعدادات عامة</h5>
	    	<div class="row">
	    		<div class="col-md-4 col-sm-6 col-12 mb-3">
	    			<input
	    			type="checkbox" name="students_must_choose_teachers"
	    			id="students_must_choose_teachers" value="1"
	    			{{ getSettings('students_must_choose_teachers') == 1 ? 'checked' : '' }}>
	    			<label for="students_must_choose_teachers">
	    				إختيار المعلمين عند تقديم الطلاب
	    			</label>
	    		</div>
	    		<div class="col-md-4 col-sm-6 col-12 mb-3">
	    			<input
	    			type="checkbox" name="print_after_add_student"
	    			id="print_after_add_student" value="1"
	    			{{ getSettings('print_after_add_student') == 1 ? 'checked' : '' }}>
	    			<label for="print_after_add_student">
	    				طباعة ورقة بالبيانات بعد تقديم الطلاب
	    			</label>
	    		</div>
	    		<div class="col-md-4 col-sm-6 col-12 mb-3">
	    			<input
	    			type="checkbox" name="enable_students_online_application"
	    			id="enable_students_online_application" value="1"
	    			{{ getSettings('enable_students_online_application') == 1 ? 'checked' : '' }}>
	    			<label for="enable_students_online_application">
	    				التقديم الإلكتروني للطلاب
	    			</label>
	    		</div>
	    	</div>
	    	<div class="row">
	    		<div class="col-md-4 col-sm-6 col-12 mb-3">
	    			<input
	    			type="checkbox" name="must_confirm_students_application"
	    			id="must_confirm_students_application" value="1"
	    			{{ getSettings('must_confirm_students_application') == 1 ? 'checked' : '' }}>
	    			<label for="must_confirm_students_application">
	    				تأكيد الحجز أولاً بعد التقديم الإلكتروني
	    			</label>
	    		</div>
	    		<div class="col-md-4 col-sm-6 col-12 mb-3">
	    			<input
	    			type="checkbox" name="show_answers_after_exam_ends"
	    			id="show_answers_after_exam_ends" value="1"
	    			{{ getSettings('show_answers_after_exam_ends') == 1 ? 'checked' : '' }}>
	    			<label for="show_answers_after_exam_ends">
	    				عرض الإجابات بعد إنتهاء الإختبار
	    			</label>
	    		</div>
	    		<div class="col-md-4 col-sm-6 col-12 mb-3">
	    			<input
	    			type="checkbox" name="always_print_invoice_billing"
	    			id="always_print_invoice_billing" value="1"
	    			{{ getSettings('always_print_invoice_billing') == 1 ? 'checked' : '' }}>
	    			<label for="always_print_invoice_billing">
	    				طباعة الفاتورة بعد دفع المصروفات
	    			</label>
	    		</div>
	    	</div>
	    	<hr>
	    	<h5 class="font-weight-bold">بيانات نصية</h5>
	    	<div class="row">
	    		<div class="col-md-4 col-sm-6 col-12 mb-3">
	    			<label for="place_name">إسم المكان</label>
	    			<input type="text" name="place_name"
	    			id="place_name" value="{{ getSettings('place_name') }}"
	    			class="form-control">
	    		</div>
	    		<div class="col-md-4 col-sm-6 col-12 mb-3">
	    			<label for="center_phone1">رقم التواصل بالسنتر</label>
	    			<input type="number" name="center_phone1"
	    			id="center_phone1" value="{{ getSettings('center_phone1') }}"
	    			class="form-control">
	    		</div>
		    	<div class="col-md-4 col-sm-6 col-12 mb-3">
		    		<label for="center_logo">لوجو السنتر</label>
		    		<input type="file" name="center_logo" id="center_logo" class="form-control">
		    		@if(Session::has('image_error'))
		    		<small class="text-danger">{{ Session::get('image_error') }}</small>
		    		@endif
					<img class="img-thumbnail mt-2 d-none" width="100%" id="logo_preview">
		    	</div>
	    	</div>
	    	<div class="row mt-3">
	    		<div class="col-12">
	    			<label for="student_paper_text">النص بداخل ورقة التقديم</label>
	    			<textarea name="student_paper_text" id="student_paper_text" class="form-control" rows="5" placeholder="قم بكتابة نص يظهر في ورقة تقديم الطالب....">{{ getSettings('student_paper_text') }}</textarea>
	    			<script>
						$editor = KothingEditor.create(document.getElementById('student_paper_text'), {
						  width: '100%',
                          height: '150px',
                          toolbarItem: [
                          ['undo', 'redo'],
                          ['font'],
                          ['bold', 'underline', 'italic'],
                          ['outdent', 'indent', 'align', 'list'],
                          ['table'],
                          ['fullScreen'],
                        ],
                        font: [
                            'Cairo', 'Tahoma'
                        ],
						});

						$editor.onKeyUp = function () {
							document.getElementById('student_paper_text').value = this.getContents();
						}
					</script>
	    		</div>
	    	</div>
	    	<input type="submit" class="d-none">
	    	<button class="btn btn-success btn-block mt-3" type="submit">
	    		حفظ التغييرات
	    	</button>
	    </form>
    </div>
</div>
@endsection

@section('js')
<script>
	let uploadLogoElement = document.getElementById('center_logo'),
		imagePreviewElement = document.getElementById('logo_preview');

	uploadLogoElement.onchange = function () {
		let logoLink = URL.createObjectURL(this.files[0]);

		imagePreviewElement.src = logoLink;
		imagePreviewElement.classList.remove('d-none');
	}
</script>
@endsection