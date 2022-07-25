@extends('admin.layouts.app', ['activePage' => 'videos.index', 'titlePage' => "قائمة الفيديوهات"])

@section('title')
قائمة الفيديوهات
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('dist/css/bootstrap4_datatables.css') }}">
<link rel="stylesheet" href="{{ asset('dist/css/bootstrap4_datatablesButtons.css') }}">
@endsection

@section('content')
<div class="content">

    @if(Session::has('success'))
    <div class="alert alert-success">
        {{ Session::get('success') }}
    </div>
    @endif

    <div class="bg-white p-3 rounded shadow">
    	<div class="table-responsive">
    		{!! $dataTable->table(['class' => 'table table-hover text-center']) !!}
    	</div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('dist/js/jquery_datatables.js') }}"></script>
<script src="{{ asset('dist/js/bootstrap4_datatables.js') }}"></script>
<script src="{{ asset('dist/js/datatables_buttons.js') }}"></script>
<script src="/vendor/datatables/buttons.server-side.js"></script>
{!! $dataTable->scripts() !!}
@endsection