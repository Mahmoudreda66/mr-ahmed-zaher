<div class="modal fade" id="LevelModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">المرحلة المطلوبة</h5>
            </div>
            <div class="modal-body">
                <form action="" method="get" id="students-list-form" autocomplete="off" class="mb-0">
                    <div class="mb-0">
                        <label for="level">المرحلة</label>
                        <select name="level" id="level" class="form-control" disabled>
                            <option value="NULL" disabled selected>إختر المرحلة</option>
                        </select>
                    </div>
                    <input type="submit" value="" class="d-none">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('students-list-form').submit();">عرض</button>
            </div>
        </div>
    </div>
</div>

<div class="sidebar" data-color="purple" data-background-color="white" data-image="{{ asset('material') }}/img/sidebar-1.jpg">
    <div class="logo mt-0 pt-0">
        <div class="custom-logo">
            <div class="user-info text-center mr-0">
                <span class="user-name">أ/ {{ auth()->user()->name }}</span><br>
                <span class="user-role">
                    المنصب:
                    @foreach(auth()->user()->roles as $index => $role)
                    {{ $role->display_name }}{{ $index < auth()->user()->roles->count() - 1 ? '، ' : '' }}
                    @endforeach
                </span>
            </div>
        </div>
    </div>
    <div class="sidebar-wrapper">
        <ul class="nav">
            @if(auth()->user()->hasPermission('show-dashboard'))
            <li class="nav-item {{ $activePage == 'dashboard' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="fas fa-tachometer-alt" style="font-size: 17px;"></i>
                    <p>الرئيسية</p>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('students'))
            <li class="nav-item {{ ($activePage == 'students.index' || $activePage == 'students.create' || $activePage == 'filled_absence_list') ? ' active' : '' }}">
                <a class="nav-link" data-toggle="collapse" href="#studentsLinks" aria-expanded="false">
                    <i class="fas fa-users" style="font-size: 17px;"></i>
                    <p>الطلاب</p>
                </a>
                <div class="collapse" id="studentsLinks">
                    <ul class="nav">
                        @if(auth()->user()->hasPermission('add-student'))
                        <li class="nav-item {{ $activePage == 'students.create' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('students.create') }}">
                                <span class="sidebar-normal">إضافة طالب</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('show-students'))
                        <li class="nav-item {{ $activePage == 'students.index' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('students.index') }}">
                                <span class="sidebar-normal">قائمة الطلاب</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('students-list'))
                        <li class="nav-item {{ $activePage == '' ? ' active' : '' }}">
                            <a class="nav-link" id="print-students-list" href="javascript:void(0)" data-route="{{ route('students.list') }}">
                                <span class="sidebar-normal">طباعة كشف الأسماء</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('students-absence-list'))
                        <li class="nav-item {{ $activePage == '' ? ' active' : '' }}">
                            <a class="nav-link" id="print-absence-list" href="javascript:void(0)" data-route="{{ route('absences.list') }}">
                                <span class="sidebar-normal">طباعة كشف الغياب</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('print-barcodes'))
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0)" id="print-barcodes" data-route="{{ route('barcodes.index') }}">
                                <span class="sidebar-normal">طباعة كروت باركود</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('filled-absence-list'))
                        <li class="nav-item {{ $activePage == 'filled_absence_list' ? 'active' : '' }}">
                            <a class="nav-link" href="javascript:void(0)" id="filled-absence-list" data-route="{{ route('filled_absence_list') }}">
                                <span class="sidebar-normal">طباعة كشف غياب مملوء</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasPermission('money'))
            <li class="nav-item {{ ($activePage == 'statistics.index' || $activePage == 'out-money.index' || $activePage == 'expenses.index') ? ' active' : '' }}">
                <a class="nav-link" data-toggle="collapse" href="#moneyLinks" aria-expanded="false">
                    <i class="fas fa-chart-pie" style="font-size: 17px;"></i>
                    <p>الأموال</p>
                </a>
                <div class="collapse" id="moneyLinks">
                    <ul class="nav">
                        @if(auth()->user()->hasPermission('students-expenses-list'))
                        <li class="nav-item {{ $activePage == 'expenses.index' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('expenses.index') }}">
                                <span class="sidebar-normal">المصروفات</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('show-statistics'))
                        <li class="nav-item {{ $activePage == 'statistics.index' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('statistics.index') }}">
                                <span class="sidebar-normal">الإحصائيات</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('show-out-money'))
                        <li class="nav-item {{ $activePage == 'out-money.index' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('out-money.index') }}">
                                <span class="sidebar-normal">الأموال الخارجة</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasPermission('teachers'))
            <li class="nav-item {{ ($activePage == 'teachers.index' || $activePage == 'teachers.create') ? ' active' : '' }}">
                <a class="nav-link" data-toggle="collapse" href="#teachersLinks" aria-expanded="false">
                    <i class="fas fa-user" style="font-size: 17px;"></i>
                    <p>المعلمين</p>
                </a>
                <div class="collapse" id="teachersLinks">
                    <ul class="nav">
                        @if(auth()->user()->hasPermission('add-teacher'))
                        <li class="nav-item {{ $activePage == 'teachers.create' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('teachers.create') }}">
                                <span class="sidebar-normal">إضافة معلم</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('show-teachers'))
                        <li class="nav-item {{ $activePage == 'teachers.index' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('teachers.index') }}">
                                <span class="sidebar-normal">قائمة المعلمين</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasPermission('lessons'))
            <li class="nav-item {{ ($activePage == 'lessons.index' || $activePage == 'lessons.create') ? ' active' : '' }}">
                <a class="nav-link" data-toggle="collapse" href="#lessonsLinks" aria-expanded="false">
                    <i class="fas fa-atom" style="font-size: 17px;"></i>
                    <p>الحصص</p>
                </a>
                <div class="collapse" id="lessonsLinks">
                    <ul class="nav">
                        @if(auth()->user()->hasPermission('add-lesson'))
                        <li class="nav-item {{ $activePage == 'lessons.create' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('lessons.create') }}">
                                <span class="sidebar-normal">إضافة حصة</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('show-lessons'))
                        <li class="nav-item {{ $activePage == 'lessons.index' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('lessons.index') }}">
                                <span class="sidebar-normal">قائمة الحصص</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasPermission('exams'))
            <li class="nav-item {{ ($activePage == 'exams.index' || $activePage == 'exams.create') || $activePage == 'exams.attemps' || $activePage == 'exams.marks' || $activePage == 'exams.manual-marks' || $activePage == 'exams.correcting' ? ' active' : '' }}">
                <a class="nav-link" data-toggle="collapse" href="#examsLinks" aria-expanded="false">
                    <i class="fas fa-file" style="font-size: 17px;"></i>
                    <p>الإختبارات</p>
                </a>
                <div class="collapse" id="examsLinks">
                    <ul class="nav">
                        @if(auth()->user()->hasPermission('add-exam'))
                        <li class="nav-item {{ $activePage == 'exams.create' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('exams.create') }}">
                                <span class="sidebar-normal">إضافة إختبار</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('show-exams'))
                        <li class="nav-item {{ $activePage == 'exams.index' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('exams.index') }}">
                                <span class="sidebar-normal">قائمة الإختبارات</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('exams-attemps'))
                        <li class="nav-item {{ $activePage == 'exams.attemps' ? ' active' : '' }}">
                            <a class="nav-link" id="exams-attemps" data-route="{{ route('exams-attemps.index') }}" href="javascript:void(0)">
                                <span class="sidebar-normal">سجل دخول الإختبارات</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('exams-marks'))
                        <li class="nav-item {{ $activePage == 'exams.marks' ? ' active' : '' }}">
                            <a class="nav-link" href="javascript:void(0)" data-route="{{ route('exams-marks.index') }}" id="exams-marks">
                                <span class="sidebar-normal">درجات الإختبارات</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('exams-manual-marks'))
                        <li class="nav-item {{ $activePage == 'exams.manual-marks' ? ' active' : '' }}">
                            <a class="nav-link" href="javascript:void(0)" data-route="{{ route('manual-marks.create') }}" id="manual-marks">
                                <span class="sidebar-normal">
                                    إضافة الدرجات يدوياً
                                </span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('exams-correcting'))
                        <li class="nav-item {{ $activePage == 'exams.correcting' ? ' active' : '' }}">
                            <a class="nav-link" href="javascript:void(0)" data-route="{{ route('exams-correcting') }}" id="exams-correcting">
                                <span class="sidebar-normal">
                                    تصحيح الإختبارات الإلكترونية
                                </span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasPermission('certificates'))
            <li class="nav-item {{ ($activePage == 'empty-marks-certificate' || $activePage == 'filled-marks-certificate') ? ' active' : '' }}">
                <a class="nav-link" data-toggle="collapse" href="#certificatesLinks" aria-expanded="false">
                    <i class="fas fa-certificate" style="font-size: 17px;"></i>
                    <p>الشهادات</p>
                </a>
                <div class="collapse" id="certificatesLinks">
                    <ul class="nav">
                        @if(auth()->user()->hasPermission('empty-marks-certificate'))
                        <li class="nav-item {{ $activePage == 'empty-marks-certificate' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('empty_marks_certificate') }}">
                                <span class="sidebar-normal">شهادة درجات فارغة</span>
                            </a>
                        </li>
                        <li class="nav-item {{ $activePage == 'students-empty-marks-certificate' ? ' active' : '' }}">
                            <a class="nav-link" href="javascript:void(0)" id="students_empty_marks_certificate" data-route="{{ route('students_empty_marks_certificate') }}">
                                <span class="sidebar-normal">شهادة درجات فارغة بالأسماء</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('filled-marks-certificate'))
                        <li class="nav-item {{ $activePage == 'filled-marks-certificate' ? ' active' : '' }}">
                            <a class="nav-link" id="filled-marks-certificate" href="javascript:void(0)" data-route="{{ route('filled_marks_certificate') }}">
                                <span class="sidebar-normal">شهادة درجات مملوئة</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasPermission('absences'))
            <li class="nav-item {{ ($activePage == 'absences.latest_index' || $activePage == 'absences.manual' || $activePage == 'teachers.absences' || $activePage == 'lessons-absence-mode' || $activePage == 'day-absence-mode' || $activePage == 'absence-report') ? ' active' : '' }}">
                <a class="nav-link" data-toggle="collapse" href="#absenceLinks" aria-expanded="false">
                    <i class="fas fa-file-alt" style="font-size: 17px;"></i>
                    <p>الغياب</p>
                </a>
                <div class="collapse" id="absenceLinks">
                    <ul class="nav">
                        @if(auth()->user()->hasPermission('last-students-absences-records'))
                        <li class="nav-item {{ $activePage == 'absences.latest_index' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('absences.latest_index') }}">
                                <span class="sidebar-normal">آخر التسجيلات</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('teachers-absences'))
                        <li class="nav-item {{ $activePage == 'teachers.absences' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('teachers-absences.index') }}">
                                <span class="sidebar-normal">غياب المعلمين</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('lessons-absence-mode'))
                        <li class="nav-item {{ $activePage == 'lessons-absence-mode' ? ' active' : '' }}">
                            <a class="nav-link" id="lessons-absence-mode" data-route="{{ route('lessons_absence_mode') }}" href="javascript:void(0)">
                                <span>وضع غياب الحصص</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('day-absence-mode'))
                        <li class="nav-item {{ $activePage == 'day-absence-mode' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('day_absence_mode') }}">
                                <span>وضع غياب اليوم</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('absence-report'))
                        <li class="nav-item {{ $activePage == 'absence-report' ? ' active' : '' }}">
                            <a class="nav-link" id="absence-reports" href="javascript:void(0)" data-route="{{ route('absences.reports') }}">
                                <span>تقارير الغياب</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasPermission('users-permissions'))
            <li class="nav-item {{ $activePage == 'users-management' ? ' active' : '' }}">
                <a class="nav-link" data-toggle="collapse" href="#usersLinks" aria-expanded="false">
                    <i class="fas fa-lock" style="font-size: 17px;"></i>
                    <p>المستخدمين والصلاحيات</p>
                </a>
                <div class="collapse" id="usersLinks">
                    <ul class="nav">
                        <li class="nav-item {{ $activePage == 'users-management' ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <span class="sidebar-normal">إدارة المستخدمين</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" target="_blank" href="{{ url('/admin/user-management') }}">
                                <span class="sidebar-normal">الصلاحيات والمهام</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasPermission('system-settings'))
            <li class="nav-item {{ $activePage == 'settings.index' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('settings.index') }}">
                    <i class="fas fa-cog" style="font-size: 17px;"></i>
                    <p>الإعدادات</p>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('database-management'))
            <li class="nav-item {{ $activePage == 'database.index' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('database.index') }}">
                    <i class="fas fa-database" style="font-size: 17px;"></i>
                    <p>قاعدة البيانات</p>
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#showLinks" aria-expanded="false">
                    <i class="fas fa-eye" style="font-size: 17px;"></i>
                    <p>عرض الموقع</p>
                </a>
                <div class="collapse" id="showLinks">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link" target="_blank" href="{{ route('students.exams.index') }}">
                                <span class="sidebar-normal">واجهة الإختبارات</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" target="_blank" href="{{ route('parents.home') }}">
                                <span class="sidebar-normal">واجهة أولياء الأمور</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" target="_blank" href="{{ route('studentsApplication.home') }}">
                                <span class="sidebar-normal">واجهة تقديم الطلاب</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</div>

