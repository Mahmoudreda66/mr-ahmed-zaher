@extends('admin.layouts.app', ['activePage' => 'exams.attemps', 'titlePage' => "محاولات دخول الإختبارات"])
@section('title')
محاولات دخول الإختبارات
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

<div class="allpage-loader" id="allpage-loader">
    <span></span>
</div>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">بيانات الدخول للإختبارات - {{ $levelData->name_ar }}</h4>
                        <p class="card-category">
                        	عند الضغط على زر الحذف سيتم رفض الإختبار الجاري للطالب والسماح له بمرة أخرى
                            <br>
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
                                    <th class="text-center">نوع الدخول</th>
                                    <th class="text-center">الوقت</th>
                                    <th class="text-center">خيارات</th>
                                </thead>
                                <tbody>
                                	@forelse($attemps as $attemp)
                                	<tr>
                                		<td>{{ $attemp->id }}</td>
                                        <td>{{ $attemp->student->name }}</td>
                                		<td>{{ $attemp->student->level->name_ar }}</td>
                                		<td>
                                			ال{{ $attemp->exam->subject->name_ar }}
                                			 - أ/ {{ $attemp->exam->teacher->profile->name }}
                                		</td>
                                        <td>
                                            {{ $attemp->enter_type == '0' ? 'إلكتروني' : 'ورقي' }}
                                        </td>
                                		<td title="{{ $attemp->created_at->diffForHumans() }}">
                                			{{ date('H:i / Y-m-d', strtotime($attemp->created_at)) }}
                                		</td>
                                		<td>
                                			@if(auth()->user()->hasPermission('delete-exam-attemp'))
                                            <i
                                            data-id="{{ $attemp->id }}"
                                            title="خذف"
                                            class="cursor-pointer fas fa-trash text-danger delete-attemp"></i>
                                            @else
                                            -
                                            @endif
                                		</td>
                                	</tr>
                                	@empty
                                	<tr>
                                		<td colspan="7">
                                			<div class="alert alert-info text-center mb-0">
                                				لا يوجد محاولات دخول حتى الآن
                                			</div>
                                		</td>
                                	</tr>
                                	@endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="pagination text-center mx-auto">
                        {{ $attemps->links("pagination::level-exam") }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('/dist/js/exams_attemps.js') }}"></script>
<script src="{{ asset('/dist/js/get_exams_with_levels.js') }}" data-get="/admin/exams/get-exams/"></script>
@endsection