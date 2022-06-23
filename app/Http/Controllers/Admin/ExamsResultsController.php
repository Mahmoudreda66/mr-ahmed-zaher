<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Level;
use App\Models\Admin\Student;
use App\Models\Admin\LessonsGroups;
use App\Models\Exams\ExamsResults;
use App\Models\Exams\ExamsEnterAttemps;
use App\Models\Exams\Exam;
use Illuminate\Support\Facades\Validator;
use App\Rules\numberOlderThanNumber;

class ExamsResultsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:exams-marks');
        $this->middleware('permission:delete-exam-mark')->only('destroy');
        $this->middleware('permission:edit-exam-mark')->only('update');
        $this->middleware('permission:exams-manual-marks')->only(['create', 'store']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $allowed_levels = [];
        $levels = Level::get();

        foreach($levels as $level){
            $allowed_levels[] = $level->id;
        }

        if($request->has('level') && in_array($request->level, $allowed_levels)){
            $levelData = Level::find($request->level);

            $arguments = ['results', 'levelData', 'levels'];

            if($request->has('exam')){
                $exam = Exam::findOrFail($request->exam);
                $arguments[] = 'exam';

                $results = ExamsResults::whereHas('exam', function ($q) use ($levelData) {
                    $q->where('level_id', $levelData->id);
                })
                ->where('exam_id', $request->exam)
                ->orderBy('id', 'DESC')
                ->paginate(30);
            }else{
                $results = ExamsResults::whereHas('exam', function ($q) use ($levelData) {
                    $q->where('level_id', $levelData->id);
                })
                ->orderBy('id', 'DESC')
                ->paginate(30);
            }

            return view('admin.exams.results.index', compact(...$arguments));
        }

        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $allowed_levels = [];
        $levels = Level::get();

        foreach($levels as $level){
            $allowed_levels[] = $level->id;
            if($request->level == $level->id){
                $levelData = $level;
            }
        }

        if(isset($request->level) && in_array($request->level, $allowed_levels)){

            $arguments = [];

            if(isset($request->exam) && isset($request->group)){
                $exam = Exam::findOrFail($request->exam);
                $group = LessonsGroups::findOrFail($request->group);
                $students = $group->students;
                array_push($arguments, 'exam', 'group', 'students');
            }

            array_push($arguments, 'levels', 'levelData');

            return view('admin.exams.results.manual', compact(...$arguments));
        }

        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'exam' => 'required|numeric',
            'marksContainer' => 'required|json'
        ], [
            'exam.required' => 'قم بإختيار الإختبار المطلوب',
            'exam.numeric' => 'لقد حدث خطأ في تعريف الإختبار',
            'marksContainer.required' => 'الدرجات مطلوبة',
            'marksContainer.array' => 'صيغة الدرجات غير صحيحة',
        ]);

        if($validation->fails()){
            return redirect()->back()->withErrors($validation);
        }

        $exam = Exam::findOrFail($request->exam);

        foreach (json_decode($request->marksContainer, true) as $value) {
            $studentId = $value['id'];
            $student = Student::find($studentId);
            $mark = $value['mark'];
            $fullmark = $value['fullmark'];
            $errors = [];

            if(
                empty($mark) ||
                !is_numeric($mark) ||
                empty($fullmark) ||
                !is_numeric($fullmark) ||
                empty($studentId) ||
                !is_numeric($studentId)
            ){
                return redirect()->back()->with('error', 'هناك حقول غير مملوئة');
            }else{
                if($mark > $fullmark){
                    return redirect()->back()->with('error', 'يوجد درجة أكبر من الدرجة النهائية');
                }
            }

            if(!$student){
                return redirect()->back()->with('error', 'لم يتم العثور على الطالب');
            }

            $attemp = ExamsEnterAttemps::updateOrCreate([
                'student_id' => $studentId,
                'exam_id' => $request->exam,
                'enter_type' => 1
            ]);

            ExamsResults::updateOrCreate([
                'student_id' => $studentId,
                'exam_id' => $request->exam,
            ], [
                'student_id' => $studentId,
                'exam_id' => $request->exam,
                'mark' => [
                    'full_mark' => $fullmark,
                    'correct_answers' => $mark,
                    'wrong_answers' => $fullmark - $mark
                ],
                'exams_enter_attemps_id' => $attemp->id
            ]);
        }

        return redirect()->back()->with('success', 'تم حفظ الدرجات بنجاح');
    }

    public function store_single(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'student' => 'required|numeric',
            'exam' => 'required|numeric',
            'full_mark' => 'required|numeric|max:500|min:1',
            'mark' => ['required', 'numeric', new numberOlderThanNumber($request->full_mark)]
        ], [
            'student.required' => 'لم يتم العثور على الطالب',
            'student.numeric' => 'لقد حدث خطأ في معرف الطالب',
            'exam.required' => 'قم بإختيار الإختبار المطلوب',
            'exam.numeric' => 'لقد حدث خطأ في تعريف الإختبار',
            'full_mark.required' => 'قم بكتابة الدرجة الكلية',
            'full_mark.numeric' => 'يجب أن تتكون الدرجة الكلية من أرقام',
            'full_mark.max' => 'أقصى عدد للدرجة الكلية هو 500',
            'full_mark.min' => 'أقل عدد للدرجة الكلية هو 1',
            'mark.required' => 'قم بكتابة الدرجة',
            'mark.numeric' => 'يجب أن تتكون الدرجة من أرقام',
        ]);

        if($validation->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation',
                'data' => $validation->errors()->all()
            ]);
        }

        $student = Student::find($request->student);
        $exam = Exam::find($request->exam);

        if(!$student){
            return response()->json([
                'status' => false,
                'message' => '404',
                'data' => ['لم يتم العثور على الطالب']
            ]);
        }

        if(!$exam){
            return response()->json([
                'status' => false,
                'message' => '404',
                'data' => ['لم يتم العثور على الإختبار']
            ]);
        }

        $attemp = ExamsEnterAttemps::updateOrCreate([
            'student_id' => $request->student,
            'exam_id' => $request->exam,
            'enter_type' => isset($request->enter_type) && $request->enter_type == 0 ? 0 : 1
        ]);

        ExamsResults::updateOrCreate([
            'student_id' => $request->student,
            'exam_id' => $request->exam,
        ], [
            'student_id' => $request->student,
            'exam_id' => $request->exam,
            'mark' => [
                'full_mark' => $request->full_mark,
                'correct_answers' => $request->mark,
                'wrong_answers' => $request->full_mark - $request->mark
            ],
            'exams_enter_attemps_id' => $attemp->id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => []
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $result = ExamsResults::findOrFail($id);

        $validation = Validator::make($request->all(), [
            'full_mark' => 'required|numeric|max:500|min:1',
            'correct_answers' => ['required', 'numeric', new numberOlderThanNumber($request->full_mark)]
        ], [
            'full_mark.required' => 'قم بكتابة الدرجة الكلية',
            'full_mark.numeric' => 'يجب أن تتكون الدرجة الكلية من أرقام',
            'full_mark.max' => 'أقصى عدد للدرجة الكلية هو 500',
            'full_mark.min' => 'أقل عدد للدرجة الكلية هو 1',
            'correct_answers.required' => 'قم بكتابة الدرجة',
            'correct_answers.numeric' => 'يجب أن تتكون الدرجة من أرقام',
        ]);

        if($validation->fails()){
            return redirect()->back()->withErrors($validation)->withInput()->with(['id' => $id]);
        }

        $mark = [
            'full_mark' => $request->full_mark,
            'correct_answers' => $request->correct_answers,
            'wrong_answers' => $request->full_mark - $request->correct_answers
        ];

        $result->update(['mark' => $mark]);

        return redirect()->back()->with(['success' => 'تم تحديث الدرجة بنجاح']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ExamsResults::findOrFail($id)->delete();

        return redirect()->back()->with(['success' => 'تم حذف الدرجة بنجاح']);
    }

    public function print($id)
    {
        $exam = Exam::findOrFail($id);

        $attemps = $exam->attemps;

        return view('admin.exams.results.print', compact('attemps'));
    }
}
