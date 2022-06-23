@extends('admin.layouts.app', ['activePage' => 'exams.correcting', 'titlePage' => "تصحيح الإختبارات الإلكترونية"])

@section('title')
تصحيح الإختبارات الإلكترونية
@endsection

@section('css')
<link href="{{ asset('/dist/css/kothing-editor.min.css') }}" rel="stylesheet">
<script src="{{ asset('/dist/js/kothing-editor.min.js') }}"></script>
@endsection

@php
function getAnswer($question)
{
    $answer = \App\Models\Exams\ExamsAnswers::where([
        ['exams_question_id', $question],
        ['student_id', $_GET['student']]
    ])->select('id', 'body')->first();

    return $answer;
}

function getCorrecting($question)
{
    $correcting = \App\Models\Exams\ExamsCorrecting::where([
        ['exams_question_id', $question],
        ['student_id', $_GET['student']]
    ])->select('id', 'body')->first();

    return $correcting;
}
@endphp

@section('content')

<div class="allpage-loader" id="allpage-loader">
    <span></span>
</div>

<div class="content correcting">
    <div class="modal fade show" id="customSearchModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">النتيجة</h5>
                </div>
                <div class="modal-body">
                    <form action="{{ route('exams-correcting', ['level' => $_GET['level']]) }}" class="mb-0" method="get" autocomplete="off" id="custom-result-form">
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
                    		<small class="text-danger form-text level"></small>
                    	</div>
                        <div class="mb-0">
                        	<label for="exam">الإختبار</label>
	                        <select name="exam" disabled id="exam" class="form-control">
	                        	<option value="NULL" disabled selected>
	                        		إختر الإختبار
	                        	</option>
	                        </select>
	                        <small class="text-danger form-text exam"></small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('home') }}">
                        <button type="button" class="btn btn-secondary">إغلاق</button>
                    </a>
                    <button type="button" class="btn btn-primary"
                    onclick="validateSearchForm();">عرض</button>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($_GET['exam']) && !empty($_GET['student']))
    <div class="container-fluid p-4 bg-white">
        <div class="row border-bottom pb-3 mb-3">
            <div class="col-md-6 col-12 mb-3 mb-md-0">
                <span>الطالب</span>
                <select id="students" class="form-control">
                    <option value="NULL" disabled>
                        يتم عرض الطلاب الذين دخلوا الإختبار فقط
                    </option>
                    @foreach($attemps as $attemp)
                    <option
                    {{ $attemp->student_id == $_GET['student'] ? 'selected' : '' }}
                    value="{{ $attemp->student_id }}">
                        {{ $attemp->student->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 col-12 mb-3 mb-md-0">
                <div class="mb-3">
                    <span>الصف الدراسي: </span>
                    <span>{{ $levelData->name_ar }}</span>
                </div>
                <div id="exam-info" data-id="{{ $_GET['exam'] }}">
                    <span>
                        الإختبار: 
                    </span>
                    <span>
                        ال{{ $exam->subject->name_ar }} - أ/ {{ $exam->teacher->profile->name }}
                    </span>
                </div>
            </div>
        </div>
        @forelse($student->attemps as $attemp)
        @if($attemp->exam_id == $_GET['exam'])
            @foreach($attemp->exam->sections as $index => $section)
                <div class="mt-3 pt-2 pb-1 dir-{{ $section->dir == 'ltr' ? 'ltr text-left' : 'rtl text-right' }}">
                    <h2 class="d-block exam-title">
                        <span>{{ $index + 1 }}- </span>
                        <span>{{ $section->title }}</span>
                    </h2>
                </div>
                @foreach($section->questions as $question)
                @php $questionTitle = $question->body['question']; @endphp
                <ul style="list-style: bengali;" class="mb-0 dir-{{ $section->dir == 'ltr' ? 'ltr pl-0 pr-4 text-left' : 'rtl text-right pl-4 pr-0' }}">
                    @if($question->type == 0) {{-- Choose --}}
                    @php $options = $question->body['options'] @endphp
                    <li class="mb-3 sho-bb-0 dropdown {{ getCorrecting($question->id) !== null ? (getCorrecting($question->id)->body['status'] === 'true' ? 'corrected-question correct' : 'corrected-question wrong') : '' }}">
                        <label class="form-label dropdown-toggle cursor-pointer d-block" for="question_id={{ $question->id }}"  id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $questionTitle }}
                        </label>
                        <ul class="dir-rtl dropdown-menu" aria-labelledby="dropdownMenuButton1" data-id="{{ $question->id }}">
                            <li class="dropdown-item cursor-pointer question-answer-status" data-value="1">صواب</li>
                            <li class="dropdown-item cursor-pointer question-answer-status" data-value="0">خطأ</li>
                        </ul>
                        <div class="question-options">
                            <table class="table text-center">
                                <tr class="option border-bottom">
                                    @foreach ($options as $index => $option)
                                    <td>
                                        <input
                                        disabled {{ getAnswer($question->id) !== null && getAnswer($question->id)->body['answer'] == $index ? 'checked' : '' }}
                                        type="radio"
                                        value="{{ $index }}"
                                        name="question_id={{ $question->id }}"
                                        id="question_id={{ $question->id }}_option_index={{ $index }}">
                                        <label class="form-label {{ $question->answer == $index ? 'r-true-q' : '' }}"
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
                    <li class="mb-3 border-bottom pb-3 sho-bb-0 dropdown {{ getCorrecting($question->id) !== null ? (getCorrecting($question->id)->body['status'] === 'true' ? 'corrected-question correct' : 'corrected-question wrong') : '' }}">
                        <label class="form-label dropdown-toggle cursor-pointer d-block" for="question_id={{ $question->id }}"  id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" for="question_id={{ $question->id }}">
                            {{ $questionTitle }}
                        </label>
                        <ul class="dir-rtl dropdown-menu" aria-labelledby="dropdownMenuButton1" data-id="{{ $question->id }}">
                            <li class="dropdown-item cursor-pointer question-answer-status" data-value="1">صواب</li>
                            <li class="dropdown-item cursor-pointer question-answer-status" data-value="0">خطأ</li>
                        </ul>
                        <div class="bg-light p-2 answer-box border-bottom">
                            {!! getAnswer($question->id)->body['answer'] ?? 'إجابة فارغة' !!}
                        </div>
                    </li>
                    @elseif($question->type == 2) {{-- Short Answer --}}
                    <li class="mb-3 border-bottom pb-2 sho-bb-0 dropdown {{ getCorrecting($question->id) !== null ? (getCorrecting($question->id)->body['status'] === 'true' ? 'corrected-question correct' : 'corrected-question wrong') : '' }}">
                        <label class="form-label dropdown-toggle cursor-pointer d-block" for="question_id={{ $question->id }}"  id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" for="question_id={{ $question->id }}">
                            {{ $questionTitle }}
                        </label>
                        <ul class="dir-rtl dropdown-menu" aria-labelledby="dropdownMenuButton1" data-id="{{ $question->id }}">
                            <li class="dropdown-item cursor-pointer question-answer-status" data-value="1">صواب</li>
                            <li class="dropdown-item cursor-pointer question-answer-status" data-value="0">خطأ</li>
                        </ul>
                        <div class="bg-light p-2 answer-box border-bottom">
                            {!! getAnswer($question->id)->body['answer'] ?? 'إجابة فارغة' !!}
                        </div>
                    </li>
                    @elseif($question->type == 3) {{-- T&F --}}
                    <li>
                        <div class="border-top mb-3 pt-4 sho-bb-0 dropdown row {{ getCorrecting($question->id) !== null ? (getCorrecting($question->id)->body['status'] === 'true' ? 'corrected-question mb-2 correct' : 'corrected-question mb-2 wrong') : '' }}">
                            <div class="col-8 mb-3 dropdown-toggle cursor-pointer" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $questionTitle }}
                            </div>
                            <ul class="dir-rtl dropdown-menu" aria-labelledby="dropdownMenuButton1" data-id="{{ $question->id }}">
                                <li class="dropdown-item cursor-pointer question-answer-status" data-value="1">صواب</li>
                                <li class="dropdown-item cursor-pointer question-answer-status" data-value="0">خطأ</li>
                            </ul>
                            <div class="col-4">
                                <div class="d-inline-block">
                                    <input
                                    disabled {{ getAnswer($question->id) !== null && getAnswer($question->id)->body['answer'] == 1 ? 'checked' : '' }}
                                    type="radio" value="1"
                                    name="question_id={{ $question->id }}"
                                    id="question_id={{ $question->id }}_1">
                                    <label class="form-label {{ $question->answer == 1 ? 'r-true-q' : '' }}"
                                    for="question_id={{ $question->id }}_1">
                                        <i class="fas fa-check text-success"></i>
                                    </label>
                                </div>
                                <span class="px-3"></span>
                                <div class="d-inline">
                                    <input
                                    disabled {{ getAnswer($question->id) !== null && getAnswer($question->id)->body['answer'] == 0 ? 'checked' : '' }}
                                    type="radio" value="0"
                                    name="question_id={{ $question->id }}"
                                    id="question_id={{ $question->id }}_0">
                                    <label class="form-label {{ $question->answer == 0 ? 'r-true-q' : '' }}"
                                    for="question_id={{ $question->id }}_0">
                                        <i class="fas fa-times text-danger"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endif
                </ul>
                @endforeach
            @endforeach
        @endif
        @empty
        <div class="container-fluid bg-white p-4 pb-0">
            <div class="alert alert-danger text-center mb-0">
                لم يدخل الطالب للإختبار
            </div>
        </div>
        @endforelse
        <form id="final-mark-form" class="mt-2 pt-3 border-top container-fluid">
            <div class="row">
                <div class="col-4">
                    <label for="mark">الدرجة</label>
                    <input type="text" name="mark" id="mark" class="form-control">
                </div>
                <div class="col-4">
                    <label for="full_mark">الدرجة الكلية</label>
                    <input type="text" name="full_mark" id="full_mark" class="form-control">
                </div>
                <div class="col-4 position-relative">
                    <button class="btn btn-success position-absolute" style="bottom: 0px;">
                        حفظ
                    </button>
                </div>
            </div>
        </form>
    </div>
    @else
    <div class="container-fluid bg-white p-4 pb-0">
        <div class="alert alert-danger text-center">
            لا يوجد طلاب أتموا الإختبار حتى الآن
        </div>
        <a href="{{ route('exams.index') }}">
            <button class="btn btn-info mx-auto d-block">
                الرجوع
            </button>
        </a>
    </div>
    @endif
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

@if(Session::has('error'))
<script>
    $.notify('{{ Session::get("error") }}', 'error');
</script>
@endif
<script src="{{ asset('/dist/js/get_exams_with_levels.js') }}"></script>
<script src="{{ asset('/dist/js/exams_correcting.js') }}"></script>
@endsection