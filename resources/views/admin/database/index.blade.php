@extends('admin.layouts.app', ['activePage' => 'database.index', 'titlePage' => "قاعدة البيانات"])

@section('title')
قاعدة البيانات
@endsection

@section('content')
<div class="content">

	@if(Session::has('success'))
	<div class="alert alert-success">
		{{ Session::get('success') }}
	</div>
	@endif

	@if(Session::has('error'))
	<div class="alert alert-danger">
		{{ Session::get('error') }}
	</div>
	@endif

	<!-- Delete Modal -->
	<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="exampleModalLabel">حذف النسخ الإحتياطية</h5>
	            </div>
	            <div class="modal-body">
	                <form action="{{ route('database.delete_all') }}" method="post" id="delete-form" autocomplete="off">
	                    @csrf
	                    {{ method_field('delete') }}
	                    <div class="mb-2" style="font-size: 15px;">
	                    	هل أنت متأكد من رغبتك في حذف جميع النسخ الإحتياطية؟ لن يتم إستعادتها مرة أخرى
	                    </div>
	                </form>
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
	                <button type="button" class="btn btn-danger" onclick="document.getElementById('delete-form').submit();">حذف</button>
	            </div>
	        </div>
	    </div>
	</div>

	<div class="container-fluid bg-white p-4">
		<div class="row">
			<div class="col-12">
				<h6><strong>جداول قاعدة البيانات</strong></h6>
				<form action="{{ route('database.truncate_many') }}" method="post" id="truncation-form" class="mb-0">
					@csrf
					<div class="row">
						<div class="col-md-3 col-sm-3 col-6 mb-3 border-left">
							<div class="mb-1">
								<label>
									<input type="checkbox" name="students" value="students">
									الطلاب
								</label>
							</div>
							<div class="mb-1">
								<label>
									<input type="checkbox" name="absences" value="absences">
									غياب الطلاب
								</label>
							</div>
							<div class="mb-1">
								<label>
									<input type="checkbox" name="expenses" value="expenses">
									مصروفات الطلاب
								</label>
							</div>
							<div class="mb-1">
								<label>
									<input type="checkbox" name="student_teachers" value="student_teachers">
									معلمين الطلاب
								</label>
							</div>
						</div>
						<div class="col-md-3 col-sm-3 col-6 mb-3 border-left">
							<div class="mb-1">
								<label>
									<input type="checkbox" name="teachers" value="teachers">
									المعلمين
								</label>
							</div>
							<div class="mb-1">
								<label>
									<input type="checkbox" name="teachers_absences" value="teachers_absences">
									غياب المعلمين
								</label>
							</div>
							<div class="mb-1">
								<label>
									<input type="checkbox" name="lessons" value="lessons">
									الحصص
								</label>
							</div>
							<div class="mb-1">
								<label>
									<input type="checkbox" name="lessons_groups" value="lessons_groups">
									مجموعات الحصص
								</label>
							</div>
						</div>
						<div class="col-md-3 col-sm-3 col-6 mb-3 border-left">
							<div class="mb-1">
								<label>
									<input type="checkbox" name="lessons_groups_student" value="lessons_groups_student">
									مجموعات الطلاب
								</label>
							</div>
							<div class="mb-1">
								<label>
									<input type="checkbox" name="exams" value="exams">
									الإختبارات
								</label>
							</div>
							<div class="mb-1">
								<label>
									<input type="checkbox" name="exams_results" value="exams_results">
									درجات الإختبارات
								</label>
							</div>
							<div class="mb-1">
								<label>
									<input type="checkbox" name="exams_enter_attemps" value="exams_enter_attemps">
									محاولات دخول الإختبارات
								</label>
							</div>
						</div>
						<div class="col-md-3 col-sm-3 col-6 mb-3">
							<div class="mb-1">
								<label>
									<input type="checkbox" name="out_money" value="out_money">
									الأموال الخارجة
								</label>
							</div>
						</div>
					</div>
					<!-- <hr> -->
					<button id="submit-truncation-btn" disabled class="btn btn-success d-block">تفريغ محتوى الجدول</button>
					<a id="select-all-btn" class="cursor-pointer" style="font-size: 13px;">
						تحديد الكل
					</a>
				</form>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-12">
				<a href="{{ route('database.backup') }}">
					<button class="btn btn-primary">
						أخذ نسخة إحتياطية
					</button>
				</a>
				<small class="form-text">
					مسار الحفظ: {{ storage_path('app\backups') }}
				</small>
				<form action="{{ route('database.upload_backup') }}" method="post" class="mb-0 mt-3" id="backup-form">
					@csrf
					<div class="row">
						@forelse($allBackups as $i => $backup)
						<div class="col-lg-3 col-sm-4 col-6 mb-3">
							<input type="radio" name="backup_file" id="b{{ $i }}" value="{{ $backup }}">
							<label for="b{{ $i }}">
								<span>نسخة إحتياطية بتاريخ: </span>
								<span>{{ date('Y-m-d', explode('.', explode('/', $backup)[1])[0]) }}</span>
							</label>
						</div>
						@empty
						<div class="col-12">
							<div class="alert alert-info text-center">لا يوجد نسخ إحتياطية حتى الآن</div>
						</div>
						@endforelse
					</div>
					<button class="btn btn-sm btn-success" type="submit">إستعادة النسخة</button>
					<button class="btn btn-sm btn-danger" onclick="new bootstrap.Modal(document.getElementById('deleteModal')).show(); return false;">حذف جميع النسخ</button>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js')
<script src="{{ asset('/dist/js/database.js') }}"></script>
@endsection