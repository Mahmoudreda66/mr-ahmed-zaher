@extends('parents.layouts.app', ['nav_transparent' => false])

@section('title')
أولياء الأمور - الرئيسية
@endsection

@section('content')
<div class="container mt-5">
	<div class="bg-white p-3">
		<div class="row">
			<div class="col-md-8 col-sm-7 col-12 mb-3 mb-md-0 border-start">
                @if(Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
				<h6><strong>تفاصيل عامة</strong></h6>
				<table class="table table-hover mb-0 mt-3">
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
                    <tr>
                        <td>دفع عند الحجز: {{ $student->given_money ?? 0 }} ج</td>
                    </tr>
				</table>
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
                <div class="mt-4" id="absences">
                    <h6 class="mb-0"><strong>قائمة الغياب</strong></h6>
                    <table class="table table-hover mb-0">
                        <thead>
                            <th class="text-primary">تم خلال</th>
                            <th class="text-primary">الحالة</th>
                            <th class="text-primary">التاريخ</th>
                        </thead>
                        <tbody>
                            @forelse ($student->absence_list->reverse() as $absence)
                            <tr>
                                <td>
                                    @if ($absence->group)
                                    {{ empty($absence->group->group_name) ? ('أ/ ' . $absence->group->lesson->teacher->profile->name . ' - ' . 'مجموعة بلا إسم') : ('أ/ ' . $absence->group->lesson->teacher->profile->name . ' - ' . $absence->group->group_name) }}
                                    @else
                                    يوم {{ $absence->join_at }}
                                    @endif
                                </td>
                                <td>
                                	@if($absence->status)
                                	<small class="bg-success badge">حاضر</small>
                                	@else
                                	<small class="bg-danger badge">غائب</small>
                                	@endif
                                </td>
                                <td>{{ $absence->join_at }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3">
                                    <div class="alert mb-0 alert-info text-center p-3">لا يوجد غياب حتى الآن</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4" id="marks">
                    <h6 class="mb-0"><strong>سجل الإختبارات</strong></h6>
                    <table class="table table-hover mb-0">
                        <thead>
                            <th class="text-primary">الإختبار</th>
                            <th class="text-primary">الدرجة</th>
                            <th class="text-primary">التاريخ</th>
                            <th class="text-primary">نوع الإختبار</th>
                        </thead>
                        <tbody>
                            @forelse($student->attemps->reverse() as $exam)
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
                                <td>
                                    {{ date('Y-m-d', strtotime($exam->created_at)) }}
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
                            <small style="font-size: 12px;">المُعرف: {{ $student->id }}</small>
                        </div>
                    </div>
                </div>
                <hr class="m-0 p-0">
                <div class="mt-3" id="expenses">
                    <h6 class="mb-3">
                        <strong>المدفوعات  السابقة</strong>
                    </h6>
                    <table class="table table-hover mb-0">
                        <tbody>
                            @forelse($student->expenses->reverse() as $expenses)
                            <tr>
                                <td>
                                    مصروفات شهر
                                    <span>{{ $expenses->month * 1 }}</span>
                                    تم دفع
                                    <span class="badge bg-secondary">{{ $expenses->money }} ج</span>
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
                            <td>
                                ال{{ \App\Models\Admin\Subject::where('name_en', $subject)->first()['name_ar'] ?? 'غير معروفة' }}</td>
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
</div>
@endsection