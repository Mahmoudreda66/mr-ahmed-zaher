@extends('admin.layouts.app', ['activePage' => 'exams.index', 'titlePage' => "تجهيز أسئلة الإختبار"])

@section('title')
تجهيز أسئلة الإختبار
@endsection

@section('content')

<div class="allpage-loader" id="allpage-loader">
    <span></span>
</div>

<!-- Edit Exam Modal -->
<div class="modal fade" id="editExamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل الإختبار</h5>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('exams.update', $exam->id) }}" id="edit-exam-form" autocomplete="off" class="mb-0">
                    @csrf
                    {{ method_field('PUT') }}
                    <div class="mb-3">
                        <label for="title" class="form-label @error('title')
                            is-invalid
                        @enderror">المدة</label>
                        <input type="text" name="duration" id="duration" class="form-control" value="{{ old('duration', $exam->duration) }}">
                        @error('duration')
                        <small class="d-block invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-0">
                        <input type="checkbox" name="auto_correct" id="auto_correct"
                        @if(old('auto_correct'))
                        checked
                        @else
                        {{ $exam->type == 0 ? 'checked' : '' }}
                        @endif
                        class="@error('')
                            is-invalid
                        @enderror">
                        <label for="auto_correct" class="form-label">تصحيح تلقائي</label>
                        @error('auto_correct')
                        <small class="d-block invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>
                    <input type="submit" value="" class="d-none">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeModal" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary"
                onclick="document.getElementById('edit-exam-form').submit();">حفظ</button>
            </div>
        </div>
    </div>
</div>

@if($exam->exam_type == 0)
<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة قسم</h5>
            </div>
            <div class="modal-body">
                <form method="post" id="add-section-form" autocomplete="off" class="mb-0">
                    @csrf
                    <input type="hidden" name="exam" id="exam" value="{{ $exam->id }}">
                    <div class="mb-3">
                        <label for="title" class="form-label @error('title')
                            is-invalid
                        @enderror">عنوان القسم</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title', '') }}">
                        @error('title')
                        <small class="d-block invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea name="description" id="description" class="form-control @error('')
                            is-invalid
                        @enderror" value="{{ old('description', 'description') }}"></textarea>
                        @error('description')
                        <small class="d-block invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-0">
                        <div class="row">
                            <div class="col-6">
                                <input checked type="radio" name="dir" id="rtl_dir" value="rtl">
                                <label for="rtl_dir">من اليمين لليسار</label>
                            </div>
                            <div class="col-6">
                                <input
                                {{ $exam->subject->name_en == 'english' || $exam->subject->name_en == 'french' || $exam->subject->name_en == 'germany' ? 'checked' : '' }}
                                type="radio" name="dir" id="ltr_dir" value="ltr">
                                <label for="ltr_dir">من اليسار لليمين</label>
                            </div>
                        </div>
                    </div>
                    <input type="submit" value="" class="d-none">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeModal" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" id="submitform-btn">إضافة</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Section Modal -->
<div class="modal fade" id="editSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل  قسم</h5>
            </div>
            <div class="modal-body">
                <form method="post" id="edit-section-form" autocomplete="off" class="mb-0">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="mb-3">
                        <label for="title" class="form-label @error('title')
                            is-invalid
                        @enderror">عنوان القسم</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title', '') }}">
                        @error('title')
                        <small class="d-block invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea name="description" id="description" class="form-control @error('')
                            is-invalid
                        @enderror" value="{{ old('description', 'description') }}"></textarea>
                        @error('description')
                        <small class="d-block invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-0">
                        <div class="row">
                            <div class="col-6">
                                <input checked type="radio" name="dir" id="rtl_dir" value="rtl">
                                <label for="rtl_dir">من اليمين لليسار</label>
                            </div>
                            <div class="col-6">
                                <input
                                {{ $exam->subject->name_en == 'english' || $exam->subject->name_en == 'french' || $exam->subject->name_en == 'germany' ? 'checked' : '' }}
                                type="radio" name="dir" id="ltr_dir" value="ltr">
                                <label for="ltr_dir">من اليسار لليمين</label>
                            </div>
                        </div>
                    </div>
                    <input type="submit" value="" class="d-none">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeModal" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" id="update-section-btn">حفظ</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Section Modal -->
