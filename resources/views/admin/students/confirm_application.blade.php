@extends('admin.layouts.app', ['activePage' => 'students.confirmation', 'titlePage' => "تأكيد حجز الطلاب"])

@section('title')
تأكيد حجز الطلاب
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('dist/css/bootstrap4_datatables.css') }}">
<link rel="stylesheet" href="{{ asset('dist/css/bootstrap4_datatablesButtons.css') }}">
@endsection

@section('content')
<div class="content">

    @if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    <div class="container-fluid">
        <div class="bg-white p-3 rounded shadow">
            <form action="" id="confirmationForm" class="d-none" method="post">
                @csrf
            </form>

            <form action="{{ url('/admin/students/destroy?redirect_to=students_confirmation') }}" id="deleteForm" class="d-none" method="post">
                @csrf
                {{ method_field('DELETE') }}
                <input type="hidden" name="id">
            </form>

    		<div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-hover text-center']) !!}      
            </div>
    	</div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('dist/js/jquery_datatables.js') }}"></script>
<script src="{{ asset('dist/js/bootstrap4_datatables.js') }}"></script>
<script src="{{ asset('dist/js/datatables_buttons.js') }}"></script>
<script src="/vendor/datatables/buttons.server-side.js"></script>
<script src="{{ asset('dist/js/students_confirmation.js') }}"></script>
{!! $dataTable->scripts() !!}
@endsection