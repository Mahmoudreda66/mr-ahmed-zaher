@extends('admin.layouts.app', ['activePage' => 'lessons.create', 'titlePage' => "إضافة حصة"])

@section('title')
إضافة حصة
@endsection

@section('content')

<div class="allpage-loader" id="allpage-loader">
    <span></span>
</div>

<!-- Groups Modal -->
<div class="modal fade" id="groupsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    إضافة مجموعة 
                </h5>
            </div>
            <div class="modal-body">
                <form method="post" id="add-group-form" autocomplete="off" class="mb-0">
                    <input type="hidden" name="id" id="id">
                    <div class="mb-3">
                        <label for="group_name">إسم المجموعة</label>
                        <input type="text" name="group_name" placeholder="حقل غير إجباري" id="group_name" class="form-control">          
                    </div>
                    <div id="times-container">
                        <div class="mb-3">
                            <div class="row position-relative">
                                <div class="col-6">
                                    <label for="day_1">اليوم</label>
                                    <select name="day_1" id="day_1" class="form-control">
                                        <option value="السبت">السبت</option>
                                        <option value="الأحد">الأحد</option>
                                        <option value="الإثنين">الإثنين</option>
                                        <option value="الثلاثاء">الثلاثاء</option>
                                        <option value="الأربعاء">الأربعاء</option>
                                        <option value="الخميس">الخميس</option>
                                        <option value="الجمعة">الجمعة</option>
                                    </select>
                                </div>
                                <i class="fas fa-times cursor-pointer position-absolute" style="bottom: 0px; right: 49%;"></i>
                                <div class="col-6">
                                    <label for="time_1">الوقت</label>
                                    <input type="time" name="time_1" id="time_1" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <button class="btn btn-sm btn-secondary mb-0" type="button" id="add-time">إضافة موعد</button>
                    </div>
                    <input type="submit" value="" class="d-none">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-success" onclick="submitGroupAdding();">حفظ</button>
            </div>
        </div>
    </div>
</div>

<div class="content">

    @if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    <div class="bg-white p-4 container-fluid">
    	<form action="{{ route('lessons.store') }}" method="post" id="add-lesson" autocomplete="off" class="mb-0">
            @csrf
            <input type="hidden" name="groups_count" id="groups_count" value="0">
            <input type="hidden" name="times" id="times" value=''>
    		<div class="row">
    			<div class="col-md-6 col-12 mb-3">
    				<label for="teacher">المعلم</label>
    				<select name="teacher" id="teacher" class="form-control @error('teacher')
                    is-invalid
                    @enderror">
    					<option value="NULL" selected disabled>إختر المعلم</option>
    					@foreach($teachers as $teacher)
    					<option
                        value="{{ $teacher->id }}">أ/ {{ $teacher->profile->name }} - معلم ال{{ $teacher->subject->name_ar }}</option>
    					@endforeach
    				</select>
                    @error('teacher')
                    <small class="invalid-feedback d-block">{{ $message }}</small>
                    @enderror
    			</div>
    			<div class="col-md-6 col-12 mb-3">
    				<label for="level">الصف</label>
    				<select disabled name="level" id="level" class="form-control @error('level')
                    is-invalid
                    @enderror">
    					<option value="NULL" disabled selected>إختر الصف</option>
    				</select>
                    @error('level')
                    <small class="invalid-feedback d-block">{{ $message }}</small>
                    @enderror
    			</div>
    		</div>
    		<div class="row mb-3">
    			<div class="col-md-6 col-12 mb-3">
    				<label for="subject">المادة</label>
    				<input type="hidden" name="subject" id="subject-id">
    				<input type="text" disabled id="subject" class="form-control @error('subject')
                    is-invalid
                    @enderror">
                    @error('subject')
                    <small class="invalid-feedback d-block">{{ $message }}</small>
                    @enderror
    			</div>
                <div class="col-md-6 col-12 mb-3">
                    <label for="duration">مدة الحصة</label>
                    <input type="number" value="{{ old('duration', '') }}" name="duration" id="duration" class="form-control @error('duration')
                    is-invalid
                    @enderror">
                    @error('duration')
                    <small class="invalid-feedback d-block">{{ $message }}</small>
                    @enderror
                </div>
    		</div>
            <hr class="mb-3">
            <table class="table table-hover text-center d-none" id="groups-table">
                <thead>
                    <th class="text-primary text-center">إسم المجموعة</th>
                    <th class="text-primary text-center">الأيام</th>
                    <th class="text-primary text-center">الأوقات</th>
                    <th class="text-primary text-center">خيارات</th>
                </thead>
                <tbody></tbody>
            </table>
            @error('times')
            <small class="invalid-feedback d-block">{{ $message }}</small>
            @enderror
            <button class="btn-sm btn btn-primary" id="add-group-btn" type="button">
                إضافة مجموعة
            </button>
    		<button class="btn btn-block btn-success cursor-ban" disabled type="submit">إضافة</button>
    	</form>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('/dist/js/create_lessons.js') }}"></script>
@endsection