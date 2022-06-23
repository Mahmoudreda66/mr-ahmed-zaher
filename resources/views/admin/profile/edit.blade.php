@extends('admin.layouts.app', ['activePage' => 'editProfile', 'titlePage' => "تعديل الحساب"])

@section('title')
تعديل الحساب
@endsection

@section('content')
<div class="content">

    @if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    <div class="container-fluid p-4 bg-white">
        <form action="{{ route('profile.update') }}" autocomplete="off" method="post" id="edit-form" class="mb-0" enctype="multipart/form-data">
            @csrf
            {{ method_field('PUT') }}
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="name">الإسم</label>
                    <input type="text" name="name"
                    value="{{ old('name', auth()->user()->name) }}" id="name" class="form-control @error('name')
                    is-invalid
                    @enderror">
                    @error('name')
                    <small class="d-block invalid-feedback">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6 col-12 mb-3">
                    <label for="phone">رقم الهاتف</label>
                    <input type="number" name="phone"
                    value="{{ old('phone', auth()->user()->phone) }}" id="phone" class="form-control @error('phone')
                    is-invalid
                    @enderror">
                    @error('phone')
                    <small class="d-block invalid-feedback">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <button class="btn btn-success mt-3 btn-sm">حفظ</button>
        </form>
        <hr>
        <form action="{{ route('profile.password') }}" method="post" id="password-form" class="mb-0" autocomplete="off">
            @csrf
            {{ method_field('PUT') }}
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="old_password">كلمة السر القديمة</label>
                    <input type="password" name="old_password"
                    placeholder="كلمة السر الحالية"
                    id="old_password" class="form-control @error('old_password')
                    is-invalid
                    @enderror">
                    @error('old_password')
                    <small class="d-block invalid-feedback">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6 col-12 mb-3">
                    <label for="new_password">كلمة السر الجديدة</label>
                    <input type="password" name="new_password"
                    placeholder="كلمة السر الحالية"
                    id="new_password" class="form-control @error('new_password')
                    is-invalid
                    @enderror">
                    @error('new_password')
                    <small class="d-block invalid-feedback">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <button class="btn btn-success mt-3 btn-sm">حفظ</button>
        </form>
    </div>
</div>
@endsection