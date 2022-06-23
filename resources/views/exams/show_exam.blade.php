@extends('exams.layouts.app', ['nav_transparent' => false])

@section('title')
إختبار ال{{ $exam->subject->name_ar }} - {{ $exam->level->name_ar }}
@endsection

@section('css')
<style>
    body {
        background-color: #eee;
        overflow: hidden;
    }
    .sho-bb-0:last-of-type{
        border-bottom: 0px !important;
    }
    tr td{
        border-bottom: 0px !important;
    }
</style>
<link href="{{ asset('/dist/css/kothing-editor.min.css') }}" rel="stylesheet">
<script src="{{ asset('/dist/js/kothing-editor.min.js') }}"></script>
@endsection

@section('content')

<div class="modal fade" tabindex="-1" id="endModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إنهاء الإختبار</h5>
            </div>
            <div class="modal-body">
                <p class="mb-0">هل أنت متأكد من إنهاء الإختبار؟ </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-success" id="end-exam">إنهاء</button>
            </div>
        </div>
    </div>
</div>

<div class="exam-ready-loader" id="exam-ready-loader">
    <h6 class="fw-bold mb-2">ملاحظات هامة: </h6>
    <ul class="mb-4">
        <li class="mb-1">سيتم إحتساب وقت الإختبار عند الضغط على الزر أدناه.</li>
        <li class="mb-1">سيتم غلق الإختبار تلقائياً عند إنتهاء وقت الإختبار وهو {{ $exam->duration }} دقيقة.</li>
        <li class="mb-1">لا يمكن مغادرة هذه الصفحة خلال الإختبار حيث يعتبر الإختبار لاغي.</li>
    </ul>
    <button
    data-exam="{{ $exam->id }}"
    class="btn btn-primary btn-sm" id="exam-start">إضغط هنا لبدء الإختبار</button>
</div>

<div class="allpage-loader" id="allpage-loader">
    <span></span>
</div>

<div id="time-counter" class="time-counter"></div>

<div class="exam-options shadow bg-light">
    <div class="btn-group dropstart">
        <button type="button" class="btn btn-secondary dropdown-toggle"
        data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-cog"></i>
        </button>
        <ul class="dropdown-menu text-end">
            <li class="dropdown-item" id="toggle-status">إخفاء العداد</li>
            <li><hr class="dropdown-divider"></li>
            <li class="dropdown-item" data-bs-toggle="modal" data-bs-target="#endModal">إنهاء الإختبار</li>
        </ul>
    </div>
</div>

<div class="container p-2 mt-5">
    <div class="row">
        <div class="bg-white col-lg-9 col-md-12 col-12 mx-auto">
            <div class="border p-2 my-3" style="font-size: 15px;">
                <div class="container">
                    <div class="row pt-1 pb-2 border-bottom mb-2">
                        <div class="col-7">
                            <div style="font-size: 20px;" class="font-weight-bold pb-1">{{ cache()->get('app_name', 'سمارت سنتر') }}</div>
                        </div>
                    </div>
                    <div class="row pt-2">
                        <div class="col-7">
                            <span>المادة: </span>
                            <span>ال{{ $exam->subject->name_ar }}.</span>
                        </div>
                        <div class="col-5">
                            <span>المُعلم: </span>
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
                            <span id="exam-duration">{{ $exam->duration }}</span>
                            <span>دقيقة.</span>
                        </div>
                    </div>
                    @if($exam->header)
                    <div class="row mt-2">
                        {!! $exam->header !!}
                    </div>
                    @endif
                    <form method="post"
                    autocomplete="off" 
                    action="{{ route('students.exams.submit', $exam->id) }}" id="exam-form">
                        @csrf
                        @foreach($exam->sections as $index => $section)
                        <div class="row mt-3 pt-2 pb-1 border-top dir-{{ $section->dir == 'ltr' ? 'ltr text-left' : 'rtl text-right' }}">
                            <h2 class="d-block exam-title mb-0">
                                <span>{{ $index + 1 }}- </span>
                                <span>{{ $section->title }}</span>
                            </h2>
                            <p class="mb-0 form-text mt-0" style="font-size: 12px;">{{ $section->description }}</p>
                        </div>
                        @foreach($section->questions as $question)
                        @php $questionTitle = $question->body['question']; @endphp
                        <ul style="list-style: bengali;" class="mb-0 dir-{{ $section->dir == 'ltr' ? 'ltr pe-0 ps-3 text-left' : 'rtl text-right pe-3 ps-0' }}">
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
                                            <td>
                                                <input
                                                type="radio"
                                                value="{{ $index }}"
                                                name="question_id={{ $question->id }}"
                                                id="question_id={{ $question->id }}_option_index={{ $index }}">
                                                <label class="form-label"
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
                            <li class="mb-3 border-bottom pb-3 sho-bb-0">
                                <label class="form-label" for="question_id={{ $question->id }}">
                                    {{ $questionTitle }}
                                </label>
                                <textarea name="question_id={{ $question->id }}" id="question_id={{ $question->id }}" rows="5" placeholder="{{ $section->dir == 'rtl' ? 'الإجابة...' : 'Answer...' }}" class="form-control"></textarea>
                                @if($question->body['addEditor'] == 1)
                                <script>
                                $editor = KothingEditor.create(document.getElementById('question_id={{ $question->id }}'), {
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
                                    document.getElementById('question_id={{ $question->id }}').value = this.getContents();
                                }
                            </script>
                                @endif
                            </li>
                            @elseif($question->type == 2) {{-- Short Answer --}}
                            <li class="mb-3 border-bottom pb-3 sho-bb-0">
                                <label class="form-label" for="question_id={{ $question->id }}">
                                    {{ $questionTitle }}
                                </label>
                                <input type="text" name="question_id={{ $question->id }}" placeholder="{{ $section->dir == 'rtl' ? 'الإجابة...' : 'Answer...' }}" id="question_id={{ $question->id }}" class="form-control">
                            </li>
                            @elseif($question->type == 3) {{-- T&F --}}
                            <div class="row border-top pt-3 sho-bb-0">
                                <div class="col-7 mb-3">
                                    {{ $questionTitle }}
                                </div>
                                <div class="col-5">
                                    <div class="d-inline-block">
                                        <input type="radio" value="1"
                                        name="question_id={{ $question->id }}"
                                        id="question_id={{ $question->id }}_1">
                                        <label class="form-label"
                                        for="question_id={{ $question->id }}_1">
                                            <i class="fas fa-check text-success"></i>
                                        </label>
                                    </div>
                                    <span class="px-1"></span>
                                    <div class="d-inline">
                                        <input type="radio" value="0"
                                        name="question_id={{ $question->id }}"
                                        id="question_id={{ $question->id }}_0">
                                        <label class="form-label"
                                        for="question_id={{ $question->id }}_0">
                                            <i class="fas fa-times text-danger"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </ul>
                        @endforeach
                        @endforeach
                    </form>
                    @if($exam->footer)
                    <div class="row pt-2 border-top">
                        {!! $exam->footer !!}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('/dist/js/start_exam.js') }}"></script>
@endsection