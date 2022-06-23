@extends('admin.layouts.app', ['activePage' => 'teachers.create', 'titlePage' => "إضافة معلم"])

@section('title')
إضافة معلم
@endsection

@section('content')
<div class="content">

    @if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    <div class="bg-white p-4 rounded container-fluid">
        <form
        action="{{ route('teachers.store') }}"
        autocomplete="off"
        method="post"
        id="create-form"
        enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="name">الإسم</label>
                    <input type="text" name="name" id="name" class="form-control @error('name')
                        is-invalid
                    @enderror" value="{{ old('name', '') }}">
                    @error('name')
                    <small class="d-block invalid-feedback">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6 col-12 mb-3">
                    <label for="subject">المادة</label>
                    <select name="subject" id="subject" class="form-control @error('subject')
                        is-invalid
                    @enderror">
                        <option value="NULL" disabled selected>إختر المادة</option>
                        @foreach ($subjects as $subject)
                        <option @if (old('subject')==$subject->id)
                            selected
                            @endif
                            value="{{ $subject->id }}">ال{{ $subject->name_ar }}</option>
                        @endforeach
                    </select>
                    @error('subject')
                    <small class="d-block invalid-feedback">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="mobile">رقم الهاتف</label>
                    <input type="number" name="mobile" id="mobile" class="form-control @error('mobile')
                        is-invalid
                    @enderror" value="{{ old('mobile', '') }}">
                    @error('mobile')
                    <small class="d-block invalid-feedback">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6 col-12 mb-3">
                    <label for="levels">المرحلة</label>
                    <select name="levels[]" id="levels" class="form-control @error('levels')
                        is-invalid
                    @enderror" multiple style="height: auto;">
                        @foreach ($levels as $level)
                        <option value="{{ $level->id }}">{{ $level->name_ar }}</option>
                        @endforeach
                    </select>
                    @error('levels')
                    <small class="d-block invalid-feedback">{{ $message }}</small>
                    @enderror
                    <small class="form-text">إضغط على زر ctrl لإختيار أكثر من مرحلة</small>
                </div>
            </div>
            <button class="btn btn-success btn-block mb-0">إضافة</button>
        </form>
    </div>
    <div class="mt-3 bg-white p-4">
        <h6><strong>ملاحظة هامة</strong></h6>
        <ul class="list-style-square mb-0">
            <li>يجب كتابة إسم المعلم بدون ألقاب.</li>
            <li>كلمة السر الخاصة بالمعلم تكون رقم هاتفه بشكل تلقائي لحين تغييرها.</li>
        </ul>
    </div>
</div>
@endsection