@extends('admin.layouts.app', ['activePage' => 'students.index', 'titlePage' => 'قائمة الطلاب'])

@section('title')
قائمة الطلاب
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('dist/css/bootstrap4_datatables.css') }}">
<link rel="stylesheet" href="{{ asset('dist/css/bootstrap4_datatablesButtons.css') }}">
@endsection

@section('content')

@if(Session::has('print_invoice') && Session::get('print_invoice'))
<script>
    window.open('{{ route("expenses.print-invoice", Session::get("invoice_id")) }}', 'طباعة بيانات الطالب', 'fullscreen=no,height=450,left=0,resizable=no,status=no,width=400,titlebar=yes,menubar=no');
</script>
@endif

<!-- Expenses Modal -->
<div class="modal fade" id="expensesModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">دفع المصروفات</h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('expenses.store') }}" method="post" id="expenses-form" autocomplete="off">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="mb-3">
                        <label for="student" class="form-label">الطالب</label>
                        <input type="text" name="student" id="student" readonly class="form-control @error('student')
                            is-invalid
                        @enderror" value="{{ old('student', '') }}">
                        @error('student')
                        <small class="d-block invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="expenses" class="form-label">المصروفات</label>
                        <input type="number" name="expenses" id="expenses" class="form-control @error('expenses')
                            is-invalid
                        @enderror" value="{{ old('expenses', '') }}">
                        @error('expenses')
                        <small class="d-block invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="month" class="form-label">الشهر</label>
                        <input type="number" name="month" id="month" class="form-control @error('month')
                            is-invalid
                        @enderror" value="{{ old('month', date('m') * 1) }}">
                        @error('month')
                        <small class="d-block invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>
                    <input type="submit" value="" class="d-none">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('expenses-form').submit();">حفظ</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">حذف الطالب</h5>
            </div>
            <div class="modal-body">
                <form action="{{ url('/admin/students/destroy') }}" method="post" id="delete-form" autocomplete="off">
                    @csrf
                    {{ method_field('delete') }}
                    <input type="hidden" name="id" id="id">
                    <div class="mb-2" style="font-size: 15px;">هل أنت متأكد من عملية الحذف؟ سيتم أرشفة بيانات الطالب لوقت لاحق</div>
                    <div class="mb-3">
                        <input type="text" id="student" class="form-control" readonly>
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

<div class="content">

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
                            {!! $dataTable->table(['class' => 'text-center table table-hover']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')

@if ($errors->any())

<script>
    let oModal = document.getElementById('expensesModal'),
        oExpensesModal = new bootstrap.Modal(oModal);
    oExpensesModal.show();
</script>

@endif
<script src="{{ asset('dist/js/jquery_datatables.js') }}"></script>
<script src="{{ asset('dist/js/bootstrap4_datatables.js') }}"></script>
<script src="{{ asset('dist/js/datatables_buttons.js') }}"></script>
<script src="/vendor/datatables/buttons.server-side.js"></script>
<script src="{{ asset('dist/js/students.js') }}"></script>
{!! $dataTable->scripts() !!}
@endsection