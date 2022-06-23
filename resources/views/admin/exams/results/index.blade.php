@extends('admin.layouts.app', ['activePage' => 'exams.marks', 'titlePage' => "درجات الإختبارات"])
@section('title')
درجات الإختبارات
@endsection

@section('content')

<div class="modal fade" id="customResultModal" tabindex="-1" aria-labelledby="examModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="examModalLabel">
                    نتائج إختبار معين
                </h5>
            </div>
            <div class="modal-body">
                <form action="" method="get" id="custom-result-form" autocomplete="off">
                    <div class="mb-3">
                        <label for="level">المرحلة</label>
                        <select name="level" id="level" class="form-control">
                            <option value="NULL" disabled selected>إختر المرحلة</option>
                            @foreach($levels as $level)
                            <option
                            {{ isset($_GET['level']) ? ($_GET['level'] == $level->id ? 'selected' : '') : '' }}
                            value="{{ $level->id }}">
                                {{ $level->name_ar }}
                            </option>
                            @endforeach
                        </select>
                        <small class="form-text text-danger level"></small>
                    </div>
                    <div class="mb-3">
                        <label for="exam">الإختبار</label>
                        <select name="exam" id="exam" class="form-control" {{ !isset($_GET['exam']) ? 'disabled' : '' }}>
                            <option value="NULL" disabled selected>إختر الإختبار</option>
                        </select>
                        <small class="form-text text-danger exam"></small>
                    </div>
                    <input type="submit" value="" class="d-none">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" onclick="validateSearchForm()">عرض</button>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->hasPermission('edit-exam-mark'))
<div class="modal fade" id="editMarkModal" tabindex="-1" aria-labelledby="examModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="examModalLabel">
                    تعديل الدرجة
                </h5>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="edit-mark-form" autocomplete="off">
                    @csrf
                    {{ method_field('PUT') }}
                    <div class="mb-3">
                        <label for="full_mark">الدرجة الكلية</label>
                        <input type="number"
                        value="{{ old('full_mark') }}"
                        name="full_mark" id="full_mark" class="form-control @error('full_mark')
                        is-invalid
                        @enderror">
                        @error('full_mark')
                        <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="correct_answers">الدرجة</label>
                        <input type="number"
                        value="{{ old('correct_answers') }}"
                        name="correct_answers" id="correct_answers" class="form-control @error('correct_answers')
                        is-invalid
                        @enderror">
                        @error('correct_answers')
                        <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <input type="submit" value="" class="d-none">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('edit-mark-form').submit()">حفظ</button>
            </div>
        </div>
    </div>
</div>
@endif

@if(auth()->user()->hasPermission('delete-exam-mark'))
<div class="modal fade" id="deleteMarkModal" tabindex="-1" aria-labelledby="examModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="examModalLabel">
                    حذف الدرجة
                </h5>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="delete-mark-form" autocomplete="off">
                    @csrf
                    {{ method_field('DELETE') }}
                    <div class="mb-0">
                        <label for="mark">الدرجة الكلية</label>
                        <input type="text" class="form-control" id="mark" name="mark" disabled>
                    </div>
                    <input type="submit" value="" class="d-none">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('delete-mark-form').submit()">حذف</button>
            </div>
        </div>
    </div>
</div>
@endif

<div class="content">

    @if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-2">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">درجات إختبارات {{ $levelData->name_ar }}</h4>
                        <p class="card-category">
                            <a href="javascript:void(0)" onclick="new bootstrap.Modal(document.getElementById('customResultModal')).show()">
                                هل  تريد نتائج إختبار معين؟ 
                            </a>
                        </p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover text-center">
                                <thead class="text-primary">
                                    <th class="text-center" style="direction: ltr;">#ID</th>
                                    <th class="text-center">الطالب</th>
                                    <th class="text-center">المرحلة</th>
                                    <th class="text-center">الإختبار</th>
                                    <th class="text-center">الوقت</th>
                                    <th class="text-center">الدرجة</th>
                                    <th class="text-center">خيارات</th>
                                </thead>
                                <tbody>
                                	@forelse($results as $result)
                                	<tr>
                                		<td>{{ $result->id }}</td>
                                        <td>{{ $result->student->name }}</td>
                                		<td>{{ $result->student->level->name_ar }}</td>
                                		<td>
                                			ال{{ $result->exam->subject->name_ar }}
                                			 - أ/ {{ $result->exam->teacher->profile->name }}
                                		</td>
                                		<td title="{{ $result->created_at->diffForHumans() }}">
                                			{{ date('H:i / Y-m-d', strtotime($result->created_at)) }}
                                		</td>
                                        <td>
                                            {{ $result->mark['full_mark'] . '/' . $result->mark['correct_answers'] }}
                                        </td>
                                		<td>
                                			@if(auth()->user()->hasPermission('delete-exam-mark'))
                                            <i
                                            data-id="{{ $result->id }}"
                                            data-student="{{ $result->student->name }}"
                                            data-mark="{{ $result->mark['full_mark'] . '/' . $result->mark['correct_answers'] }}"
                                            title="خذف"
                                            class="cursor-pointer fas fa-trash text-danger delete-result"></i>
                                            @endif

                                            @if(auth()->user()->hasPermission('edit-exam-mark'))
                                            &nbsp;
                                            <i
                                            data-id="{{ $result->id }}"
                                            data-fullmark="{{ $result->mark['full_mark'] }}"
                                            data-mark="{{ $result->mark['correct_answers'] }}"
                                            title="تعديل"
                                            class="cursor-pointer fas fa-edit text-success edit-result"></i>
                                            @endif
                                		</td>
                                	</tr>
                                	@empty
                                	<tr>
                                		<td colspan="7">
                                			<div class="alert alert-info text-center mb-0">
                                				لا توجد درجات حتى الآن
                                			</div>
                                		</td>
                                	</tr>
                                	@endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="pagination text-center mx-auto">
                        {{ $results->links("pagination::level-exam") }}
                    </div>
                </div>
            </div>
        </div>
        @if(isset($exam))
        <a href="{{ route('marks.print', $exam->id) }}">
            <button class="btn btn-primary">
                <i class="fas fa-print"></i>
                طباعة
            </button>
        </a>
        @endif
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('/dist/js/get_exams_with_levels.js') }}" data-get="/admin/exams/get-exams/"></script>
<script src="{{ asset('/dist/js/exams_results.js') }}"></script>
@if($errors->any())
<script>
    new bootstrap.Modal(document.getElementById('editMarkModal')).show();
    editForm.action = '/admin/exams-marks/' + '{{ Session::get('id') }}';
</script>
@endif
@endsection