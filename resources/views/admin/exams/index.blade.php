@extends('admin.layouts.app', ['activePage' => 'exams.index', 'titlePage' => "قائمة الإختبارات"])

@section('title')
قائمة الإختبارات
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
                    <h5 class="modal-title" id="exampleModalLabel">حذف الإختبار</h5>
                </div>
                <div class="modal-body">
                    <form action="{{ url('/admin/exams/destroy') }}" method="post" id="delete-form" autocomplete="off">
                        @csrf
                        {{ method_field('delete') }}
                        <input type="hidden" name="id" id="id">
                        <div class="mb-2" style="font-size: 15px;">هل أنت متأكد من عملية الحذف؟ </div>
                        <div class="mb-3">
                            <input type="text" id="exam" class="form-control" readonly>
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

    <!-- Toggle On Modal -->
    <div class="modal fade" id="toggleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">تغيير حالة الإختبار</h5>
                </div>
                <div class="modal-body">
                    <form action="{{ route('exams.toggle') }}" method="post" id="toggle-form" autocomplete="off">
                        @csrf
                        {{ method_field('PUT') }}
                        <input type="hidden" name="id" id="id">
                        <input type="hidden" name="status" id="status">
                        <div class="mb-3">
                            <input type="text" id="exam" class="form-control" readonly>
                        </div>
                    </form>
                    <div id="on">
                        <ul class="list-style-square">
                            <li>سيتم تفعيل الإختبار فور الموافقة.</li>
                            <li>سيتمكن الطلاب من الدخول للإختبار  فور الموافقة.</li>
                            <li>يجب عليك إنهاء الإختبار فور الإنتهاء من الوقت المحدد.</li>
                        </ul>
                    </div>
                    <div id="off">
                        <ul class="list-style-square">
                            <li>سيتم إيقاف الإختبار فور الموافقة.</li>
                            <li>سيتم رفض جميع الإختبارات المقدمة من الطلاب بعد الإيقاف.</li>
                            <li>سيتم رفض دخول طلاب جديدة للإختبار.</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-success" onclick="document.getElementById('toggle-form').submit();">موافق</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 10 Modal -->
    <div class="modal fade" id="top10Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">العشرة الأوائل</h5>
                </div>
                <div class="modal-body">
                    <div class="allpage-loader position-relative" style="height: 250px;" id="loader">
                        <span></span>
                    </div>
                    <table class="table table-hover mb-0">
                        <thead id="top10_thead">
                            <tr>
                                <th class="text-primary">الإسم</th>
                                <th class="text-primary">الدرجة</th>
                            </tr>
                        </thead>
                        <tbody id="top10_container"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
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
<script src="{{ asset('dist/js/jquery_datatables.js') }}"></script>
<script src="{{ asset('dist/js/bootstrap4_datatables.js') }}"></script>
<script src="{{ asset('dist/js/datatables_buttons.js') }}"></script>
<script src="/vendor/datatables/buttons.server-side.js"></script>
{!! $dataTable->scripts() !!}
<script src="{{ asset('/dist/js/exams.js') }}"></script>
@endsection