@extends('admin.layouts.app', ['activePage' => 'lessons.index', 'titlePage' => "عرض الحصة"])

@section('title')
عرض الحصة
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
				<form method="post" id="delete-group-form" autocomplete="off" class="mb-0">
					@csrf
					{{ method_field('delete') }}
					<input type="hidden" name="id" id="id">
					<div class="mb-0" style="font-size: 15px;">
						هل أنت متأكد من حذف المجموعة؟ سيتم حذف جميع الطلاب المربوطة هذه المجموعة
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

<!-- Delete Lessons Modal -->
<div class="modal fade" id="deleteLessonModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">حذف المجموعة</h5>
			</div>
			<div class="modal-body">
				<form action="{{ route('lessons.destroy', $lesson->id) }}" method="post" id="delete-lesson-form" autocomplete="off" class="mb-0">
					@csrf
					{{ method_field('delete') }}
					<input type="hidden" name="id" id="id">
					<div class="mb-2" style="font-size: 15px;">
						هل أنت متأكد من رغبتك لحذف الحصة؟
					</div>
					<div class="mb-0">
						<input type="text" disabled value="حصة ال{{ $lesson->subject->name_ar }} - أ/ {{ $lesson->teacher->profile->name }}" class="form-control">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
				<button type="button" class="btn btn-danger" onclick="document.getElementById('delete-lesson-form').submit();">حذف</button>
			</div>
		</div>
	</div>
</div>

<!-- Add Group Modal -->
<div class="modal fade" id="addGroupModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">
					إضافة مجموعة
				</h5>
			</div>
			<div class="modal-body">
				<form method="post" action="{{ route('lessons-groups.store') }}" id="add-group-form" autocomplete="off" class="mb-0">
					@csrf
					<input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
					<input type="hidden" name="group_times" id="group_times">
					<div class="mb-3">
						<label for="group_name">إسم المجموعة</label>
						<input type="text" name="group_name" placeholder="حقل غير إجباري" id="group_name" class="form-control @error('group_name')
                        is-invalid
                        @enderror">
						@error('group_name')
						<small class="invalid-feedback d-block">{{ $message }}</small>
						@enderror
						@error('lesson_id')
						<small class="invalid-feedback d-block">{{ $message }}</small>
						@enderror
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
							@error('group_times')
							<small class="invalid-feedback d-block">{{ $message }}</small>
							@enderror
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

	<div class="container-fluid bg-white p-4">
		<h6><strong>معلومات الحصة</strong></h6>
		<table class="table table-hover">
			<tbody>
				<tr>
					<td>المادة: ال{{ $lesson->subject->name_ar }}</td>
				</tr>
				<tr>
					<td>المعلم: {{ $lesson->teacher->profile->name }}</td>
				</tr>
				<tr>
					<td>الصف: {{ $lesson->level->name_ar }}</td>
				</tr>
				<tr>
					<td>عدد المجموعات: {{ $lesson->groups->count() }}</td>
				</tr>
				<tr>
					<td>مدة الحصة: {{ $lesson->duration }} دقيقة</td>
				</tr>
			</tbody>
		</table>
		<div class="row">
			<div class="col-md-6 col-12 mb-3">
				<hr>
				<h6><strong>المجموعات</strong></h6>
				@forelse($lesson->groups as $i => $group)
				<table class="table table-hover mb-0">
					<thead>
						<tr>
							<th class="text-primary">#</th>
							<th class="text-primary">إسم المجموعة</th>
							<th class="text-primary">المواعيد</th>
							<th class="text-primary">عدد الطلاب</th>
							<th class="text-primary">خيارات</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								{{ $group->id }}
							</td>
							<td>
								{{ empty($group->group_name) ? 'لا يوجد' : $group->group_name }}
							</td>
							<td>
								@foreach ($group->times as $day => $time)
								<div>{{ $day }} : {{ $time }}</div>
								@endforeach
							</td>
							<td>
								<span>{{ $group->students->count() }}</span>
							</td>
							<td>
								<small>
									<a href="{{ route('lessons-groups.show', [$group->id, 'add_student']) }}">
										<i class="fas fa-plus text-success pr-1 cursor-pointer" title="إضافة طالب"></i>
									</a>
									<a href="{{ route('lessons-groups.show', [$group->id, 'delete_group']) }}">
										<i data-id="{{ $group->id }}" class="fas fa-trash text-danger delete-group pr-1 cursor-pointer" title="حذف المجموعة"></i>
									</a>
									<a href="{{ route('lessons-groups.show', $group->id) }}">
										<i class="fas fa-eye text-success pr-1 cursor-pointer" title="عرض المجموعة"></i>
									</a>
								</small>
							</td>
						</tr>
					</tbody>
				</table>
				@if ($i < count($lesson->groups) - 1)
					<hr>
					<hr>
					@endif
					@empty
					<div class="alert alert-info text-center mb-0">
						لا يوجد مجموعات حتى الآن
					</div>
					@endforelse
			</div>
			<div class="col-md-6 col-12 mb-3">
				<h6><strong>سجل حضور المعلم</strong></h6>
				<table class="table table-hover mb-0">
					<thead>
						<tr>
							<th class="text-primary">المجموعة</th>
							<th class="text-primary">الحالة</th>
							<th class="text-primary">التاريخ</th>
						</tr>
					</thead>
					<tbody>
						@forelse($teacherAbsences as $absence)
						<tr>
							<td>
								{{ empty($absence->group->group_name) ? 'مجموعة بلا إسم' : $absence->group->group_name }}
							</td>
							<td>
								{!! $absence->status == 1 ? '<span class="badge badge-success">حاضر</span>' : '<span class="badge badge-danger">غائب</span>' !!}
							</td>
							<td title="{{ $absence->created_at }}">{{ $absence->join_at }}</td>
						</tr>
						@empty
						<tr>
							<td colspan="6">
								<div class="alert alert-info mb-0 text-center">لا يوجد سجل حضور حتى الآن</div>
							</td>
						</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
	@if (auth()->user()->hasPermission('edit-lesson'))
	<button class="btn btn-primary mt-3" onclick="addGroupModal.show();">
		<i class="fas fa-plus"></i>
		إضافة مجموعة
	</button>
	@endif
	@if (auth()->user()->hasPermission('delete-lesson'))
	<button class="btn btn-danger mt-3" id="delete-lesson" onclick="new bootstrap.Modal(document.getElementById('deleteLessonModal')).show()">
		<i class="fas fa-trash"></i>
		حذف
	</button>
	@endif
</div>
@endsection

@section('js')
<script src="{{ asset('/dist/js/lessons_groups.js') }}"></script>
<script src="{{ asset('/dist/js/add_group.js') }}"></script>
@if(Session::has('show_add_group'))
<script>
	addGroupModal.show();
</script>
@endif
@endsection