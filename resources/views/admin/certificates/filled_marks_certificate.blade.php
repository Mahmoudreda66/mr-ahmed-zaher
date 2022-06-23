@extends('admin.layouts.app', ['activePage' => 'filled-marks-certificate', 'titlePage' => "شهادة درجات مملوئة"])

@section('title')
شهادة درجات مملوئة
@endsection

@section('content')
<div class="content">
	@if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif
    <div class="bg-white p-3">
    	<form action="{{ route('filled_marks_certificate_stamp') }}" method="get" class="mb-0">
    		<h4 class="font-weight-bold mb-3">إختر الإختبارات</h4>
    		<input type="hidden" name="level" value="{{ $_GET['level'] }}">
    		<div class="row">
    			@forelse($exams as $exam)
	    		<label data-examcheck class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3 d-block cursor-pointer">
	    			<div class="p-3 bg-light rounded" data-style-el>
	    				<div class="text-center mb-3">
	    					<span>إختبار ال{{ $exam->subject->name_ar }}</span>
	    				</div>
	    				<ul>
	    					<li class="mb-2">أ/ {{ $exam->teacher->profile->name }}</li>
	    					<li class="mb-2">{{ $exam->level->name_ar }}</li>
	    					<li class="mb-2">{{ $exam->date }}</li>
	    				</ul>
	    			</div>
    				<input type="checkbox" name="exams[]" value="{{ $exam->id }}">
	    		</label>
	    		@empty
	    		<div class="col-12">
	    			<div class="alert alert-info text-center  mb-0">
		    			لا يوجد إختبارات  إلكترونية حتى الآن
		    		</div>
	    		</div>
	    		@endforelse
    		</div>
    		<button class="btn btn-success" type="submit">
    			<i class="fas fa-arrow-left"></i>
    			تجهيز الشهادات
    		</button>
    	</form>
    </div>
</div>
@endsection

@section('js')
@if($errors->any())
	@foreach($errors->all() as $error)
	<script>
		$.notify('{{ $error }}', 'error');
	</script>
	@endforeach
@endif
<script src="{{ asset('dist/js/filled_marks_certificate.js') }}"></script>
@endsection