<div class="modal fade" id="deleteSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">حذف القسم</h5>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ url('/admin/exams-sections') }}" id="delete-section-form" autocomplete="off" class="mb-0">
                    @csrf
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="id" id="id">
                    <div class="mb-3">
                        <label for="name">هل أنت متأكد من حذف القسم؟ سيتم حذف القسم بجميع الأسئلة</label>
                        <input type="text" disabled class="form-control" id="name">
                    </div>
                    <input type="submit" value="" class="d-none">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeModal" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('delete-section-form').submit();" id="submitform-btn">حذف</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة سؤال</h5>
            </div>
            <div class="modal-body">
                <form method="post" id="add-question-form" autocomplete="off" class="mb-0">
                    @csrf
                    <input type="hidden" name="exam" id="exam" value="{{ $exam->id }}">
                    <input type="hidden" name="section" id="section">
                    <div id="questions-area">
                        <div id="question-type-area">
                            <label for="question-type">نوع السؤال</label>
                            <select name="question-type" id="question-type" class="form-control">
                                <option value="NULL" disabled selected>إختر النوع</option>
                                @if($exam->type == 0)
                                <option value="0">سؤال إختياري</option>
                                <option value="3">سؤال صواب أم خطأ</option>
                                @else
                                <option value="0">سؤال إختياري</option>
                                <option value="1">سؤال إجابة طويلة</option>
                                <option value="2">سؤال إجابة قصيرة</option>
                                <option value="3">سؤال صواب أم خطأ</option>
                                @endif
                            </select>
                        </div>
                        <div id="question-body-area" class="mt-3"></div>
                    </div>
                    <input type="submit" value="" class="d-none">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeModal" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" id="submit-add-q-form-btn">إضافة</button>
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

    <div class="bg-white p-4 container-fluid position-relative">
        <h3 class="font-weight-bold mb-3 mt-0 pt-0">ورقة إختبار ال{{ $exam->subject->name_ar }} - {{ $exam->level->name_ar }}</h3>
        @if($exam->type == 0)
        <small class="badge badge-warning position-absolute" style="left: 23px; top: 23px;">
            إختبار تصحيح تلقائي
        </small>
        @endif
        <div>
            <div class="row mb-2">
                <div class="col-7">
                    <span>المادة: </span>
                    <span>ال{{ $exam->subject->name_ar }}</span>
                </div>
                <div class="col-5">
                    <span>المعلم: </span>
                    <span>أ/ {{ $exam->teacher->profile->name }}</span>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-7">
                    <span>الصف: </span>
                    <span>{{ $exam->level->name_ar }}</span>
                </div>
                <div class="col-5">
                    <span>المدة: </span>
                    <span>{{ $exam->duration }} دقيقة</span>
                </div>
            </div>
        </div>
        @if($exam->exam_type == 0)
        <div id="content-area" class="border-top pb-3 mt-3">
            @foreach($exam->sections as $index => $section)
            <div class="mb-3 border-bottom pb-2 pt-2 parent-section dir-{{ $section->dir == 'ltr' ? 'ltr text-left' : 'rtl text-right' }}" data-id="{{ $section->id }}">
                <div class="tad font-weight-bold border-bottom pb-2 d-inline-block overflow-hidden">
                    <div class="float-right">
                        <div>{{ $index + 1 }}- {{ $section->title }}</div>
                        <small>{!! nl2br($section->description) !!}</small>
                    </div>
                    <div class="float-right">
                        <i
                        data-id="{{ $section->id }}"
                        data-name="{{ $section->title }}"
                        data-description="{{ $section->description }}"
                        data-direction="{{ $section->dir }}"
                        class="fas fa-edit text-success edit-section"></i>
                        <i
                        data-id="{{ $section->id }}"
                        data-name="{{ $section->title }}"
                        class="fas fa-trash text-danger delete-section"></i>
                    </div>
                </div>
                <div class="mt-2" id="question-area">
                @foreach($section->questions as $question)
                <ul class="list-style-square my-0">
                @if($question->type == 0) {{-- Choose Question --}}
                <li class="exam-question-c question" data-id="{{ $question->id }}">
                    <span>{{ $question->body['question'] }}</span>
                    <div class="d-inline">
                        <i class="fas fa-plus text-info"></i>
                        <i class="fas fa-edit text-success"></i>
                        <i class="fas fa-trash text-danger"></i>
                    </div>
                    <table class="table font-weight-normal">
                        <tbody>
                            <tr>
                                @foreach($question->body['options'] as $i => $option)
                                <td
                                data-index="{{ $i }}"
                                class="{{ $question->answer == $i ? 'text-success font-weight-bold' : '' }}"
                                style="border: 0px;">
                                    <span>{{ $i + 1 . '- ' }}</span>
                                    <span>{{ $option }}</span>
                                    <small class="d-inline">
                                        <i class="fas fa-edit text-success" style="font-size: 10px;"></i>
                                        <i class="fas fa-trash text-danger" style="font-size: 10px;"></i>
                                    </small>
                                </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </li>
                @elseif($question->type == 1) {{-- Long Answer Question --}}
                <li class="exam-question-c question" data-id="{{ $question->id }}">
                    <span>{{ $question->body['question'] }}</span>
                    @if($question->body['addEditor'])
                    <i class="fas fa-keyboard text-info" style="font-size: 10px;"></i>
                    @endif
                    <div class="d-inline">
                        <i class="fas fa-edit text-success"></i>
                        <i class="fas fa-trash text-danger"></i>
                    </div>
                </li>
                @elseif($question->type == 2) {{-- Short Answer Question --}}
                <li class="exam-question-c question" data-id="{{ $question->id }}">
                    <span>{{ $question->body['question'] }}</span>
                    <div class="d-inline">
                        <i class="fas fa-edit text-success"></i>
                        <i class="fas fa-trash text-danger"></i>
                    </div>
                </li>
                @elseif($question->type == 3) {{-- T&F Question --}}
                <li class="exam-question-c question {{ $question->answer ? 'text-success' : 'text-danger' }}" data-id="{{ $question->id }}">
                    <span>{{ $question->body['question'] }}</span>
                    <div class="d-inline">
                        <i class="fas fa-edit text-success"></i>
                        <i class="fas fa-trash text-danger"></i>
                    </div>
                </li>
                @endif
                </ul>
                @endforeach
                </div>
                <div
                data-section="{{ $section->id }}"
                class="as-a mt-3 add-question-btn" style="padding-right: 15px;" id="add-question-btn">
                    <i class="fas fa-plus"></i>
                    إضافة سؤال
                </div>
            </div>
            @endforeach
        </div>
        <div>
            <div id="add-section-btn" class="as-a">
                <i class="fas fa-plus"></i>
                إضافة قسم
            </div>
        </div>
        @endif
    </div>
    <div class="mt-3">
        @if($exam->exam_type == 0)
        <a href="{{ route('exam.view', $exam->id) }}" target="_blank">
            <button class="btn btn-primary">
                <i class="fas fa-eye"></i>
                معاينة الإختبار
            </button>
        </a>
        @endif
        @if(auth()->user()->hasPermission('edit-exam'))
        <button class="btn btn-success" id="edit-exam-btn">
            <i class="fas fa-edit"></i>
            تعديل الإختبار
        </button>
        @endif
    </div>
</div>
@endsection

@section('js')

@if($errors->any())
<script>new bootstrap.Modal(document.getElementById('editExamModal')).show();</script>
@endif

<script>
    document.getElementById('edit-exam-btn').onclick = function () {
        new bootstrap.Modal(document.getElementById('editExamModal')).show();
    }
</script>
@if($exam->exam_type == 0)
<script>
    // variables
    let sectionModal = new bootstrap.Modal(document.getElementById('addSectionModal')),
        sectionForm = document.getElementById('add-section-form');

    if('{{ $exam->sections->count() }}' == 0){ // the exam is empty from sections
        sectionModal.show();
    }
</script>
<script src="{{ asset('/dist/js/prepare_exam.js') }}"></script>
@endif
@endsection