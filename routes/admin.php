<?php

use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ExpensesController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\AbsenceController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TeachersAbsenceController;
use App\Http\Controllers\Exams\ExamSectionController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Exams\ExamController as ExamsExamController;
use App\Http\Controllers\Exams\ExamQuestionController;
use App\Http\Controllers\Exams\ExamsEnterAttempsController;
use App\Http\Controllers\Admin\ExamsResultsController;
use App\Http\Controllers\Exams\ExamsCorrectingController;
use App\Http\Controllers\Admin\LessonsGroupsController;
use App\Http\Controllers\Admin\DatabaseController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CertificateController;
use App\Models\Admin\LessonsGroups;
use Illuminate\Http\Request;
use App\Models\Admin\Student;
use App\Models\Admin\LessonsGroupsStudent;
use App\Models\Admin\Level;
use App\Models\Exams\Exam;
use App\Models\Admin\Teacher;
use App\Models\Admin\Lesson;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::group(['prefix' => 'admin'], function () {
    // caching the app info
    if (Schema::hasTable('settings')) {
        cache()->add('app_name', \App\Models\Admin\Settings::where('name', 'place_name')->select('value')->first()['value'], 68400);
        cache()->add('app_levels', Level::orderBy('id', 'ASC')->get());
    }

    // admins auth routes
    Auth::routes(['register' => false]);

    Route::group(['middleware' => ['auth', 'activeAdmin']], function () {

        // dashboard routes
        Route::redirect('', '/admin/home');
        Route::get('/home', [HomeController::class, 'index'])->name('home');
        Route::get('profile', ['as' => 'profile.edit', 'uses' => 'App\Http\Controllers\Admin\ProfileController@edit']);
        Route::put('profile', ['as' => 'profile.update', 'uses' => 'App\Http\Controllers\Admin\ProfileController@update']);
        Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'App\Http\Controllers\Admin\ProfileController@password']);

        // students routes
        Route::get('students/print/{id}', [StudentController::class, 'print'])->name('students.print');
        Route::get('students/search', [StudentController::class, 'search'])->name('students.search');
        Route::get('students/search-a', [StudentController::class, 'search_a']);
        Route::get('students/list', [StudentController::class, 'list'])->name('students.list');
        Route::get('students/level/{level}', function ($level) {

            Level::findOrFail($level);
            return Student::with('absence_list')->where([
                ['level_id', $level]
            ])->get();
        });
        Route::get('delete', function () {
            $st = Student::all();
        });
        Route::get('students/find-by-id-group/{id}/{group}', function ($id, $group) {

            $student = Student::findOrFail($id);

            $groupObject = LessonsGroups::findOrFail($group);

            $studentGroup = LessonsGroupsStudent::with('group')
            ->where('student_id', $student->id)
            ->whereHas('group.lesson', function ($q) use ($groupObject) {
                $q->where('subject_id', $groupObject->lesson->subject_id);
            })
            ->first();

            return ['student' => $student, 'group' => $studentGroup];            
        });
        Route::get('students/print-card/{id}', [StudentController::class, 'print_card']);
        Route::get('students/fill-absence-list', [StudentController::class, 'fill_absence_list'])->name('fill_absence_list');
        Route::get('students/filled-absence-list', [StudentController::class, 'filled_absence_list'])->name('filled_absence_list');
        Route::resource('students', StudentController::class);

        // barcodes routes
        Route::get('print-barcodes', [StudentController::class, 'print_barcodes'])->name('barcodes.index');

        // statistics routes
        Route::resource('out-money', StatisticsController::class);
        Route::get('statistics', [StatisticsController::class, 'statistics_index'])->name('statistics.index');
        Route::get('statistics/secondary-3', [StatisticsController::class, 'secondary3_statistics_index'])->name('3sec.statistics.index');

        // settings routes
        Route::get('settings/expenses/{level}', [SettingsController::class, 'expenses'])->name('expenses.get');
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

        // expenses routes
        Route::delete('expenses/force-delete/{id}', [ExpensesController::class, 'force_delete']);
        Route::put('expenses/restore/{id}', [ExpensesController::class, 'restore']);
        Route::get('expenses/print-invoice/{id}', [ExpensesController::class, 'print_invoice'])->name('expenses.print-invoice');
        Route::get('expenses/search', [ExpensesController::class, 'search'])->name('expenses.search');
        Route::resource('expenses', ExpensesController::class);

        // students absence routes
        Route::get('absences/list', [StudentController::class, 'absence_list'])->name('absences.list');
        Route::get('lessons-absence-mode', [AbsenceController::class, 'lessons_absence'])->name('lessons_absence_mode');
        Route::post('lessons-absence-mode', [AbsenceController::class, 'store_lessons_absence']);
        Route::post('another-group-lessons-absence-mode', [AbsenceController::class, 'store_lessons_absence_a_group']);
        Route::get('day-absence-mode', [AbsenceController::class, 'day_absence'])->name('day_absence_mode');
        Route::post('day-absence-mode', [AbsenceController::class, 'store_day_absence']);
        Route::post('day-absence-mode/end', [AbsenceController::class, 'end_day'])->name('absences.day.end_day');
        Route::post('absences/end-lesson', [AbsenceController::class, 'end_lesson']);
        Route::get('absences', [AbsenceController::class, 'latest_index'])->name('absences.latest_index');
        Route::delete('absences', [AbsenceController::class, 'destroy'])->name('absences.destroy');
        Route::put('absences/toggle/{id}', [AbsenceController::class, 'toggle']);
        Route::get('absences/reports', [AbsenceController::class, 'reports'])->name('absences.reports');

        // levels routes
        Route::get('get-levels', function () {

            return cache()->get('app_levels', Level::orderBy('id', 'ASC')->get());

        })->name('levels.get');
        Route::get('levels/get-ids/{ids}', [LevelController::class, 'get_by_ids']);

        // teachers routes
        Route::get('students-teacher/{id}', [TeacherController::class, 'get_students']);
        Route::resource('teachers', TeacherController::class);

        // teachers absences routes
        Route::get('teacher-levels-subject/{id}', function ($id) {

            $teacher = Teacher::with('subject')->select('levels', 'subject_id')->find($id);
            if ($teacher) {
                return $teacher;
            } else {
                abort(404);
            }
        });
        Route::put('teachers-absences/toggle/{id}', [TeachersAbsenceController::class, 'toggle']);
        Route::resource('teachers-absences', TeachersAbsenceController::class);

        // lessons routes
        Route::get('get-lessons/{level}', function ($level) {

            $lessons = Lesson::with('subject', 'teacher')->where('level_id', $level)->get();
            $lessonsContainer = [];
            foreach ($lessons as $lesson) {
                $lessonsContainer[] = [$lesson, $lesson->teacher->profile->name];
            }

            return response()->json($lessonsContainer);
        })->name('lessons.get');
        Route::get('get-lessons-teacher/{teacher}', function ($teacher) {

            return response()->json(Lesson::with('subject', 'level')->where('teacher_id', $teacher)->get());
        });
        Route::resource('lessons', LessonController::class);

        // lessons groups routes
        Route::post('lessons-groups/link', [LessonsGroupsController::class, 'link_students_to_group'])->name('link-student');
        Route::get('lessons-groups/get-groups-by-lesson/{lesson}', function ($lesson) {
            Lesson::findOrFail($lesson);

            return response()->json(LessonsGroups::where('lesson_id', $lesson)->select('id', 'group_name')->get());
        });
        Route::get('lessons-groups/get-students-by-group/{group}', function ($group) {
            $lessonGroup = LessonsGroups::with('students.absence_list', 'students.expenses', 'students.level')
                ->where('id', $group)
                ->get();

            return response()->json($lessonGroup);
        });
        Route::get('lessons-groups/get-group-by-exam/{exam}', [LessonsGroupsController::class, 'get_groups_by_exam']);
        Route::resource('lessons-groups', LessonsGroupsController::class);

        // exams routes
        Route::get('exams/view/{id}', [ExamController::class, 'view'])->name('exam.view');
        Route::put('exams/toggle', function (Request $request) {

            Exam::findOrFail($request->id)->update([
                'status' => $request->status == 0 ? now() : null
            ]);

            return redirect()->back()->with(['success' => 'تم تغيير حالة الإختبار بنجاح']);
        })->name('exams.toggle');
        Route::get('exams/get-exams/{id}', function ($id) {

            $exams = Exam::where([
                ['level_id', $id],
            ])
                ->with('teacher', 'level', 'subject')
                ->select('id', 'subject_id', 'level_id', 'teacher_id', 'date')
                ->get();

            $responseData = [];

            foreach ($exams as $exam) {
                $responseData[] = ['exam' => $exam, 'teacher' => User::select('name')->find($exam->teacher->profile->id)];
            }

            return response()->json($responseData);
        });
        Route::get('exams/get-paper-exams/{id}', function ($id) {

            $exams = Exam::where([
                ['level_id', $id],
                ['exam_type', 1]
            ])
                ->with('teacher', 'level', 'subject')
                ->select('id', 'subject_id', 'level_id', 'teacher_id', 'date')
                ->get();

            $responseData = [];

            foreach ($exams as $exam) {
                $responseData[] = ['exam' => $exam, 'teacher' => User::select('name')->find($exam->teacher->profile->id)];
            }

            return response()->json($responseData);
        });
        Route::get('exams/get-manual-exams-by-level/{id}', function ($id) {

            $exams = Exam::where([
                ['level_id', $id],
                ['exam_type', 0],
                ['type', 1]
            ])
                ->with('teacher', 'level', 'subject')
                ->select('id', 'subject_id', 'level_id', 'teacher_id', 'date')
                ->get();

            $responseData = [];

            foreach ($exams as $exam) {
                $responseData[] = ['exam' => $exam, 'teacher' => User::select('name')->find($exam->teacher->profile->id)];
            }

            return response()->json($responseData);
        });
        Route::get('exams/marks-card', [ExamController::class, 'marks_card'])->name('exams.marks_card');
        Route::get('exams/top_10/{id}', [ExamsExamController::class, 'top10_students']);
        Route::get('exams/manual-marks/create', [ExamsResultsController::class, 'create'])->name('manual-marks.create');
        Route::post('exams/manual-marks', [ExamsResultsController::class, 'store'])->name('manual-marks.store');
        Route::get('exams/exams-marks/print/{id}', [ExamsResultsController::class, 'print'])->name('marks.print');
        Route::post('exams/exams-marks/single', [ExamsResultsController::class, 'store_single']);
        Route::resource('exams-marks', ExamsResultsController::class);
        Route::resource('exams', ExamController::class);
        Route::resource('exams-attemps', ExamsEnterAttempsController::class);

        // exams sections routes
        Route::resource('exams-sections', ExamSectionController::class);
        Route::delete('exams-sections', [ExamSectionController::class, 'destroy']);

        // exams questions&correcting routes
        Route::post('exams-questions/add-choice', [ExamQuestionController::class, 'add_choice']);
        Route::put('exams-questions/edit-choice', [ExamQuestionController::class, 'edit_choice']);
        Route::delete('exams-questions/delete-choice', [ExamQuestionController::class, 'delete_choice']);
        Route::resource('exams-questions', ExamQuestionController::class);
        Route::get('exams-correcting', [ExamQuestionController::class, 'correcting_index'])->name('exams-correcting');
        Route::post('exams-correcting', [ExamsCorrectingController::class, 'store'])->name('exams-correcting.store');

        // certificates routes
        Route::get('certificate/empty-marks-certificate', [CertificateController::class, 'empty_marks_certificate'])->name('empty_marks_certificate');
        Route::get('certificate/empty-marks-certificate-stamp', function () {
            return view('admin.solid_pages.empty_marks_certificate');
        })->name('empty_marks_certificate_stamp');
        Route::get('certificate/filled-marks-certificate', [CertificateController::class, 'filled_marks_certificate'])->name('filled_marks_certificate');
        Route::get('certificate/filled-marks-certificate-stamp', [CertificateController::class, 'filled_marks_certificate_stamp'])->name('filled_marks_certificate_stamp');
        Route::get('students_empty_marks_certificate', [CertificateController::class, 'students_empty_marks_certificate'])->name('students_empty_marks_certificate');

        // subjects routes
        Route::get('get-subject_levels-by-teacher/{teacher}', function ($teacher) {
            return response()->json(Teacher::with('subject')->findOrFail($teacher));
        });

        // database routes
        Route::get('database', [DatabaseController::class, 'index'])->name('database.index');
        Route::get('database/backup', [DatabaseController::class, 'take_backup'])->name('database.backup');
        Route::post('database-many', [DatabaseController::class, 'truncate_many'])->name('database.truncate_many');
        Route::post('database-upload', [DatabaseController::class, 'upload_backup'])->name('database.upload_backup');
        Route::delete('database/delete-all', [DatabaseController::class, 'delete_all_backups'])->name('database.delete_all');

        // users routes
        Route::put('users/{id}/toggle-activity', [UserController::class, 'toggle_activity'])->name('users.toggle_activity');
        Route::resource('users', UserController::class);

        // datatable route
        Route::get('dataTableTranslation', function () {
            return response()->json(json_decode('{
            "loadingRecords": "يتم التحميل...",
            "lengthMenu": "أظهر _MENU_ مدخلات",
            "zeroRecords": "لم يعثر على أية سجلات",
            "info": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
            "infoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
            "search": "البحث: ",
            "paginate": {
                "first": "الأول",
                "previous": "السابق",
                "next": "التالي",
                "last": "الأخير"
            },
            "aria": {
                "sortAscending": ": تفعيل لترتيب العمود تصاعدياً",
                "sortDescending": ": تفعيل لترتيب العمود تنازلياً"
            },
            "select": {
                "rows": {
                    "_": "%d قيمة محددة",
                    "1": "1 قيمة محددة"
                },
                "cells": {
                    "1": "1 خلية محددة",
                    "_": "%d خلايا محددة"
                },
                "columns": {
                    "1": "1 عمود محدد",
                    "_": "%d أعمدة محددة"
                }
            },
            "buttons": {
                "print": "طباعة",
                "copyKeys": "زر <i>ctrl<\/i> أو <i>⌘<\/i> + <i>C<\/i> من الجدول<br>ليتم نسخها إلى الحافظة<br><br>للإلغاء اضغط على الرسالة أو اضغط على زر الخروج.",
                "pageLength": {
                    "-1": "اظهار الكل",
                    "_": "إظهار %d أسطر"
                },
                "collection": "مجموعة",
                "copy": "نسخ",
                "copyTitle": "نسخ إلى الحافظة",
                "xlsx": "xlsx",
                "excel": "Excel",
                "pdf": "PDF",
                "colvis": "إظهار الأعمدة",
                "colvisRestore": "إستعادة العرض",
                "copySuccess": {
                    "1": "تم نسخ سطر واحد الى الحافظة",
                    "_": "تم نسخ %ds أسطر الى الحافظة"
                }
            },
            "autoFill": {
                "cancel": "إلغاء",
                "fill": "املأ جميع الحقول بـ <i>%d&lt;\\\/i&gt;<\/i>",
                "fillHorizontal": "تعبئة الحقول أفقيًا",
                "fillVertical": "تعبئة الحقول عموديا"
            },
            "searchBuilder": {
                "add": "اضافة شرط",
                "clearAll": "ازالة الكل",
                "condition": "الشرط",
                "data": "المعلومة",
                "logicAnd": "و",
                "logicOr": "أو",
                "title": [
                    "منشئ البحث"
                ],
                "value": "القيمة",
                "conditions": {
                    "date": {
                        "after": "بعد",
                        "before": "قبل",
                        "between": "بين",
                        "empty": "فارغ",
                        "equals": "تساوي",
                        "not": "ليس",
                        "notBetween": "ليست بين",
                        "notEmpty": "ليست فارغة"
                    },
                    "number": {
                        "between": "بين",
                        "empty": "فارغة",
                        "equals": "تساوي",
                        "gt": "أكبر من",
                        "gte": "أكبر وتساوي",
                        "lt": "أقل من",
                        "lte": "أقل وتساوي",
                        "not": "ليست",
                        "notBetween": "ليست بين",
                        "notEmpty": "ليست فارغة"
                    },
                    "string": {
                        "contains": "يحتوي",
                        "empty": "فاغ",
                        "endsWith": "ينتهي ب",
                        "equals": "يساوي",
                        "not": "ليست",
                        "notEmpty": "ليست فارغة",
                        "startsWith": " تبدأ بـ "
                    }
                },
                "button": {
                    "0": "فلاتر البحث",
                    "_": "فلاتر البحث (%d)"
                },
                "deleteTitle": "حذف فلاتر"
            },
            "searchPanes": {
                "clearMessage": "ازالة الكل",
                "collapse": {
                    "0": "بحث",
                    "_": "بحث (%d)"
                },
                "count": "عدد",
                "countFiltered": "عدد المفلتر",
                "loadMessage": "جارِ التحميل ...",
                "title": "الفلاتر النشطة"
            },
            "infoThousands": ",",
            "datetime": {
                "previous": "السابق",
                "next": "التالي",
                "hours": "الساعة",
                "minutes": "الدقيقة",
                "seconds": "الثانية",
                "unknown": "-",
                "amPm": [
                    "صباحا",
                    "مساءا"
                ],
                "weekdays": [
                    "الأحد",
                    "الإثنين",
                    "الثلاثاء",
                    "الأربعاء",
                    "الخميس",
                    "الجمعة",
                    "السبت"
                ],
                "months": [
                    "يناير",
                    "فبراير",
                    "مارس",
                    "أبريل",
                    "مايو",
                    "يونيو",
                    "يوليو",
                    "أغسطس",
                    "سبتمبر",
                    "أكتوبر",
                    "نوفمبر",
                    "ديسمبر"
                ]
            },
            "editor": {
                "close": "إغلاق",
                "create": {
                    "button": "إضافة",
                    "title": "إضافة جديدة",
                    "submit": "إرسال"
                },
                "edit": {
                    "button": "تعديل",
                    "title": "تعديل السجل",
                    "submit": "تحديث"
                },
                "remove": {
                    "button": "حذف",
                    "title": "حذف",
                    "submit": "حذف",
                    "confirm": {
                        "_": "هل أنت متأكد من رغبتك في حذف السجلات %d المحددة؟",
                        "1": "هل أنت متأكد من رغبتك في حذف السجل؟"
                    }
                },
                "error": {
                    "system": "حدث خطأ ما"
                },
                "multi": {
                    "title": "قيم متعدية",
                    "restore": "تراجع"
                }
            },
            "processing": "يتم التحميل...",
            "emptyTable": "لا يوجد بيانات متاحة في الجدول",
            "infoEmpty": "يعرض 0 إلى 0 من أصل 0 مُدخل",
            "thousands": "."
        }  '));
        })->name('dataTableTranslation');

    });

    // system routes
    Route::post('restart-system', function () {
        auth()->logout();
        Artisan::call('system:restart');
        return redirect()->back()->with(['success' => 'تم تهيئة النظام بنجاح']);
    })->name('system.restart');
});
