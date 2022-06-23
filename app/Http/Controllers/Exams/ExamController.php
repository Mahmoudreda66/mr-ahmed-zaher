<?php

namespace App\Http\Controllers\Exams;

use App\Http\Controllers\Controller;
use App\Models\Exams\Exam;
use App\Models\Admin\Student;
use App\Models\Exams\ExamQuestion;
use App\Models\Exams\ExamSection;
use App\Models\Exams\ExamsAnswers;
use App\Models\Exams\ExamsEnterAttemps;
use Illuminate\Http\Request;
use App\Models\Exams\ExamsResults;
use App\Models\Admin\Settings;

class ExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('examsAuth')->except('top10_students');
    }

    public function index(Request $request)
    {
        $exams = Exam::where([
            ['level_id', auth('students')->user()->level_id],
            ['exam_type', 0]
        ])
        ->whereNotNull('status')
        ->orderBy('id', 'DESC')
        ->get();
        
        return view('exams.index', compact('exams'));
    }

    public function show_exam($id, Request $request)
    {
        $exam = Exam::findorFail($id);

        if ($exam->level_id != auth('students')->user()->level_id || !$exam->status || $exam->exam_type == 1) {
            abort(404);
        }

        foreach($exam->attemps as $attemp){
            if($attemp->student_id == auth('students')->user()->id){
                return redirect()->to(route('students.exams.index'))->with(['error' => 'تم دخول الإختبار من قبل']);
            }
        }

        return view('exams.show_exam', compact('exam'));
    }

    public function show_result($id)
    {
        $exam = Exam::findOrFail($id);

        $checkIfEntered = ExamsEnterAttemps::where([
            ['student_id', auth('students')->user()->id],
            ['exam_id', $id]
        ])->first();

        if(!$checkIfEntered){
            abort(403);
        }

        $examResult = ExamsResults::where([
            ['exam_id', $id],
            ['student_id', auth('students')->user()->id]
        ])->first();

        if(!$examResult){
            return redirect()->back()->with(['error' => 'لا يوجد إجابات لهذا الإختبار']);
        }

        $attemp = ExamsEnterAttemps::where([
            ['exam_id', $id],
            ['student_id', auth('students')->user()->id]
        ])->first();

        $showAnswers = Settings::where('name', 'show_answers_after_exam_ends')->select('value')->first()['value'];

        // get student sort
        $studentResult = $examResult->mark['correct_answers'];
        $examMarks = ExamsResults::where('exam_id', $exam->id)->select('mark')->get();
        $allMarks = [];

        foreach($examMarks as $mark){ $allMarks[] = $mark->mark['correct_answers']; }

        $studentTops    = [];
        $arabicNumber   = null;
        $isRepeated     = null;

        foreach($allMarks as $mark){
            if($mark > $studentResult){ $studentTops[] = $mark; }
        }

        $topCount = count(array_unique($studentTops));

        switch ($topCount) {
            case 0:
                $arabicNumber = 'الأول';
                break;
            case 1:
                $arabicNumber = 'الثاني';
                break;
            case 2:
                $arabicNumber = 'الثالث';
                break;
            case 3:
                $arabicNumber = 'الرابع';
                break;
            case 4:
                $arabicNumber = 'الخامس';
                break;
            case 5:
                $arabicNumber = 'السادس';
                break;
            case 6:
                $arabicNumber = 'السابع';
                break;
            case 7:
                $arabicNumber = 'الثامن';
                break;
            case 8:
                $arabicNumber = 'التاسع';
                break;
            case 9:
                $arabicNumber = 'العاشر';
                break;
            default:
                $arabicNumber = null;
                break;
        }

        $arguments = [
            'exam',
            'examResult',
            'attemp',
            'showAnswers',
            'arabicNumber',
            'topCount'
        ];

        return view('exams.results.show_result', compact(...$arguments));
    }

    public function top10_students($id)
    {
        $exam = Exam::findOrFail($id);
        $allMarks = ExamsResults::where('exam_id', $exam->id)
        ->select('student_id', 'mark')
        ->get();
        $sorted = [];

        foreach($allMarks as $mark){
            $sorted[$mark->student->name] = $mark->mark['correct_answers'] * 1;
        }

        asort($sorted);

        $all_sorted_marks = array_reverse($sorted);

        $top_10_students = array_slice(array_unique($all_sorted_marks), 0, 10);

        $students_final = [];

        foreach($top_10_students as $key => $value){
            $items = array_keys($all_sorted_marks, $value);
            foreach($items as $item){
                $students_final[$item] = $sorted[$item];
            }
        }

        return response()->json([
            'status'    => true,
            'data'      => $students_final
        ]);
    }

    public function student_enter($exam) {
        $examData = Exam::findOrFail($exam);
        Student::findOrFail(auth('students')->user()->id);

        if(!$examData->status){
            return response()->json(['status' => false, 'message' => 'لا يمكن الدخول للإختبار حيث تم إنتهاء الوقت المحدد']);
        }

        $checkIfEntered = ExamsEnterAttemps::where([
            ['student_id', auth('students')->user()->id],
            ['exam_id', $exam]
        ])->first();

        if($checkIfEntered){
            return response()->json(['status' => false, 'message' => 'تم الدخول للإختبار من قبل']);
        }

        ExamsEnterAttemps::create([
            'student_id' => auth('students')->user()->id,
            'exam_id' => $exam,
            'enter_type' => 0
        ]);


        return response()->json(['status' => true]);
    }

    public function results_index()
    {
        $exams = ExamsEnterAttemps::with('exam', 'result')
        ->where([
            ['student_id', auth('students')->user()->id]
        ])
        ->orderBy('id', 'DESC')
        ->with('exam.teacher', 'exam.subject')
        ->get();

        return view('exams.results.index', compact('exams'));
    }
}
