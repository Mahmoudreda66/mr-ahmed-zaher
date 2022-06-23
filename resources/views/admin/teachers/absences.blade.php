@extends('admin.layouts.app', ['activePage' => 'teachers.absences', 'titlePage' => "غياب وحضور المعلمين"])

@section('title')
غياب وحضور المعلمين
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('dist/css/bootstrap4_datatables.css') }}">
<link rel="stylesheet" href="{{ asset('dist/css/bootstrap4_datatablesButtons.css') }}">
@endsection

@section('content')

<div class="content">

    @if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    <form method="post" id="delete-record">
        @csrf
        {{ method_field('DELETE') }}
    </form>

    <div class="bg-white p-4 container-fluid">
    	<form action="{{ route('teachers-absences.store') }}" method="post" id="add-absence" class="mb-0">
            <div class="row">
                @csrf
                <div class="mb-3 col-md-6 col-12">
                    <label for="teacher">المعلم</label>
                    <select name="teacher" id="teacher" class="form-control">
                        <option value="NULL" disabled selected>إختر المعلم</option>
                        @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">
                            أ/ {{ $teacher->profile->name }} - معلم ال{{ $teacher->subject->name_ar }}
                        </option>
                        @endforeach
                    </select>
                    <small class="form-text text-danger teacher"></small>
                </div>
                <div class="mb-3 col-md-6 col-12">
                    <label for="lesson">الحصة</label>
                    <select disabled name="lesson" id="lesson" class="form-control">
                        <option value="NULL" disabled selected>إختر الحصة</option>
                    </select>
                    <small class="form-text text-danger lesson"></small>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6 col-12">
                    <label for="group">المجموعة</label>
                    <select name="group" id="group" class="form-control" disabled>
                        <option value="NULL" disabled selected>إختر المجموعة</option>
                    </select>
                </div>
                <div class="col-md-6 col-12 mb-3">
                    <label for="at">الموعد</label>
                    <input type="date" value="<?php echo date('Y-m-d'); ?>" name="at" id="at" class="form-control">
                </div>
            </div>
            <button class="btn btn-sm btn-success" type="submit" name="status" value="1">
                <i class="fas fa-check"></i>
                حاضر
            </button>
            <button class="btn btn-sm btn-danger" type="submit" name="status" value="0">
                <i class="fas fa-ban"></i>
                غائب
            </button>
        </form>
        <hr>
        {!! $dataTable->table(['class' => 'text-center table table-hover']) !!}
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('dist/js/jquery_datatables.js') }}"></script>
<script src="{{ asset('dist/js/bootstrap4_datatables.js') }}"></script>
<script src="{{ asset('dist/js/datatables_buttons.js') }}"></script>
<script src="/vendor/datatables/buttons.server-side.js"></script>
<script src="{{ asset('/dist/js/teachers_absences.js') }}"></script>
{!! $dataTable->scripts() !!}
@endsection