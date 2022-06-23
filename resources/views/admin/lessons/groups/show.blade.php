@extends('admin.layouts.app', ['activePage' => 'lessons.index', 'titlePage' => "عرض المجموعة"])

@section('title')
عرض المجموعة
@endsection

@section('content')

<!-- Delete Groups Modal -->
<div class="modal fade" id="deleteGroupModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">حذف المجموعة</h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('lessons-groups.destroy', $group->id) }}" method="post" id="delete-group-form" autocomplete="off" class="mb-0">
                    @csrf
                    {{ method_field('delete') }}
                    <input type="hidden" name="id" id="id">
                    <div class="mb-0" style="font-size: 15px;">
                    	هل أنت متأكد من حذف المجموعة؟ سيتم حذف جميع الطلاب المربوطة هذه  المجموعة
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('delete-group-form').submit();">حذف</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Student To Group Modal -->
<div class="modal fade" id="studentGroupModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">إضافة طالب للمجموعة</h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('link-student') }}" method="post" id="students-search-form" autocomplete="off" class="mb-0">
                	@csrf
                	<input type="hidden" name="group_id" id="group_id" value="{{ $group->id }}">
                    <div class="mb-3">
                    	<label>المجموعة</label>
                    	<input type="text" disabled id="group_display_name" class="form-control" value="{{ empty($group->group_name) ? 'لا يوجد إسم' : $group->group_name }}">
                    	@error('group_id')
                    	<small class="invalid-feedback d-block">{{ $message }}</small>
                    	@enderror
                    </div>
                    <div class="mb-0 position-relative">
                    	<label for="students" class="d-block">الطالب</label>
                    	<select name="students[]" id="students" class="form-control" multiple style="height: 300px;">
                            @forelse($students as $student)
                            <option value="{{ $student->id }}">
                                {{ $student->name }}
                            </option>
                            @empty
                            <option disabled selected>
                                لا يوجد طلاب
                            </option>
                            @endforelse   
                        </select>
						<ul class="search-hints" style="display: none;">
						</ul>
                    </div>
                    <input type="submit" class="d-none">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-success" id="add-student-s-btn" onclick="document.getElementById('students-search-form').submit();">حفظ</button>
            </div>
        </div>
    </div>
</div>

