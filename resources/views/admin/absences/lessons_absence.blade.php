@extends('admin.layouts.app', ['activePage' => 'lessons-absence-mode', 'titlePage' => "وضع الغياب"])

@section('title')
وضع الغياب
@endsection

@section('content')
<div class="content">
    
    @if(!empty($_GET['level']) && !empty($_GET['lesson']) && !empty($_GET['group']))

    <div class="allpage-loader" id="allpage-loader">
        <span></span>
    </div>

    <div class="btn-group dropstart options-cog-btn">
        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-cog"></i>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" target="_blank" href="{{ route('home') }}">فتح نافذة جديدة</a>
            </li>
            <li class="dropdown-item cursor-pointer" id="close-modal">إغلاق</li>
            <li class="dropdown-item cursor-pointer" id='end-lesson'>إنهاء الحصة</li>
        </ul>
    </div>
   
    <div class="modal fade show" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 900px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">وضع الغياب</h5>
                </div>
                <div class="modal-body">
                    <form action="/admin/absence-mode" method="post" autocomplete="off" onsubmit='return false;' id="code-form">
                        @csrf
                        <input type="hidden" id="level-id" value="{{ $_GET['level'] }}">
                        <input type="hidden" id="lesson-id" value="{{ $_GET['lesson'] }}">
                        <input type="hidden" id="group-id" value="{{ $_GET['group'] }}">
                        <div>
                            <label for="code">رمز الإستجابة السريع</label>
                            <input autofocus type="text" name="code" id="code" class="form-control">
                            <small class="form-text">قم بفحص رمز الإستجابة السريع لتسجيل حضورك</small>
                            <small class="text-danger form-text code"></small>
                        </div>
                    </form>
                    <div class="row" id="students-container">
                        <div class="students-absence-loader" id="students-absence-loader">
                            <span></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Another Group Modal -->
    <div class="modal fade" id="diffrentGroupModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">الحضور بمجموعة مختلفة</h5>
                </div>
                <div class="modal-body">
                    <form method="post" class="mb-0" id="diffrent-group-form" autocomplete="off">
                        <div class="mb-3">
                            <label>المجموعة المقيد بها</label>
                            <input type="text" name="group_name" id="group_name" readonly class="form-control">
                        </div>
                        <div class="mb-0">
                            <label for="date">التاريخ</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" id="date" class="form-control">
                        </div>
                        <input type="submit" value="" class="d-none">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary" onclick="submitDiffrenttGroup()">حفظ</button>
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
                    <form method="post" id="expenses-form" autocomplete="off">
                        <input type="hidden" name="id" id="id">
                        <div class="mb-3">
                            <label for="student" class="form-label">الطالب</label>
                            <input type="text" name="student" id="student" readonly class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="expenses" class="form-label">المصروفات</label>
                            <input type="number" name="expenses" id="expenses" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="month" class="form-label">الشهر</label>
                            <input type="number" name="month" id="month" class="form-control">
                        </div>
                        <input type="submit" value="" class="d-none">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary" id="submitExpenses">حفظ</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white p-4 container-fluid">
        <form action="{{ route('lessons_absence_mode') }}" method="get" id="absence-form" class="mb-0">
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="s_level">المرحلة</label>
                    <select name="level" id="s_level" class="form-control">
                        <option value="NULL" selected disabled>إختر الصف</option>
                        @foreach ($levels as $level)
                        <option
                        @if(!empty($_GET['level']))
                        {{ $_GET['level'] == $level->id ? 'selected' : '' }}
                        @endif
                        value="{{ $level->id }}">
                        {{ $level->name_ar }}
                        </option>
                        @endforeach
                    </select>
                    <small class="text-danger form-text level"></small>
                </div>
                <div class="col-md-6 col-12 mb-3">
                    <label for="lesson">الحصة</label>
                    <select name="lesson" {{ $_GET['lesson'] ?? null && $_GET['level'] ?? null ? '' : 'disabled' }} id="lesson" class="form-control">
                        <option value="NULL" disabled selected>إختر الحصة</option>
                    </select>
                    <small class="text-danger form-text lesson"></small>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-12">
                    <label for="group">المجموعة</label>
                    <select name="group" id="group" class="form-control" disabled>
                        <option value="NULL" disabled selected>إختر المجموعة</option>
                    </select>
                    <small class="text-danger form-text group"></small>
                </div>
                <div class="col-md-6 col-12">
                    <label for="date">التاريخ</label>
                    <input type="date" name="date" value="{{ $_GET['date'] ?? date('Y-m-d') }}" id="date" class="form-control">
                    <small class="text-danger form-text date"></small>
                </div>
            </div>
            <button class="btn-primary mt-3 btn mb-0">تشغيل الوضع</button>
        </form>
    </div>

    <div class="bg-white p-4 container-fluid mt-3">
        <h6 class="fw-bold">ملاحظات هامة</h6>
        <ul style="list-style: square;" class="mb-0">
            <li class="mb-1">عند تشغيل وضع الغياب لا يمكن  الضغط على أي زر بخلاف زر إغلاق الوضع</li>
            <li class="mb-1">سيتم عرض إسم كل طالب عند تسجيله مع صوت الصافرة</li>
            <li class="mb-1">لن يتم إحتساب الغياب إلا عند الضغط على زر إغلاق الحصة وسيتم إحتساب الحضور فقط</li>
        </ul>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('dist/js/lessons_absence_mode.js') }}"></script>
@endsection