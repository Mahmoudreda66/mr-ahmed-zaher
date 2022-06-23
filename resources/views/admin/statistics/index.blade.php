@extends('admin.layouts.app', ['activePage' => 'statistics.index', 'titlePage' => "الإحصائيات"])

@section('title')
الإحصائيات
@endsection

@section('css')
<style>
    @media print {
        .card {
            margin: 20px !important;
        }
    }
</style>
<link rel="stylesheet" href="{{ asset('/dist/css/jquery_datatables.css') }}">
@endsection

@section('content')
<!-- Month Modal -->
<div class="modal fade" id="monthModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">تقرير سابق</h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('statistics.index') }}" method="get" id="month-form" autocomplete="off" class="mb-0">
                    <div class="row">
                        <div class="col-6">
                            <label for="month" class="form-label">الشهر</label>
                            <input type="number" min="1" max="12" name="month" id="month" autofocus class="form-control" value="{{ $_GET['month'] }}">
                        </div>
                        <div class="col-6">
                            <label for="year" class="form-label">السنة</label>
                            <select name="year" id="year" class="form-control">
                                @for($i = (date('Y') - 10); $i <= date('Y'); $i++)
                                <option {{ isset($_GET['year']) ? ($_GET['year'] == $i ? 'selected' : '') : ($i == date('Y') ? 'selected' : '') }} value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <input type="submit" value="" class="d-none">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('month-form').submit();">عرض</button>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <h3 class="text-center mb-3 font-weight-bold m-0">
        تقارير شهر {{ $_GET['month'] }}
    </h3>
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-4 col-6 mb-3">
                <div class="bg-white p-3 rounded">
                    <div class="text-center font-weight-bold mb-3">الصف الأول الإعدادي</div>
                    <table class="mb-0 table table-hover table-striped">
                        <tr>
                            <td>الدخل</td>
                            <td>{{ $incomes['prep1Incom'] }} جـ</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-4 col-6 mb-3">
                <div class="bg-white p-3 rounded">
                    <div class="text-center font-weight-bold mb-3">الصف الثاني الإعدادي</div>
                    <table class="mb-0 table table-hover table-striped">
                        <tr>
                            <td>الدخل</td>
                            <td>{{ $incomes['prep2Incom'] }} جـ</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-4 col-6 mb-3">
                <div class="bg-white p-3 rounded">
                    <div class="text-center font-weight-bold mb-3">الصف الثالث الإعدادي</div>
                    <table class="mb-0 table table-hover table-striped">
                        <tr>
                            <td>الدخل</td>
                            <td>{{ $incomes['prep3Incom'] }} جـ</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-6 mb-3">
                <div class="bg-white p-3 rounded">
                    <div class="text-center font-weight-bold mb-3">الصف الأول الثانوي</div>
                    <table class="mb-0 table table-hover table-striped">
                        <tr>
                            <td>الدخل</td>
                            <td>{{ $incomes['sec1Incom'] }} جـ</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-4 col-6 mb-3">
                <div class="bg-white p-3 rounded">
                    <div class="text-center font-weight-bold mb-3">الصف الثاني الثانوي</div>
                    <table class="mb-0 table table-hover table-striped">
                        <tr>
                            <td>الدخل</td>
                            <td>{{ $incomes['sec2Incom'] }} جـ</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-4 col-6 mb-3">
                <div class="bg-white p-3 rounded">
                    <div class="text-center font-weight-bold mb-3">الصف الثالث الثانوي</div>
                    <table class="mb-0 table table-hover table-striped">
                        <tr>
                            <td>الدخل</td>
                            <td>{{ $incomes['sec3Incom'] }} جـ</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="mb-3 bg-white rounded">
            <h4 class="text-center mb-3 font-weight-bold pt-3">
                عدد الطلاب
            </h4>
            <div class="row">
                <div class="col-md-4 col-6">
                    <table class="table table-hover mb-0">
                        <tr>
                            <td class="border-top-0">الصف الأول الإعدادي</td>
                            <td class="border-top-0">{{ $counts['prep1Count'] }} طالب</td>
                        </tr>
                        <tr>
                            <td class="border-top-0">الصف الأول الثانوي</td>
                            <td class="border-top-0">{{ $counts['sec1Count'] }} طالب</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4 col-6">
                    <table class="table table-hover mb-0">
                        <tr>
                            <td class="border-top-0">الصف الثاني الإعدادي</td>
                            <td class="border-top-0">{{ $counts['prep2Count'] }} طالب</td>
                        </tr>
                        <tr>
                            <td class="border-top-0">الصف الثاني الثانوي</td>
                            <td class="border-top-0">{{ $counts['sec2Count'] }} طالب</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4 col-6">
                    <table class="table table-hover mb-0">
                        <tr>
                            <td class="border-top-0">الصف الثالث الإعدادي</td>
                            <td class="border-top-0">{{ $counts['prep3Count'] }} طالب</td>
                        </tr>
                        <tr>
                            <td class="border-top-0">الصف الثالث الثانوي</td>
                            <td class="border-top-0">{{ $counts['sec3Count'] }} طالب</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="bg-white rounded p-3 mb-3">
            <div class="row">
                <div class="col-12">
                    <h4 class="text-center mb-3 font-weight-bold">
                        طلاب تخلفت عن  دفع المصروفات
                    </h4>
                    <table class="table hover text-center" id="unPaidStudents">
                        <thead>
                            <tr>
                                <th class="dir-ltr text-center font-weight-normal">#ID</th>
                                <th class="text-center font-weight-normal">الطالب</th>
                                <th class="text-center font-weight-normal">الصف</th>
                                <th class="text-center font-weight-normal">المصروفات المقررة</th>
                                <th class="text-center font-weight-normal">خيارات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unpaid_students as $i => $student)
                            <tr>
                                <td>{{ $student->id }}</td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->level->name_ar }}</td>
                                <td>{{ $expenses[$student->level->name_en] }}</td>
                                <td>
                                    <a href="{{ route('students.show', $student->id) }}">
                                        <i class="fas fa-eye text-success pr-1"></i>
                                    </a>
                                    <a href="{{ route('students.show', [$student->id, 'pay']) }}">
                                        <i class="fas fa-dollar-sign text-info"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="bg-white p-3 rounded mb-3">
            <div class="row">
                <div class="col-12">
                    <h4 class="text-center mb-3 font-weight-bold">
                        سجل المصروفات المدفوعة
                    </h4>
                    <div class="table-responsive">
                        <table class="table hover text-center" id="paidStudents">
                            <thead>
                                <tr>
                                    <th class="text-center dir-ltr font-weight-normal">#ID</th>
                                    <th class="text-center font-weight-normal">الطالب</th>
                                    <th class="text-center font-weight-normal">المرحلة</th>
                                    <th class="text-center font-weight-normal">المبلغ</th>
                                    <th class="text-center font-weight-normal">المستلم</th>
                                    <th class="text-center font-weight-normal">تاريخ العملية</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paid_students as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>
                                        <a href="{{ route('students.show', $item->student->id) }}">
                                            {{ $item->student->name }}
                                        </a>
                                    </td>
                                    <td>{{ $item->student->level->name_ar }}</td>
                                    <td>{{ $item->money }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td title="{{ $item->created_at->diffForHumans() }}">{{ date('Y-m-d', strtotime($item->created_at)) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <button class="btn btn-success" onclick="window.print();">
            <i class="fas fa-print"></i>
            طباعة
        </button>
        <button class="btn btn-primary" onclick="monthModal.show();">
            <i class="far fa-file-alt"></i>
            تقرير شهر معين
        </button>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('/dist/js/jquery_datatables.js') }}"></script>
<script>
    let monthModal = new bootstrap.Modal(document.getElementById('monthModal')),
        dataTableTranslation = {
            processing:     "جاري التحميل...",
            search:         "البحث: ",
            lengthMenu:    "عرض _MENU_ عنصر",
            info:           "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
            infoEmpty:      "لا يوجد بيانات حتى الآن",
            infoFiltered:   "(منتقاة من مجموع _MAX_ مدخل)",
            infoPostFix:    "",
            loadingRecords: "جاري التحميل...",
            zeroRecords:    "لا يوجد بيانات حتى الآن",
            emptyTable:     "لا يوجد بيانات حتى الآن",
            paginate: {
                first:      "الأول",
                previous:   "السابق",
                next:       "التالي",
                last:       "الأخير"
            }
        };

    $(document).ready( function () {
        $('#paidStudents').DataTable({
            language: dataTableTranslation,
        });

        $("#unPaidStudents").DataTable({
            language: dataTableTranslation
        });
    } );
</script>
@endsection