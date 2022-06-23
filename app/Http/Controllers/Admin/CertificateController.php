<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\Level;
use App\Models\Admin\Student;
use App\Models\Exams\Exam;
use App\Models\Admin\Settings;

class CertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:certificates');
        $this->middleware('permission:empty-marks-certificate')->only(['empty_marks_certificate', 'students_empty_marks_certificate']);
        $this->middleware('permission:filled-marks-certificate')->only('filled_marks_certificate');
    }

    public function empty_marks_certificate()
    {
        return view('admin.certificates.empty_marks_certificate');
    }

    public function filled_marks_certificate(Request $request)
    {
        Level::findOrFail($request->level);

        $exams = Exam::where('level_id', $request->level)
        ->with('teacher', 'subject', 'level')
        ->get();

        return view('admin.certificates.filled_marks_certificate', compact('exams'));
    }

    public function filled_marks_certificate_stamp(Request $request)
    {
        Level::findOrFail($request->level);

        $validation = Validator::make($request->all(), [
            'exams' => 'required|array|min:1'
        ], [
            'exams.required' => 'قم بإختيار الإختبارات',
            'exams.array' => 'يجب أن تكون الإختبارات على هيئة مصفوفة',
            'exams.min' => 'قم بإختيار إختبار واحد على الأقل'
        ]);

        if($validation->fails()){
            return redirect()->back()->withErrors($validation);
        }

        $exams_ids = $request->exams;
        $students = Student::where('level_id', $request->level)
        ->with('level')
        ->get();

        foreach($exams_ids as $exam){
            $item = Exam::where('id', $exam)
            ->with('subject', 'teacher.profile', 'level')
            ->first();

            if($item){
                $exams_container[] = $item;
            }else{
                return redirect()->back()->with('error', 'لم يتم العثور على الإختبار ' . $exam);
            }
        }

        $logo = Settings::where('name', 'center_logo')->first()['value'];
        $image = empty($logo) ? 'smart center logo.png' : $logo;
        $phone = Settings::where('name', 'center_phone1')->first()['value'];

        $arguments = [
            'students',
            'exams_container',
            'logo',
            'image',
            'phone'
        ];

        return view('admin.solid_pages.filled_marks_certificate', compact(...$arguments));
    }

    public function students_empty_marks_certificate(Request $request)
    {
        Level::findOrFail($request->level);
        $students = Student::orderBy('name', 'ASC')
        ->where('level_id', $request->level)
        ->with('level')
        ->get();

        return view('admin.solid_pages.students_empty_marks_certificate', compact('students'));
    }
}