<!-- Groups Modal -->
<div class="modal fade" id="editGroupModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    تعديل المجموعة
                </h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('lessons-groups.update', $group->id) }}" method="post" id="edit-group-form" autocomplete="off" class="mb-0">
                	@csrf
                	{{ method_field('PUT') }}
                    <input type="hidden" name="id" id="id" value="{{ $group->id }}">
                    <input type="hidden" name="group_times" id="group_times">
                    <div class="mb-3">
                        <label for="group_name">إسم المجموعة</label>
                        <input type="text"
                        value="{{ $group->group_name }}" 
                        name="group_name" placeholder="حقل غير إجباري" id="group_name" class="form-control @error('group_name')
                        is-invalid
                        @enderror">
                        @error('group_name')
                        <small class="invalid-feedback d-block">{{ $message }}</small>
                        @enderror
                    </div>
                    <div id="times-container">
                    	@php $i = 0; @endphp
                        @foreach($group->times as $day => $time)
                    	@php $i++; @endphp
                        <div class="mb-3">
                            <div class="row position-relative">
                                <div class="col-6">
                                    <label for="day_{{ $i }}">اليوم</label>
                                    <select name="day_{{ $i }}" id="day_{{ $i }}" class="form-control">
                                        <option
                                        {{ $day == 'السبت' ? 'selected' : '' }}
                                        value="السبت">السبت</option>
                                        <option
                                        {{ $day == 'الأحد' ? 'selected' : '' }}
                                        value="الأحد">الأحد</option>
                                        <option
                                        {{ $day == 'الإثنين' ? 'selected' : '' }}
                                        value="الإثنين">الإثنين</option>
                                        <option
                                        {{ $day == 'الثلاثاء' ? 'selected' : '' }}
                                        value="الثلاثاء">الثلاثاء</option>
                                        <option
                                        {{ $day == 'الأربعاء' ? 'selected' : '' }}
                                        value="الأربعاء">الأربعاء</option>
                                        <option
                                        {{ $day == 'الخميس' ? 'selected' : '' }}
                                        value="الخميس">الخميس</option>
                                        <option
                                        {{ $day == 'الجمعة' ? 'selected' : '' }}
                                        value="الجمعة">الجمعة</option>
                                    </select>
                                </div>
                                <i class="fas fa-times cursor-pointer position-absolute" style="bottom: 0px; right: 49%;"></i>
                                <div class="col-6">
                                    <label for="time_{{ $i }}">الوقت</label>
                                    <input type="time" value="{{ $time }}" name="time_{{ $i }}" id="time_{{ $i }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @error('group_times')
                        <small class="invalid-feedback d-block">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-0">
                        <button class="btn btn-sm btn-secondary mb-0" type="button" id="add-time">إضافة موعد</button>
                    </div>
                    <input type="submit" value="" class="d-none">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-success" onclick="submitGroupForm();">حفظ</button>
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

    <div class="container-fluid p-4 bg-white">
    	<div class="group-info">
    		<div class="row">
    			<div class="col-sm-6 col-12 mb-3 mb-sm-0">
    				<div class="mb-2">
    					<span>إسم المجموعة: </span>
    					<span>{{ empty($group->group_name) ? 'لا يوجد' : $group->group_name }}</span>
    				</div>
    				<div>
    					<span>المرحلة: </span>
    					<span id="levelId" data-level="{{ $group->lesson->level_id }}">{{ $group->lesson->level->name_ar }}</span>
    				</div>
    			</div>
    			<div class="col-sm-6 col-12 mb-3 mb-sm-0">
    				<div class="mb-2">
    					<span>الحصة: </span>
    					<span>حصة ال{{ $group->lesson->subject->name_ar }} - أ/ {{ $group->lesson->teacher->profile->name }}</span>
    				</div>
    				<div>
    					<span>مدة الحصة: </span>
    					<span>{{ $group->lesson->duration }} دقيقة</span>
    				</div>
    			</div>
    		</div>
    		<hr>
    		<div class="font-weight-bold mb-3">جدول المواعيد</div>
    		<div class="row">
    			<div class="col-12 mx-auto">
    				<table class="table table-hover text-center mb-0">
		    			<thead>
		    				<tr>
		    					<th class="text-center text-primary">#</th>
		    					<th class="text-center text-primary">اليوم</th>
		    					<th class="text-center text-primary">الساعة</th>
		    				</tr>
		    			</thead>
		    			<tbody>
		    				@php $i = 0; @endphp
		    				@forelse($group->times as $day => $time)
		    				@php $i++; @endphp
		    				<tr>
		    					<td>{{ $i }}</td>
		    					<td>{{ $day }}</td>
		    					<td>{{ $time }}</td>
		    				</tr>
		    				@empty
		    				<tr>
		    					<td colspan="3">
		    						<div class="alert alert-info mb-0 text-center">
		    							لا يوجد مواعيد حتى الآن
		    						</div>
		    					</td>
		    				</tr>
		    				@endforelse
		    			</tbody>
		    		</table>
    			</div>
    		</div>
    		<hr>
    		<div class="font-weight-bold mb-3">
    			الطلاب الملتحقين بالمجموعة - {{ $group->students->count() }} 
    			{{ $group->students->count() > 2 && $group->students->count() < 11 ? 'طلاب' : 'طالب' }}
    		</div>
    		<div class="row">
    			@forelse($group->students as $index => $student)
    			<div class="col-lg-3 col-sm-4 col-6">
    				<div class="py-2">
    					{{ $index + 1 }}- <a href="{{ route('students.show', $student->id) }}" title="عرض الملف الشخصي للطالب">{{ $student->name }}</a>
    				</div>
    			</div>
    			@empty
    			<div class="col-12">
    				<div class="alert alert-info text-center mb-0">
	    				لا يوجد طلاب حتى الآن
	    			</div>
    			</div>
    			@endforelse
    		</div>
    	</div>
    </div>
    <div class="mt-3">
    	<button class="btn btn-primary" onclick="addStudentsModal.show()">
	    	<i class="fas fa-plus"></i>
	    	إضافة طالب
	    </button>
	    <button class="btn btn-success" onclick="editGroupModal.show();">
			<i class="fas fa-edit"></i>
			تعديل المجموعة
		</button>
	    <button class="btn btn-danger" onclick="new bootstrap.Modal(document.getElementById('deleteGroupModal')).show()">
	    	<i class="fas fa-trash"></i>
	    	حذف المجموعة
	    </button>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('/dist/js/lessons_groups.js') }}"></script>
<script src="{{ asset('/dist/js/add_students_to_group.js') }}"></script>
<script src="{{ asset('/dist/js/show_lessons_group.js') }}"></script>
@if(Session::has('open_add_modal') || isset($_GET['add_student']))
<script>
	addStudentsModal.show();
</script>
@endif

@if(Session::has('show_edit_group'))
<script>
	editGroupModal.show();
</script>
@endif

@if(isset($_GET['delete_group']))
<script>
	deleteGroupModal.show();
</script>
@endif

@if(Session::has('success'))
<script>
	$.notify("{{ Session::get('success') }}", 'success');
</script>
@endif
@endsection