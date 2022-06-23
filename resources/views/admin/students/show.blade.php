@extends('admin.layouts.app', ['activePage' => 'students.index', 'titlePage' => "عرض الطالب " . $student->name])

@section('title')
عرض الطالب {{ $student->name }}
@endsection

@section('content')

@if(Session::has('print_invoice') && Session::get('print_invoice'))
<script>
    window.open('{{ route("expenses.print-invoice", Session::get("invoice_id")) }}', 'طباعة بيانات الطالب', 'fullscreen=no,height=450,left=0,resizable=no,status=no,width=400,titlebar=yes,menubar=no');
</script>
@endif

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">حذف الطالب</h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('students.destroy', $student->id) }}" method="post" id="delete-form" autocomplete="off">
                    @csrf
                    {{ method_field('delete') }}
                    <input type="hidden" name="id" id="id" value="{{ $student->id }}">
                    <div class="mb-2" style="font-size: 15px;">هل أنت متأكد من عملية الحذف؟ سيتم أرشفة بيانات الطالب لوقت لاحق</div>
                    <div class="mb-3">
                        <input type="text" id="student" value="{{ $student->name }}" class="form-control" readonly>
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
                    <input type="hidden" name="id" id="id" value="{{ $student->id }}">
                    <div class="mb-3">
                        <label for="student" class="form-label @error('student')
                            is-invalid
                        @enderror">الطالب</label>
                        <input type="text" name="student" id="student" readonly class="form-control" value="{{ $student->name }}">
                        @error('student')
                        <small class="d-block invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="expenses" class="form-label">المصروفات</label>
                        <input type="number" name="expenses" id="expenses" class="form-control @error('expenses')
                            is-invalid
                        @enderror" value="{{ old('expenses', json_decode(\App\Models\Admin\Settings::where('name', 'expenses')->select('value')->first()['value'], true)[$student->level->name_en]) }}">
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

