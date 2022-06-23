@extends('admin.layouts.app', ['activePage' => 'out-money.index', 'titlePage' => "الأموال الخارجة"])

@section('title')
الأموال الخارجة
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

    @if(isset($showReport) && $showReport)
    <div class="modal fade show" id="showReportModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 900px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">التقرير</h5>
                </div>
                <div class="modal-body">
                    <table class="table mb-0 table-hover">
                        <thead>
                            <tr>
                                <th class="text-primary">#</th>
                                <th class="text-primary">المال</th>
                                <th class="text-primary">المستخدم</th>
                                <th class="text-primary">السبب</th>
                                <th class="text-primary">التاريخ</th>
                                <th class="text-primary">خيارات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($getReport as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>{{ $report->money }} ج</td>
                                <td>{{ $report->user->name }}</td>
                                <td>{{ nl2br($report->reason) }}</td>
                                <td>{{ $report->at }}</td>
                                <td>
                                    @if (auth()->user()->hasPermission('delete-out-money'))
                                    @php
                                    $reportData = json_encode([
                                        'id' => $report->id,
                                        'name' => $report->reason,
                                        'reason' => $report->reason,
                                        'at' => $report->at,
                                        'money' => $report->money,
                                    ]);
                                    @endphp
                                    <i onclick='deleteItem({{ $reportData }})' class="cursor-pointer delete-item fas fa-trash text-danger" title="حذف"></i>
                                    @endif
                                    @if (auth()->user()->hasPermission('edit-out-money'))
                                    &nbsp;
                                    <i onclick='editItem({{ $reportData }})' class="cursor-pointer edit-item fas fa-edit text-success" title="تعديل"></i>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">
                                    <div class="alert alert-info text-center mb-0">
                                        لا يوجد تقرير حتى الآن
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <hr class="mt-0">
                    <div>
                        إجمالي التقرير
                    </div>
                    <div class="text-primary font-weight-bold mt-2" style="font-size: 23px;">{{ $total }} جنيه</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if (auth()->user()->hasPermission('delete-out-money'))
    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">حذف العنصر</h5>
                </div>
                <div class="modal-body">
                    <form method="post" id="delete-form" autocomplete="off" class="mb-0">
                        @csrf
                        {{ method_field('delete') }}
                        <div class="mb-2" style="font-size: 15px;">هل أنت متأكد من عملية الحذف؟ </div>
                        <div class="mb-3">
                            <input type="text" id="item" class="form-control" readonly>
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
    @endif


    @if (auth()->user()->hasPermission('edit-out-money'))
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">تعديل العنصر</h5>
                </div>
                <div class="modal-body">
                    <form method="post" id="edit-form" autocomplete="off" class="mb-0">
                        {{ method_field('PUT') }}
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-12 mb-3">
                                <label for="money" class="form-label">المبلغ</label>
                                <input type="number" name="money" id="money" autofocus class="form-control @error('money')
                                is-invalid
                                @enderror" value="{{ old('money', '') }}">
                                @error('money')
                                <small class="d-block invalid-feedback">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 col-12 mb-3">
                                <label for="at" class="form-label">التاريخ</label>
                                <input type="date" name="at" id="at" autofocus class="form-control @error('at')
                                is-invalid
                                @enderror" value="{{ old('at', '') }}">
                                @error('at')
                                <small class="d-block invalid-feedback">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3 mb-md-0">
                                <label for="reason" class="form-label">السبب</label>
                                <textarea name="reason" id="reason" rows="5" class="form-control @error('reason')
                                is-invalid
                                @enderror">{{ old('reason', '') }}</textarea>
                                @error('reason')
                                <small class="d-block invalid-feedback">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <input type="submit" value="" class="d-none">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('edit-form').submit();">تعديل</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if (auth()->user()->hasPermission('add-out-money'))
    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">إضافة عنصر</h5>
                </div>
                <div class="modal-body">
                    <form action="{{ route('out-money.store') }}" method="post" id="add-item" class="mb-0">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-12 mb-3">
                                <label for="money">المبلغ</label>
                                <input type="number" name="money" id="money" value="{{ old('money') }}" class="form-control @error('money')
                                is-invalid
                                @enderror">
                                @error('money')
                                <small class="d-block invalid-feedback">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 col-12 mb-3">
                                <label for="at">التاريخ</label>
                                <input type="date" name="at" id="at" class="form-control @error('money')
                                is-invalid
                                @enderror" value="{{ old('at', date('Y-m-d')) }}">
                                @error('at')
                                <small class="d-block invalid-feedback">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3 mb-md-0">
                                <label for="reason">السبب</label>
                                <textarea name="reason" rows="5" id="reason" placeholder="سبب الدفع..." class="form-control @error('money')
                                is-invalid
                                @enderror">{{ old('reason') }}</textarea>
                                @error('reason')
                                <small class="d-block invalid-feedback">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('add-item').submit();">إضافة</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Reports Modal -->
    <div class="modal fade" id="reportsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">عرض تقرير</h5>
                </div>
                <div class="modal-body">
                    <form action="{{ route('out-money.index') }}" method="get" class="mb-0" id="reports-form">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="user">المستخدم</label>
                                <select name="user" id="user" class="form-control">
                                    <option value="*" {{ isset($_GET['user']) && $_GET['user'] === '*' ? 'selected' : '' }}>جميع المستخدمين</option>
                                    @foreach($users as $user)
                                    <option 
                                    {{ isset($_GET['user']) && $_GET['user'] == $user->id ? 'selected' : '' }}
                                    value="{{ $user->id }}">
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-12 mb-md-0 mb-3">
                                <label for="from">من</label>
                                <input type="date" name="from" id="from" class="form-control" value="{{ $_GET['from'] ?? '' }}">
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="to">إلى</label>
                                <input type="date" name="to" id="to" class="form-control" value="{{ $_GET['to'] ?? date('Y-m-d') }}">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('reports-form').submit();">عرض</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card mb-2">
            <div class="card-body">
                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-hover text-center']) !!}
                </div>
            </div>
        </div>
        @if (auth()->user()->hasPermission('add-out-money'))
        <button class="btn btn-success" onclick="addModal.show();">
            <i class="fas fa-plus"></i>
            إضافة عنصر
        </button>
        @endif
        <button class="btn btn-primary" onclick="reportsForm.show();">
            <i class="fas fa-eye"></i>
            عرض تقرير
        </button>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('dist/js/out_money.js') }}"></script>
@if(Session::has('open_edit_modal'))
<script>
    editForm.action = '/admin/out-money/' + '{{ Session::get("open_edit_modal") }}';
    editModal.show();
</script>
@endif

@if(isset($showReport) && $showReport)
<script>
    new bootstrap.Modal(document.getElementById('showReportModal')).show();
</script>
@endif
<script src="{{ asset('dist/js/jquery_datatables.js') }}"></script>
<script src="{{ asset('dist/js/bootstrap4_datatables.js') }}"></script>
<script src="{{ asset('dist/js/datatables_buttons.js') }}"></script>
<script src="/vendor/datatables/buttons.server-side.js"></script>
{!! $dataTable->scripts() !!}
@endsection