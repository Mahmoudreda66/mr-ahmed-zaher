@extends('admin.layouts.app', ['activePage' => 'exams.manual-marks', 'titlePage' => "إضافة الدرجات يدوياً"])

@section('title')
إضافة الدرجات يدوياً
@endsection

@section('content')

<div class="allpage-loader" id="allpage-loader">
    <span></span>
</div>

<div class="content">

	@if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    @if($errors->any())
    	@foreach($errors->all() as $error)
	    <div class="alert alert-danger">{{ $error }}</div>
    	@endforeach
    @endif

    <div class="modal fade show" id="customSearchModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">النتيجة</h5>
                </div>
                <div class="modal-body">
                    <form action="{{ route('manual-marks.create', ['level' => $_GET['level']]) }}" class="mb-0" method="get" autocomplete="off" id="custom-result-form">
                    	<input type="hidden" id="levelId" data-id="{{ $_GET['level'] ?? '' }}">
                    	<div class="mb-3">
                    		<label for="level">المرحلة</label>
                    		<select name="level" id="level" class="form-control">
                    			<option value="NULL" disabled selected>إختر المرحلة</option>
                    			@foreach($levels as $level)
                    			<option
                    			{{ $_GET['level'] == $level->id ? 'selected' : '' }}
                    			value="{{ $level->id }}">
                    				{{ $level->name_ar }}
                    			</option>
                    			@endforeach
                    		</select>
                    	</div>
                        <div class="mb-3">
                        	<label for="exam">الإختبار</label>
	                        <select name="exam" disabled id="exam" class="form-control">
	                        	<option value="NULL" disabled selected>
	                        		إختر الإختبار
	                        	</option>
	                        </select>
                        </div>
                        <div class="row">
                        	<div class="col-12 mb-0">
                        		<div class="mb-0">
		                        	<label for="group">المجموعة</label>
			                        <select name="group" disabled id="group" class="form-control">
			                        	<option value="NULL" disabled selected>
			                        		إختر المجموعة
			                        	</option>
			                        </select>
		                        </div>
                        	</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                	<a href="{{ route('home') }}">
                		<button type="button" class="btn btn-secondary">إغلاق</button>
                	</a>
                    <button type="button" class="btn btn-primary"
                    onclick="document.getElementById('custom-result-form').submit()">عرض</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid {{ (!empty($_GET['exam']) && !empty($_GET['group'])) ? ('bg-white p-4') : '' }}">
    	@if(isset($exam))
    	<h6 class="font-weight-bold text-center">
    		{{ $levelData->name_ar . ' - ' . 'إختبار ال' . $exam->subject->name_ar . ' أ/ ' . $exam->teacher->profile->name }}
    	</h6>
    	@endif
    	@if(!empty($_GET['exam']) && !empty($_GET['group']))
    	<form action="{{ route('exams-marks.store') }}" method="post" id="submit-marks">
    		@csrf
    		<input type="hidden" name="exam" value="{{ $_GET['exam'] }}">
    		<input type="hidden" name="marksContainer">
    	</form>
    	<input type="number" id="full-mark-number" placeholder="الدرجة الكلية" class="border-0 mb-0" style="width: 100%;">
    	<hr class="mt-0">
    	<div>
    		<div class="row text-center">
    			<div class="col-4">
    				<b>الطالب</b>
    			</div>
    			<div class="col-4">
    				<b>الدرجة</b>
    			</div>
    			<div class="col-4">
    				<b>الدرجة الكلية</b>	
    			</div>
    		</div>
    	</div>
    	<hr>
    	@forelse($students as $student)
    	<div class="student-card text-center">
    		<div class="row">
    			<div class="col-4 position-relative" style="top: 9px;">
    				<b>{{ $student->name }}</b>
    				<input type="hidden" name="id" value="{{ $student->id }}" id="id">
    			</div>
    			<div class="col-4">
    				<input type="number" class="form-control text-center mark-input" placeholder="الدرجة">
    			</div>
    			<div class="col-4">
    				<input type="number" class="form-control text-center full-mark-input" placeholder="الدرجة الكلية">
    			</div>
    		</div>
    	</div>
    	<hr>
        @empty
        <div class="alert alert-info text-center">لا يوجد طلاب بهذه المجموعة</div>
    	@endforelse
    	<button class="btn btn-success" id="saveMarksBtn">حفظ الدرجات</button>
    	@endif
	</div>
</div>
@endsection

@section('js')
<script>
	let customResultModal = document.getElementById('customSearchModal');
</script>

@if(empty($_GET['exam']))
<script>
	new bootstrap.Modal(customResultModal).show();
</script>
@endif

<script src="{{ asset('/dist/js/get_exams_with_levels.js') }}" data-get="/admin/exams/get-paper-exams/"></script>
<script src="{{ asset('/dist/js/manual-marks.js') }}"></script>
@endsection