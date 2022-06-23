@extends('exams.layouts.app', ['nav_transparent' => false])

@section('title')
درجة الإختبار
@endsection

@section('css')
<style>
    body {
        background-color: #eee;
    }
</style>
@endsection

<?php
function getAnswer ($question, $student_id) {
    $result = App\Models\Exams\ExamsAnswers::where([
        ['student_id', $student_id],
        ['exams_question_id', $question]
    ])->first();

    if($result){
        return $result['body']['answer'];
    }

    return null;
}
?>
@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-12 mx-auto position-relative">
            <div class="bg-white rounded mb-3">
                <div class="p-3 bg-light">
                    <nav>
                        <span class="text-black-50">
                            الإختبارات
                        </span> /
                        <span class="text-black-50">
                            {{ auth('students')->user()->level->name_ar }}
                        </span> /
                        <span>
                            الطالب {{ auth('students')->user()->name }}
                        </span>
                    </nav>
                </div>
                <div class="p-3">
                    <div class="overflow-hidden">
                        <h6 class="fw-bold float-end" style="margin-bottom: 35px;">
                            درجة إختبار ال{{ $exam->subject->name_ar }} - أ/ {{ $exam->teacher->profile->name }}
                        </h6>
                        @if($arabicNumber !== null)
                        <div class="float-start mb-3">
                            <div
                            style="background-color: @if($topCount == 0)
                            #25b922
                            @elseif($topCount == 1)
                            #ef8400
                            @elseif($topCount == 2)
                            #2db1b1
                            @else
                            #2d3db1
                            @endif;"
                            class="badge py-2">
                                <i class="fas fa-star"></i>
                                المركز {{ $arabicNumber }}
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="row result-boxes" style="margin-bottom: 35px;">
                        <div class="col-md-4 col-12 mb-3 mb-md-0">
                            <div class="bg-success rounded p-2">
                                <h5>{{ $examResult->mark['correct_answers'] }}</h5>
                                <small class="d-block text-center">
                                    الإجابات الصحيحة
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4 col-12 mb-3 mb-md-0">
                            <div class="bg-primary full-mark-box rounded p-2">
                                <h5>{{ $examResult->mark['full_mark'] }}</h5>
                                <small class="d-block text-center">الدرجة الكلية</small>
                            </div>
                        </div>
                        <div class="col-md-4 col-12 mb-3 mb-md-0">
                            <div class="bg-danger rounded p-2">
                                <h5>{{ $examResult->mark['wrong_answers'] }}</h5>
                                <small class="d-block text-center">الإجابات الخاطئة</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="exam-global-info mt-3">
                        <ul>
                            <li>
                                <span>تم بدأ الإختبار الساعة:</span>
                                <span>
                                    {{ date('H:i', strtotime($attemp->created_at)) }}
                                </span>
                            </li>
                            <li>
                                <span>تم الإنتهاء من الإختبار الساعة: </span>
                                <span>
                                    {{ date('H:i', strtotime($examResult->created_at)) }}
                                </span>
                            </li>
                        </ul>
                    </div>
                    @if($showAnswers == 0 || !$exam->status)
                        <hr>
                        @foreach($exam->sections as $i => $section)
                        <div class="dir-{{ $section->dir == 'ltr' ? 'ltr text-left' : 'rtl text-right' }}">
                            <div class="fw-bold mb-0">
                                <span>{{ $i + 1 }}- </span>
                                {{ $section->title }}
                            </div>
                            <span class="{{ $section->dir == 'ltr' ? 'pr-3' : 'pl-3' }} text-black-50 d-block mt-0 mb-2">
                                {{ $section->description }}
                            </span>
                            @foreach($section->questions as $question)
                                @php $questionTitle = $question->body['question']; @endphp
                                @php $questionAnswer = getAnswer($question->id, $attemp->student_id); @endphp
                                <ul style="list-style: bengali;" class="ps-0 pe-3 dir-{{ $section->dir == 'ltr' ? 'ltr pe-0 ps-3 text-left' : 'rtl text-right pe-3 ps-0' }}">
                                    @if($question->type == 0) {{-- Choose --}}
                                        @php $options = $question->body['options'] @endphp
                                        <li class="mb-3 sho-bb-0">
                                            <label class="form-label" for="question_id={{ $question->id }}">
                                                {{ $questionTitle }}
                                            </label>
                                            <div class="question-options">
                                                <table class="table text-center">
                                                    <tr class="option border-bottom">
                                                        @foreach ($options as $index => $option)
                                                            @php
                                                                $question->answer == $questionAnswer && $questionAnswer !== null && $question->answer == $index ? ($answerStatus = true) : ($answerStatus = false);
                                                            @endphp
                                                            <td>
                                                                <input
                                                                {{ $index == $questionAnswer && $questionAnswer !== null ? 'checked' : '' }}
                                                                type="radio" disabled>
                                                                <label
                                                                class="form-label
                                                                @if ($question->answer != $questionAnswer)
                                                                    @if ($questionAnswer == $index && $questionAnswer !== null)
                                                                        badge bg-danger
                                                                    @endif
                                                                    @if ($index == $question->answer)
                                                                        badge bg-success
                                                                    @endif
                                                                @endif">
                                                                    {{ $option }}
                                                                </label>
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                </table>
                                            </div>
                                        </li>
                                    @elseif($question->type == 3)
                                    <div class="row border-top pt-3 sho-bb-0">
                                        <div class="col-9">
                                            <span>
                                                {{ $questionTitle }}
                                            </span>
                                        </div>
                                        <div class="col-3">
                                            <div class="d-inline-block ms-2">
                                                <label class="form-label">
                                                    <!-- correct-t_f -->
                                                    <i class="fas fa-check text-success {{ $questionAnswer == 1 ? 'correct-t_f' : '' }}"></i>
                                                </label>
                                            </div>
                                            <div class="d-inline ms-1">
                                                <label class="form-label">
                                                    <i class="fas fa-times text-danger {{ $questionAnswer == 0 ? 'correct-t_f' : '' }}"></i>
                                                </label>
                                            </div>
                                            @if ($questionAnswer !== null)
                                                @if ($questionAnswer != $question->answer)
                                                    <small class="badge bg-danger">خطأ</small>
                                                @endif
                                            @else
                                                <small class="badge bg-danger">خطأ</small>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </ul>
                            @endforeach
                        </div>
                        @endforeach
                    @else
                        <div class="alert alert-info mb-0 text-center">
                            سيتم عرض التصويب عقب إنتهاء الإختبار
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection