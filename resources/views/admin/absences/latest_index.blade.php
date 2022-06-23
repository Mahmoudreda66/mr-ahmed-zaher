@extends('admin.layouts.app', ['activePage' => 'absences.latest_index', 'titlePage' => "آخر تسجيلات الغياب"])

@section('title')
آخر تسجيلات الغياب
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('dist/css/bootstrap4_datatables.css') }}">
<link rel="stylesheet" href="{{ asset('dist/css/bootstrap4_datatablesButtons.css') }}">
@endsection

@section('content')
<div class="content">

    @if($errors->any())

    @foreach($errors->all() as $error)
    <div class="alert alert-danger">{{ $error }}</div>
    @endforeach

    @endif

    @if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">حذف التسجيل</h5>
                </div>
                <div class="modal-body">
                    <form action="{{ route('absences.destroy') }}" method="post" id="d-record-form" autocomplete="off">
                        @csrf
                        {{ method_field('delete') }}
                        <input type="hidden" name="id" id="id">
                        <div class="mb-2" style="font-size: 15px;">سيتم حذف التسجيلة بالكامل  نهائياً</div>
                        <div class="mb-3">
                            <label for="lesson" class="form-label @error('lesson')
                                is-invalid
                            @enderror">التسجيل</label>
                            <input type="text" name="lesson" id="lesson" readonly class="form-control">
                            @error('lesson')
                            <small class="d-block invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>
                        <input type="submit" value="" class="d-none">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-danger" onclick="document.getElementById('d-record-form').submit();">حذف</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            {!! $dataTable->table(['class' => 'table text-center table-hover']) !!}
                        </div>
                    </div>
                </div>
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
    {!! $dataTable->scripts() !!}
    <script src="{{ asset('/dist/js/absence_latest_index.js') }}"></script>
@endsection