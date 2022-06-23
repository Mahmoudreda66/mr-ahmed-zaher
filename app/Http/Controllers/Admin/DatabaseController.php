<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use App\Models\Admin\Teacher;
use App\Models\Admin\Student;
use App\Models\Admin\StudentTeachers;
use App\Models\Admin\LessonsGroupsStudent;
use App\Models\Admin\Expenses;
use App\Models\Exams\Exam;
use App\Models\Exams\ExamQuestion;
use App\Models\Exams\ExamsAnswers;
use App\Models\Exams\ExamsCorrecting;
use App\Models\Exams\ExamSection;
use App\Models\Exams\ExamsEnterAttemps;
use App\Models\Exams\ExamsResults;
use App\Models\User;
use Exception;
use PDOException;
use ValueError;

class DatabaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:database-management');
    }

    public function index()
    {
        $allBackups = Storage::allFiles('backups');

        return view('admin.database.index', compact('allBackups'));
    }

    public function truncate_many(Request $request)
    {
        $request = $request->except('_token');

        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        foreach ($request as $table) {
            if (Schema::hasTable($table)) {
                if ($table === 'students') {
                    Student::truncate();
                    Expenses::truncate();
                    StudentTeachers::truncate();
                    LessonsGroupsStudent::truncate();

                    if (config('database.default') === 'pgsql') {
                        $statement = "ALTER SEQUENCE students_id_seq RESTART WITH 1000;";
                    } else {
                        $statement = "ALTER TABLE students AUTO_INCREMENT = 1000;";
                    }

                    DB::unprepared($statement);
                } else if ($table == 'expenses') {
                    Expenses::truncate();

                    if (config('database.default') === 'pgsql') {
                        $statement = "ALTER SEQUENCE expenses_id_seq RESTART WITH 1000;";
                    } else {
                        $statement = "ALTER TABLE expenses AUTO_INCREMENT = 1000;";
                    }

                    DB::unprepared($statement);
                } else if ($table === 'teachers') {
                    $teachers = Teacher::get();
                    foreach ($teachers as $teacher) {
                        $user = User::where('id', $teacher->profile_id);
                        if ($user) {
                            $user->delete();
                        }
                        $teacher->delete();
                    }
                } else if ($table === 'exams') {
                    Exam::truncate();
                    ExamsAnswers::truncate();
                    ExamsCorrecting::truncate();
                    ExamsEnterAttemps::truncate();
                    ExamQuestion::truncate();
                    ExamSection::truncate();
                    ExamsResults::truncate();
                } else {
                    DB::table($table)->truncate();
                }
            } else {
                return redirect()->back()->with(['error' => 'لم يتم العثور على الجداول ' . $table]);
            }
        }

        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        return redirect()->back()->with(['success' => 'تم تفريغ الجداول بنجاح']);
    }

    public function upload_backup(Request $request)
    {
        $backup_name = $request->backup_file;

        if (!empty($backup_name)) {
            if (Storage::has($backup_name)) {
                try {
                    DB::unprepared(file_get_contents(storage_path('app/') . $backup_name));
                } catch (ValueError $e) {
                    return redirect()->back()->with(['error' => 'نسخة إحتياطية تالفة']);
                }

                return redirect()->back()->with(['success' => 'تم إستعادة النسخة الإحتياطية بنجاح']);
            }

            return redirect()->back()->with(['error' => 'لم يتم العثور على الملف الخاص بالنسخة الإحتياطية']);
        }

        return redirect()->back()->with(['error' => 'قم بإختيار النسخة أولاً']);
    }

    public function take_backup()
    {
        Artisan::call('db:backup');

        return redirect()->back()->with(['success' => 'تم أخذ النسخة  الإحتياطية بنجاح']);
    }

    public function delete_all_backups(Request $request)
    {
        $allBackups = Storage::allFiles('backups');

        foreach($allBackups as $backup){
            Storage::delete($backup);
        }

        return redirect()->back()->with('success', 'تم حذف النسخ الإحتياطية بنجاح');
    }
}
