@extends('admin.layouts.app', ['activePage' => 'empty-marks-certificate', 'titlePage' => "شهادة درجات فارغة"])

@section('title')
شهادة درجات فارغة
@endsection

@section('content')
<div class="content">
    <div class="bg-white p-3 mb-2">
    	<iframe src="{{ route('empty_marks_certificate_stamp') }}" frameborder="0" width="100%" height="450px"></iframe>
    </div>
    <a href="{{ route('empty_marks_certificate_stamp', 'print') }}" target="_blank">
    	<button class="btn btn-primary">
	    	<i class="fas fa-print"></i>
	    	طباعة
	    </button>
    </a>
</div>
@endsection