<script>
    function getLevels(href) {
        let levelsModalAside = new bootstrap.Modal(document.getElementById('LevelModal')),
            form = document.getElementById('students-list-form'),
            levels = form.querySelector('#level');

        levelsModalAside.show();

        form.action = href;

        let getLevels = new XMLHttpRequest();

        getLevels.open('GET', '{{ route("levels.get") }}');
        getLevels.onload = function() {
            if (this.readyState === 4 && this.status === 200) { // success
                let data = JSON.parse(this.responseText);
                levels.innerHTML = '<option value="NULL" disabled selected>إختر المرحلة</option>';
                levels.setAttribute('disabled', '');
                data.forEach(function(element) {
                    let option = document.createElement('option'),
                        text = document.createTextNode(element.name_ar);
                    option.appendChild(text);
                    option.value = element.id;
                    levels.appendChild(option);
                });
                levels.removeAttribute('disabled');
            } else {
                $.notify('لقد حدث خطأ غير متوقع', 'success')
            }
        }
        getLevels.send();

        levels.onchange = function() {
            form.submit();
        }
    }

    let workingElements = [
        'filled-absence-list',
        'print-students-list',
        'print-absence-list',
        'exams-attemps',
        'exams-marks',
        'manual-marks',
        'exams-correcting',
        'marks-cards',
        'lessons-absence-mode',
        'absence-reports',
        'print-barcodes',
        'filled-marks-certificate',
        'students_empty_marks_certificate'
    ];

    for (let i = 0; i < workingElements.length; i++) {
        el = document.getElementById(workingElements[i]);

        if (el) {
            el.onclick = function() {
                getLevels(this.dataset.route);
            }
        }
    }
</script>