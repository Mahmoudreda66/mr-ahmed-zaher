@extends('admin.layouts.app', ['activePage' => 'exams.index', 'titlePage' => "معاينة الإختبار"])

@section('title')
معاينة الإختبار
@endsection

@section('css')
<link href="{{ asset('/dist/css/kothing-editor.min.css') }}" rel="stylesheet" />
<script src="{{ asset('/dist/js/kothing-editor.min.js') }}"></script>
@endsection

@section('content')
<div class="content">
	<div class="bg-white container-fluid p-2">
		<div class="border p-2" style="font-size: 15px;">
			<div class="container">
				<div class="row pt-1 pb-3 border-bottom mb-2">
					<div class="col-7">
						<div style="font-size: 20px;" class="font-weight-bold">{{ cache()->get('app_name', 'سمارت سنتر') }}</div>
					</div>
				</div>
				<div class="row">
					<div class="col-7">
						<span>المادة: </span>
						<span>ال{{ $exam->subject->name_ar }}.</span>
					</div>
					<div class="col-5">
						<span>المعلم: </span>
						<span>أ/ {{ $exam->teacher->profile->name }}.</span>
					</div>
				</div>
				<div class="row mt-1">
					<div class="col-7">
						<span>الصف: </span>
						<span>{{ $exam->level->name_ar }}.</span>
					</div>
					<div class="col-5">
						<span>المدة: </span>
						<span>{{ $exam->duration }} دقيقة.</span>
					</div>
				</div>
				@if($exam->header)
				<div class="row mt-2">
					{!! $exam->header !!}
				</div>
				@endif
				<div>
					@foreach($exam->sections as $index => $section)
					<div class="mt-2 border-top dir-{{ $section->dir == 'ltr' ? 'ltr text-left' : 'rtl text-right' }}">
						<h2 class="d-block exam-title pb-0 mb-0">
							<span>{{ $index + 1 }}- </span>
							<span>{{ $section->title }}</span>
						</h2>
						<span class="{{ $section->dir == 'ltr' ? 'pr-3' : 'pl-3' }} text-black-50 d-block mt-0 mb-2">
							{{ $section->description }}
						</span>
					</div>
					@foreach($section->questions as $question)
					@php $questionTitle = $question->body['question']; @endphp
					<ul style="list-style: bengali;" class="dir-{{ $section->dir == 'ltr' ? 'ltr pl-0 pr-3 text-left' : 'rtl text-right pl-3 pr-0' }}">
						@if($question->type == 0) {{-- Choose --}}
						@php $options = $question->body['options'] @endphp
						<li class="mb-3">
							<label for="question_id_{{ $question->id }}">
								{{ $questionTitle }}
							</label>
							<div class="options table-responsive">
								<table class="table text-center">
									<tr>
										@foreach ($options as $index => $option)
										<td>
											<input
											type="radio" name="question_id={{ $question->id }}_option"
											id="question_id={{ $question->id }}_option_index={{ $index }}">
											<label
											for="question_id={{ $question->id }}_option_index={{ $index }}">
												{{ $option }}
											</label>
										</td>
										@endforeach
									</tr>
								</table>
							</div>
						</li>
						@elseif($question->type == 1) {{-- Long Answer --}}
						<li class="mb-3">
							<label for="question_id_{{ $question->id }}">
								{{ $questionTitle }}
							</label>
							<textarea
							name="question_id_{{ $question->id }}"
							id="question_id_{{ $question->id }}" rows="5" placeholder="{{ $section->dir == 'rtl' ? 'الإجابة...' : 'Answer...' }}" class="form-control"></textarea>
							@if($question->body['addEditor'] == 1)
							<script>
								$editor = KothingEditor.create(document.getElementById('question_id_{{ $question->id }}'), {
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
									document.getElementById('question_id_{{ $question->id }}').value = this.getContents();
								}
							</script>
							@endif
						</li>
						@elseif($question->type == 2) {{-- Short Answer --}}
						<li class="mb-3">
							<label for="question_id_{{ $question->id }}">
								{{ $questionTitle }}
							</label>
							<input type="text" name="question_id_{{ $question->id }}" placeholder="{{ $section->dir == 'rtl' ? 'الإجابة...' : 'Answer...' }}" id="question_id_{{ $question->id }}" class="form-control">
						</li>
						@elseif($question->type == 3) {{-- T&F --}}
						<div class="row border-top pt-3">
							<div class="col-7">
								{{ $questionTitle }}
							</div>
							<div class="col-5">
                                <div class="d-inline-block">
                                    <input type="radio" value="1"
                                    name="switch_question_id_{{ $question->id }}"
                                    id="switch_question_id_{{ $question->id }}_1">
                                    <label class="form-label" for="switch_question_id_{{ $question->id }}_1">
                                    	<i class="fas fa-check text-success"></i>
                                	</label>
                                </div>
                                <span class="px-2"></span>
                                <div class="d-inline">
                                    <input type="radio" value="0"
                                    name="switch_question_id_{{ $question->id }}"
                                    id="switch_question_id_{{ $question->id }}_0">
                                    <label class="form-label" for="switch_question_id_{{ $question->id }}_0">
                                    	<i class="fas fa-times text-danger"></i>
                                    </label>
                                </div>
                            </div>
						</div>
						@endif
					</ul>
					@endforeach
					@endforeach
				</div>
				@if($exam->footer)
				<div class="row mt-2 pt-2 border-top">
					{!! $exam->footer !!}
				</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection