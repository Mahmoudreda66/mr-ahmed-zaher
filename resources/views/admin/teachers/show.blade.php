@extends('admin.layouts.app', ['activePage' => 'teachers.index', 'titlePage' => "عرض المعلم " . $teacher->name])

@section('title')
عرض المعلم {{ $teacher->name }}
@endsection

@section('content')

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">حذف المعلم</h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('teachers.destroy', $teacher->id) }}" method="post" id="delete-form" autocomplete="off">
                    @csrf
                    {{ method_field('delete') }}
                    <input type="hidden" name="id" id="id" value="{{ $teacher->id }}">
                    <div class="mb-2" style="font-size: 15px;">هل أنت متأكد من عملية الحذف؟ سيتم أرشفة بيانات المعلم لوقت لاحق</div>
                    <div class="mb-3">
                        <input type="text" id="teacher" value="{{ $teacher->profile->name }}" class="form-control" readonly>
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

<div class="content show-teacher-p">
    <div class="bg-white p-4 mb-2">
        <div class="row">
            <div class="col-md-8 col-sm-7 col-12 mb-3 mb-md-0 border-left">
                <div class="mb-3">
                    <h6><strong>تفاصيل عامة</strong></h6>
                    <table class="table table-hover mt-3 mb-0">
                        <tr>
                            <td>الإسم: {{ $teacher->profile->name }}</td>
                        </tr>
                        <tr>
                            <td>المادة: ال{{ $teacher->subject->name_ar }}</td>
                        </tr>
                        <tr>
                            <td> يُدرس: 
                                @foreach (json_decode($teacher->levels, true) as $i => $level)
                                    {{ App\Models\Admin\Level::find($level)->name_ar }} {{ $i < count(json_decode($teacher->levels, true)) - 1 ? ' - ' : ' ' }}        
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td>رقم الهاتف: {{ $teacher->profile->phone }}</td>
                        </tr>
                        <tr>
                            <td>عدد الحصص: {{ $teacher->lessons->count() }}</td>                            
                        </tr>
                        <tr>
                            <td>
                                <div class="border-left d-inline-block" style="padding-left: 10px; margin-left: 10px;">
                                حصص تم حضورها: {{ $presentCount }}
                                </div>
                                <span>حصص تم غيابها:  {{ $absenceCount }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <h6><strong>جدول الإختبارات</strong></h6>
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-primary">الصف</th>
                            <th class="text-primary">التوقيت</th>
                            <th class="text-primary">النوع</th>
                            <th class="text-primary">التاريخ</th>
                            <th class="text-primary">أدى الإختبار</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teacher->exams as $exam)
                        <tr>
                            <td>{{ $exam->level->name_ar }}</td>
                            <td>{{ $exam->duration }} دقيقة</td>
                            <td>{{ $exam->type == 0 ? 'إلكتروني' : 'ورقي' }}</td>
                            <td>{{ $exam->date }}</td>
                            <td>{{ $exam->attemps->count() }} طالب</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="alert alert-info p-3 text-center mb-0">
                                    لا يوجد إختبارات حتى الآن
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <hr>
                <h6><strong>سجل الحصص</strong></h6>
                <table class="table table-hover text-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-primary text-center">#</th>
                            <th class="text-primary text-center">المدة</th>
                            <th class="text-primary text-center">المرحلة</th>
                            <th class="text-primary text-center">خيارات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lessons as $lesson)
                        <tr>
                            <td>
                                {{ $lesson->id }}
                            </td>
                            <td>{{ $lesson->duration }}</td>
                            <td>{{ $lesson->level->name_ar }}</td>
                            <td>
                                <a href="{{ route('lessons.show', $lesson->id) }}">
                                    <i class="fas fa-eye text-success"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                           <td colspan="4">
                               <div class="alert alert-info p-3 text-center mb-0">
                                   لا توجد  حصص حتى الآن
                               </div>
                           </td> 
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <hr>
                <h6><strong>سجل الحضور والغياب</strong></h6>
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-primary">الصف</th>
                            <th class="text-primary">المجموعة</th>
                            <th class="text-primary">الحالة</th>
                            <th class="text-primary">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absences->absences as $absence)
                        <tr>
                            <td>
                                {{ $absence->group->lesson->level->name_ar }}
                            </td>
                            <td>
                                {{ empty($absence->group->group_name) ? 'مجموعة بلا إسم' : $absence->group->group_name }}
                            </td>
                            <td>{!! $absence->status == 1 ? '<span class="badge badge-success">حاضر</span>' : '<span class="badge badge-danger">غائب</span>' !!}</td>
                            <td title="{{ $absence->created_at }}">{{ $absence->join_at }}</td>
                        </tr>
                        @empty
                        <tr>
                           <td colspan="4">
                               <div class="alert alert-info p-3 text-center mb-0">
                                   لا يوجد سجل حتى الآن
                               </div>
                           </td> 
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 col-sm-5 col-12">
                <div style="width: 100%;">
                    @if($teacher->profile->image)
                    <img
                    style="width: 100%;"
                    src="{{ asset('/images/teachers/' . $teacher->profile->image) }}" alt="صورة {{ $teacher->profile->name }}">
                    @else
                    <img src="{{ asset('dist/images/default-teacher.png') }}" alt="صورة المعلم البديلة"
                    style="width: 100%;">
                    @endif
                </div>
                @if(App\Models\Admin\Settings::where('name', 'students_must_choose_teachers')->select('value')->first()['value'] == 1)
                <hr>
                <h6><strong>الطلبة الحضور <span id="students-counter"></span></strong></h6>
                <div id="counts" class="table-flex">
                </div>
                <div class="allpage-loader d-flex position-relative bg-light" id="students-loader" style="height: 300px;">
                    <span></span>
                </div>
                <div id="students-container"></div>
                @endif
            </div>
        </div>
    </div>
    @if(auth()->user()->hasPermission('edit-teacher'))
    <a href="{{ route('teachers.edit', $teacher->id) }}">
        <button class="btn btn-success">
            <i class="fas fa-edit"></i>
            تعديل
        </button>
    </a>
    @endif
    @if(auth()->user()->hasPermission('delete-teacher'))
    <a href="javascript:void(0)" onclick="new bootstrap.Modal(document.getElementById('deleteModal')).show()">
        <button class="btn btn-danger">
            <i class="fas fa-trash"></i>
            حذف
        </button>
    </a>
    @endif
</div>
@endsection

@section('js')
<script src="{{ asset('/dist/js/show_teachers.js') }}"></script>
@endsection