<div class="content show-student-p">
    <div class="bg-white p-4 mb-2">
        <div class="row">
            <div class="col-md-8 col-sm-7 col-12 mb-3 mb-md-0 border-left">
                <div>
                    <h6><strong>تفاصيل عامة</strong></h6>
                    <table class="table table-hover mt-3 mb-0">
                        <tr>
                            <td>الإسم: {{ $student->name }}</td>
                        </tr>
                        <tr>
                            <td>المرحلة: {{ $student->level->name_ar }}</td>
                        </tr>
                        <tr>
                            <td>رقم الهاتف: {{ $student->mobile ?? 'لا يوجد' }}</td>
                        </tr>
                        @if($student->mobile2)
                        <tr>
                            <td>رقم هاتف آخر: {{ $student->mobile2 }}</td>
                        </tr>
                        @endif
                        @if($student->student_mobile)
                        <tr>
                            <td>رقم هاتف الطالب: {{ $student->student_mobile }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>الجنس: {{ $student->gender ? 'أنثى' : 'ذكر' }}</td>
                        </tr>
                        @if($student->division !== null)
                        <tr>
                            <td>الشعبة: {{ $student->division == 0 ? 'العلمية' : 'الأدبية' }}</td>
                        </tr>
                        @endif
                        @if($student->sub_language !== null)
                        <tr>
                            <td>اللغة الثانية: {{ $student->sub_language == 0 ? 'الفرنسية' : 'الألمانية' }}</td>
                        </tr>
                        @endif
                        @if($student->edu_type !== null)
                        <tr>
                            <td>نوع التعليم: {{ $student->edu_type == 0 ? 'تعليم عربي' : 'تعليم لغات' }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>دفع عند الحجز: {{ $student->given_money ?? 0 }} ج</td>
                        </tr>
                    </table>
                </div>
                <hr class="p-0 m-0">
                <div class="mt-4">
                    <h6 class="mb-0"><strong>المجموعات المتصلة</strong></h6>
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-primary">المادة</th>
                                <th class="text-primary">المعلم</th>
                                <th class="text-primary">المجموعة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->groups as $group)
                            <tr>
                                <td>ال{{ $group->group->lesson->subject->name_ar }}</td>
                                <td>أ/ {{ $group->group->lesson->teacher->profile->name }}</td>
                                <td>{{ empty($group->group->group_name) ? 'لا يوجد إسم' : $group->group->group_name }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3">
                                    <div class="alert alert-info p-3 text-center mb-0">لا توجد مجموعات حتى الآن</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <hr class="p-0 m-0">
                <div class="mt-4">
                    <h6 class="mb-0"><strong>قائمة الغياب</strong></h6>
                    <table class="table table-hover mb-0">
                        <thead>
                            <th class="text-primary">تم خلال</th>
                            <th class="text-primary">التاريخ</th>
                        </thead>
                        <tbody>
                            @forelse ($absences as $absence)
                            <tr title="تم التسجيل {{ $absence->created_at->diffForHumans() }}">
                                <td>
                                    @if ($absence->group)
                                    {{ empty($absence->group->group_name) ? ('أ/ ' . $absence->group->lesson->teacher->profile->name . ' - ' . 'مجموعة بلا إسم') : ('أ/ ' . $absence->group->lesson->teacher->profile->name . ' - ' . $absence->group->group_name) }}
                                    @else
                                    يوم {{ $absence->join_at }}
                                    @endif
                                </td>
                                <td>{{ $absence->join_at }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2">
                                    <div class="alert mb-0 alert-info text-center p-3">لا يوجد غياب حتى الآن</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <a href="{{ route('absences.reports', [
                    'level' => $student->level_id,
                    'from' => '2019-09-25',
                    'to' => date('Y-m-d'),
                    'student' => $student->id
                    ]) }}">
                        <button class="my-2 mb-3 btn-sm btn btn-info">
                            <i class="fas fa-eye"></i>
                            عرض السجل بالكامل
                        </button>
                    </a>
                </div>
                <hr class="p-0 m-0">
                <div class="mt-4">
                    <h6 class="mb-0"><strong>سجل الإختبارات</strong></h6>
                    <table class="table table-hover mb-0">
                        <thead>
                            <th class="text-primary">الإختبار</th>
                            <th class="text-primary">الدرجة</th>
                            <th class="text-primary">التاريخ</th>
                            <th class="text-primary">نوع الإختبار</th>
                        </thead>
                        <tbody>
                            @forelse($exams as $exam)
                            <tr>
                                <td>
                                    ال{{ $exam->exam->subject->name_ar }}
                                    - أ/ {{ $exam->exam->teacher->profile->name }}
                                </td>
                                <td>
                                    @if($exam->result)
                                    {{ $exam->result->mark['full_mark'] }} /
                                    {{ $exam->result->mark['correct_answers'] }}
                                    @else
                                    لا يوجد بعد
                                    @endif
                                </td>
                                <td title="{{ date('Y-m-d', strtotime($exam->created_at)) }}">
                                    {{ $exam->created_at->diffForHumans() }}
                                </td>
                                <td>
                                    {{ $exam->enter_type == 0 ? 'إلكتروني' : 'ورقي' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4">
                                    <div class="alert alert-info mb-0 text-center p-3">
                                        لا يوجد درجات حتى الآن
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4 col-sm-5 col-12">
                <div class="mb-3" style="overflow: hidden;">
                    <div class="overflow-hidden">
                        <h6 class="mb-3"><strong>بيانات فنية</strong></h6>
                        <div class="d-flex justify-content-center" id="barcode-element">
                            {!! DNS1D::getBarcodeSVG($student->id, 'C128'); !!}
                        </div>
                        <div>
                            <small style="font-size: 12px;">كود الطالب: {{ $student->code }}</small><br>
                            <small style="font-size: 12px;">المُعرف: {{ $student->id ?? 'لا يوجد' }}</small>
                        </div>
                    </div>
                    <div class="btn btn-sm btn-secondary" id="print-barcode" data-id="{{ $student->id }}">طباعة البيانات</div>
                    <div class="btn btn-sm btn-secondary" onclick="document.getElementById('student-login').submit();">تسجيل الدخول</div>
                    <form target="_blank" action="{{ route('login_by_id', $student->id) }}" method="post" class="d-none" id="student-login">
                        @csrf
                    </form>
                </div>
                <hr class="m-0 p-0">
                <div class="mt-3">
                    <h6 class="mb-3">
                        <strong>الدفعات السابقة</strong>
                        @if(auth()->user()->hasPermission('add-student-expenses'))
                        <i class="fas fa-plus text-success pl-1 cursor-pointer" onclick="payExpensesModal.show()" title="دفع المصروفات"></i>
                        @endif
                    </h6>
                    <table class="table table-hover mb-0">
                        <tbody>
                            @forelse($student->expenses as $expenses)
                            <tr>
                                <td title="تاريخ الدفع: {{ $expenses->created_at }} / {{ $expenses->created_at->diffForHumans() }} بواسطة {{ $expenses->user->name }}">
                                    مصروفات شهر
                                    <span>{{ $expenses->month * 1 }}</span>
                                    تم دفع
                                    <span class="badge badge-secondary">{{ $expenses->money }}ج</span>
                                    <i class="fas fa-print cursor-pointer text-primary ml-1 print-expenses-invoice-btn" title="طباعة" data-id="{{ $expenses->id }}"></i>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td>
                                    <div class="alert alert-warning p-3 mb-0">
                                        لا توجد مدفوعات سابقة حتى الآن
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(App\Models\Admin\Settings::where('name', 'students_must_choose_teachers')->select('value')->first()['value'] == 1)
                <hr class="m-0 p-0">
                <div class="mt-3">
                    <h6 class="mb-3"><strong>المُعلمين</strong></h6>
                </div>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="text-primary">المادة</th>
                            <th class="text-primary">المعلم</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($student->subjects)
                        @forelse($student->subjects->teachers ?? [] as $subject => $teacher)
                        <tr>
                            <td>ال{{ \App\Models\Admin\Subject::where('name_en', $subject)->first()['name_ar'] ?? 'غير معروفة' }}</td>
                            <td>
                                أ/ {{ \App\Models\Admin\Teacher::with('profile')->where('id', $teacher)->first()->profile->name ?? 'غير معروف' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2">
                                <div class="alert alert-warning px-3 py-3 mb-0">
                                    لا توجد بيانات عن المعلمين حتى الآن
                                </div>
                            </td>
                        </tr>
                        @endforelse
                        @else
                        <tr>
                            <td colspan="2">
                                <div class="alert alert-warning px-3 py-3 mb-0">
                                    لا توجد بيانات عن المعلمين حتى الآن
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
    <a href="javascript:void(0)" onclick="window.open('{{ route('students.print', $student->id) }}', 'طباعة بيانات الطالب', 'fullscreen=no,height=450,left=0,resizable=no,status=no,width=400,titlebar=yes,menubar=no')">
        <button class="btn btn-primary">
            <i class="fas fa-print"></i>
            طباعة البيانات الأساسية
        </button>
    </a>
    @if(auth()->user()->hasPermission('edit-student'))
    <a href="{{ route('students.edit', $student->id) }}">
        <button class="btn btn-success">
            <i class="fas fa-edit"></i>
            تعديل
        </button>
    </a>
    @endif
    @if(auth()->user()->hasPermission('delete-student'))
    <a href="javascript:void(0)" onclick="new bootstrap.Modal(document.getElementById('deleteModal')).show()">
        <button class="btn btn-danger">
            <i class="fas fa-trash"></i>
            حذف
        </button>
    </a>
    @endif
    <a
        class="btn btn-success {{ !$student->mobile ? 'disabled' : '' }}"
        href="{{ $student->mobile ? ('https://wa.me/+2' . $student->mobile) : 'javascript:void(0)' }}"
        target="_blank">
        <i class="fab fa-whatsapp"></i>
        واتساب ولي الأمر
    </a>
</div>
@endsection

@section('js')
<script src="{{ asset('/dist/js/show_student.js') }}"></script>
@if(Session::has('success'))
<script>
    $.notify("{{ Session::get('success') }}", 'success');
</script>
@endif

@if(Session::has('error'))
<script>
    $.notify("{{ Session::get('error') }}", 'error');
</script>
@endif

@if(Session::has('show_expenses_modal'))
<script>
    payExpensesModal.show();
</script>
@endif
@endsection