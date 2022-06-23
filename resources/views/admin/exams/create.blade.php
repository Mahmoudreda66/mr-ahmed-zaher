@extends('admin.layouts.app', ['activePage' => 'exams.create', 'titlePage' => "إضافة إختبار"])

@section('title')
إضافة إختبار
@endsection

@section('content')
<div class="content">

    @if (Session::has('success'))
    <div class="alert alert-success">
        {{ Session::get('success') }}
        @if(Session::get('type') == 0)
         قم بإضافة الأسئلة <a href="{{ route('exams.show', Session::get('id')) }}">من هنا</a>
        @endif
    </div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    <div class="bg-white p-4 container-fluid">
        <form action="{{ route('exams.store') }}" method="post" autocomplete="off" id="add-exam" class="mb-0">
            @csrf
            <input type="hidden" name="subject" id="subject_id">
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="teacher">المعلم</label>
                    <select name="teacher" id="teacher" class="form-control @error('teacher') is-invalid @enderror" {{ auth()->user()->hasRole('teacher') ? 'readonly' : '' }}>
                        <option value="NULL" disabled selected>إختر المعلم</option>
                        @foreach($teachers as $teacher)
                        @role(['manager', 'assistant'])
                        <option
                        value="{{ $teacher->id }}">أ/ {{ $teacher->profile->name }} - معلم ال{{ $teacher->subject->name_ar }}</option>
                        @endrole

                        @role('teacher')
                        @if($teacher->profile->id === auth()->user()->id)
                        <option selected value="{{ $teacher->id }}">
                            أ/ {{ $teacher->profile->name }} - معلم ال{{ $teacher->subject->name_ar }}
                        </option>
                        @endif
                        @endrole
                        @endforeach
                    </select>
                    @error('teacher')
                    <small class="d-block invalid-feedback">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6 col-12 mb-3">
                    <label for="subject">المادة</label>
                    <input type="text" disabled id="subject" class="form-control @error('subject') is-invalid @enderror">
                    @error('subject')
                    <small class="d-block invalid-feedback">{{ $message }}</small>
                    @enderror
                    @role('teacher')
                    <script>
                        document.getElementById('subject').value = 'ال{{ auth()->user()->teacher->subject->name_ar }}';
                        document.getElementById('subject_id').value = '{{ auth()->user()->teacher->subject->id }}';
                    </script>
                    @endrole
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="level">المرحلة</label>
                    <select name="level" class="form-control @error('level') is-invalid @enderror" id="level" {{ auth()->user()->hasRole('manager') ? 'disabled' : '' }}>
                        <option value="NULL" disabled selected>إختر المرحلة</option>
                        @role('teacher')
                        @foreach(json_decode(auth()->user()->teacher->levels, true) as $level)
                        <option value="{{ $level }}">
                            {{ \App\Models\Admin\Level::where('id', $level)->select('name_ar')->first()['name_ar'] }}
                        </option>
                        @endforeach
                        @endrole
                    </select>
                    @error('level')
                    <small class="d-block invalid-feedback">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6 col-12 mb-3">
                    <label for="date">التاريخ</label>
                    <input type="datetime-local" name="date" id="date" class="form-control  @error('date') is-invalid @enderror">
                    @error('date')
                    <small class="d-block invalid-feedback">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="duration">المدة</label>
                    <input type="number" name="duration" id="duration" class="form-control @error('duration') is-invalid @enderror">
                    @error('duration')
                    <small class="d-block invalid-feedback">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="header">رأس الإختبار</label>
                    <textarea name="header" id="header" rows="5" class="form-control" placeholder="ميزة إختيارية..."></textarea>
                </div>
                <div class="col-md-6 col-12 mb-3">
                    <label for="footer">تذييل  الإختبار</label>
                    <textarea name="footer" id="footer" rows="5" class="form-control" placeholder="ميزة إختيارية...">مع تمنياتنا بالنجاح والتوفيق.</textarea>
                </div>
            </div>
            <div class="mb-3">
                <div class="row">
                    <div class="col-6">
                        <label>
                            <input type="checkbox" name="exam_type" checked value="0" id="exam_type">
                            إختبار إلكتروني
                        </label>
                        @error('exam_type')
                        <small class="invalid-feedback d-block">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label>
                            <input type="checkbox" name="auto_correct" id="auto_correct">
                            تصحيح تلقائي للإختبار
                        </label>
                        <small id="exam-type-hint" style="display: none;">
                            <small class="badge badge-warning my-0 pt-0">
                                ميزة التصحيح التلقائي تكون للإختبارات الإختيارية وتصحيح الخطأ فقط.
                            </small>
                        </small>
                        @error('auto_correct')
                        <small class="invalid-feedback d-block">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
            <button class="btn btn-block btn-success">إضافة</button>
        </form>
    </div>
</div>
@endsection

@section('js')
@if(!auth()->user()->hasRole('teacher'))
<script src="{{ asset('/dist/js/create_exam.js') }}"></script>
@endif

<script>
document.getElementById('auto_correct').onchange = function () {
    if(this.checked){
        document.getElementById('exam-type-hint').style.display = 'block';
    }else{
        document.getElementById('exam-type-hint').style.display = 'none';
    }
}

document.getElementById('exam_type').onchange = function () {
    if(!this.checked){
        document.getElementById('auto_correct').setAttribute('disabled', '');
        document.getElementById('header').setAttribute('disabled', '');
        document.getElementById('footer').setAttribute('disabled', '');
    }else{
        document.getElementById('auto_correct').removeAttribute('disabled');
        document.getElementById('header').removeAttribute('disabled');
        document.getElementById('footer').removeAttribute('disabled');
    }
}
</script>
@endsection