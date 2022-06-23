@extends('exams.layouts.app', ['nav_transparent' => false])

@section('title')
الإختبارات السابقة
@endsection

@section('css')
<style>
    body {
        background-color: #eee;
    }
</style>
@endsection

@section('content')
	<div class="row mt-5">
		<div class="col-lg-9 col-md-10 col-12 mx-auto">
			@if(Session::has('error'))
            <div class="alert alert-danger">
                {{ Session::get('error') }}
            </div>
            @endif
			<div class="bg-white rounded">
				<div class="mb-3 bg-light p-3">
					<h6 class="fw-bold mb-0">
						الإختبارات  التي تمت تأديتها
					</h6>
					<small><small class="text-black-50">
						يمكن عرض الإختبارات ذات التصحيح التلقائي فقط
					</small></small>
				</div>
				<div class="p-2 pt-0 table-responsive">
					<table class="table table-hover text-center mb-0">
						<thead>
							<tr>
								<td>#</td>
								<td>المعلم</td>
								<td>المادة</td>
								<td>التاريخ</td>
								<td>نوع الإختبار</td>
								<td>الدرجة</td>
								<td>عرض</td>
							</tr>
						</thead>
						<tbody>
							@forelse($exams as $i => $exam)
							<tr>
								<td>
									{{ count($exams) - $i }}
								</td>
								<td>
									أ/ {{ $exam->exam->teacher->profile->name }}
								</td>
								<td>
									ال{{ $exam->exam->subject->name_ar }}
								</td>
								<td title="{{ $exam->created_at->diffForHumans() }}">
									{{ date('H:i / Y-m-d', strtotime($exam->created_at)) }}
								</td>
								<td>
									{{ $exam->enter_type == 0 ? 'إلكتروني' : 'ورقي' }}
								</td>
								<td>
									@if($exam->result)
									{{ $exam->result->mark['full_mark']  . '/' . $exam->result->mark['correct_answers'] }}
									@else
									-
									@endif
								</td>
								<td>
									@if($exam->exam->type == 0 && $exam->enter_type == 0)
									<a href="{{ route('students.exams.results', $exam->exam->id) }}">
										<i class="fas fa-eye text-success"></i>
									</a>
									@else
									<i class="fas fa-eye cursor-ban text-black-50"></i>
									@endif
								</td>
							</tr>
							@empty
							<tr>
								<td colspan="7">
									<div class="alert alert-info mb-0 text-center">
										لا يوجد إختبارات حتى الآن
									</div>
								</td>
							</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@endsection