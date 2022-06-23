@extends('admin.layouts.app', ['activePage' => 'lessons.index', 'titlePage' => "قائمة الحصص"])

@section('title')
قائمة الحصص
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('dist/css/bootstrap4_datatables.css') }}">
<link rel="stylesheet" href="{{ asset('dist/css/bootstrap4_datatablesButtons.css') }}">
@endsection

@section('content')
<div class="content">

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">حذف الحصة</h5>
                </div>
                <div class="modal-body">
                    <form method="post" id="delete-form" autocomplete="off">
                        @csrf
                        {{ method_field('delete') }}
                        <div class="mb-2" style="font-size: 15px;">هل أنت متأكد من عملية الحذف؟ </div>
                        <div class="mb-3">
                            <input type="text" id="lesson" class="form-control" readonly>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-danger" onclick="document.getElementById('delete-form').submit();">حذف</button>
                </div>
            </div>
        </div>
    </div>

    @if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            {!! $dataTable->table(['class' => 'table table-hover text-center']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    let deleteModal = document.getElementById('deleteModal'),
        bsModal = new bootstrap.Modal(deleteModal);

    function deleteItem (lesson) {
        deleteModal.querySelector('form#delete-form input#lesson').value = lesson.name;
        deleteModal.querySelector('form#delete-form').action = '/admin/lessons/' + lesson.id;
        bsModal.show();
    }
</script>

<script src="{{ asset('dist/js/jquery_datatables.js') }}"></script>
<script src="{{ asset('dist/js/bootstrap4_datatables.js') }}"></script>
<script src="{{ asset('dist/js/datatables_buttons.js') }}"></script>
<script src="/vendor/datatables/buttons.server-side.js"></script>
{!! $dataTable->scripts() !!}
@endsection