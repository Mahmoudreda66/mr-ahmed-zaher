<?php

namespace App\Http\Controllers\Exams;

use App\Http\Controllers\Controller;
use App\Models\Exams\ExamQuestion;
use App\Models\Exams\ExamsEnterAttemps;
use App\Models\Exams\Exam;
use App\Models\Admin\Level;
use App\Models\Admin\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamQuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:exams-manual-marks');
    }

    public function response($status, $message, $data)
    {
        return ['status' => $status, 'message' => $message, 'data' => $data];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'section' => 'required|numeric',
            'type' => 'required|numeric|in:0,1,2,3'
        ], [
            'exam.required' => 'حدث خطأ في تعريف الإختبار',
            'exam.numeric' => 'حدث خطأ في تعريف الإختبار',
            'section.required' => 'لقد حدث خطأ في تعريف االقسم',
            'type.required' => 'قم بإختيار نوع الإختبار',
            'type.required' => 'قم بإختيار نوع الإختبار بشكل صحيح',
            'type.in' => 'قم بإختيار نوع الإختبار بشكل صحيح'
        ]);

        if($validation->fails()){
            return response()->json($this->response(false, 'validation', $validation->errors()->all()));
        }

        $exam = $request->exam;
        $section = $request->section;
        $questionType = $request->type;

        if($questionType == 0){ // choose question

            $questionText = $request->question;
            $body = json_decode($request->body, true);
            $answer = $request->answer;

            if(strlen($questionText) > 255 || strlen($questionText) <= 3){
                return response()->json($this->response(false, 'validation', ['يجب ألا يزيد السؤال عن 255 حرف ولا يقل عن 3 أحرف']));
            }

            if(count($body) > 5){
                return response()->json($this->response(false, 'validation', ['لا يمكن أن يوجد أكثر من 5 إختيارات']));
            }else if(count($body) < 2){
                return response()->json($this->response(false, 'validation', ['يجب أن يتواجد إختيارين على الأقل بالسؤال']));
            }

            if($answer === "null"){
                return response()->json($this->response(false, 'validation', ['قم بإختيار الإجابة الصحيحة']));
            }

            foreach($body as $index => $option){
                if(strlen($option) > 100 || strlen($option) < 1){
                    return response()->json($this->response(false, 'validation', ['يجب ألا يزيد الإختيار عن 100 حرف ولا يقل عن  حرف واحد بالإختيار رقم ' . $index + 1]));
                }
            }

            $rowBody = [
                'question' => $questionText,
                'options' => $body
            ];

        }else if($questionType == 1){ // long answer question
            
            $smallValidation = Validator::make($request->all(), [
                'body' => 'min:3|max:255',
                'addEditor' => 'boolean'
            ], [
                'body.min' => 'يجب ألا يقل السؤال عن 3 أحرف',
                'body.max' => 'يجب ألا يزيد السؤال عن 255 حرف',
                'addEditor.boolean' => 'قم بإختيار حالة محرر النصوص بشكل صحيح'
            ]);

            if($smallValidation->fails()){
                return response()->json($this->response(false, 'validation', $smallValidation->errors()->all()));
            }

            $question = $request->body;
            $addEditor = $request->addEditor;

            $rowBody = [
                'question' => $question,
                'addEditor' => $addEditor
            ];

        }else if($questionType == 2){ // short answer question
            
            $smallValidation = Validator::make($request->all(), [
                'body' => 'min:3|max:255'
            ], [
                'body.min' => 'يجب ألا يقل السؤال عن 3 أحرف',
                'body.max' => 'يجب ألا يزيد السؤال عن 255 حرف'
            ]);

            if($smallValidation->fails()){
                return response()->json($this->response(false, 'validation', $smallValidation->errors()->all()));
            }

            $question = $request->body;

            $rowBody = [
                'question' => $question
            ];

        }else if($questionType == 3){ // t&f question
            
            $smallValidation = Validator::make($request->all(), [
                'body' => 'min:3|max:255',
                'answer' => 'boolean'
            ], [
                'body.min' => 'يجب ألا يقل السؤال عن 3 أحرف',
                'body.max' => 'يجب ألا يزيد السؤال عن 255 حرف',
                'answer.boolean' => 'قم بإختيار الإجابة الصحيحة'
            ]);

            if($smallValidation->fails()){
                return response()->json($this->response(false, 'validation', $smallValidation->errors()->all()));
            }

            $question = $request->body;
            $answer = $request->answer;

            $rowBody = [
                'question' => $question
            ];

        }

        $question = ExamQuestion::create([
            'exam_id' => $exam,
            'exam_section_id' => $section,
            'body' => $rowBody,
            'answer' => $request->answer === 'null' ? null : $request->answer ?? null,
            'type' => $questionType,
        ]);

        return response()->json($this->response(true, 'success', $question));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\ExamQuestion  $examQuestion
     * @return \Illuminate\Http\Response
     */
    public function show(ExamQuestion $examQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\ExamQuestion  $examQuestion
     * @return \Illuminate\Http\Response
     */
    public function edit(ExamQuestion $examQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\ExamQuestion  $examQuestion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $question = ExamQuestion::findOrFail($request->question_id);

        if(strlen($request->new_text) > 255 || strlen($request->new_text) <= 3){
            return response()->json($this->response(false, 'validation', ['messages' => ['يجب ألا يزيد السؤال عن 255 حرف ولا يقل عن 3 أحرف'], 'old_text' => $question->body['question']]));
        }

        $question->update([
            'body->question' => $request->new_text
        ]);

        return response()->json($this->response(true, 'success', $question));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\ExamQuestion  $examQuestion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        ExamQuestion::findOrFail($id)->delete();
        return response()->json($this->response(true, 'success', []));
    }

    public function correcting_index(Request $request)
    {
        $allowed_levels = [];
        $levels = Level::get();

        foreach($levels as $level){
            $allowed_levels[] = $level->id;
        }

        if($request->has('level') && in_array($request->level, $allowed_levels)){
            $levelData = null;
            
            foreach($levels as $level){
                $level->id == $request->level ? $levelData = $level : '';
            }

            $arguments = [
                'levels',
                'levelData'
            ];

            if($request->has('exam')){
                $exam = Exam::where([
                    ['id', $request->exam],
                    ['level_id', $request->level]
                ])->first();

                if($exam){
                    array_push($arguments, 'exam');

                    if((auth()->user()->hasRole('teacher') && auth()->user()->teacher->id == $exam->teacher_id) || !auth()->user()->hasRole('teacher')){
                        if(!$request->has('student')){
                            $attemp = ExamsEnterAttemps::where('exam_id', $exam->id)->select('student_id')->first();

                            if($attemp){
                                return redirect()->to(route('exams-correcting', [
                                    'level' => $request->level,
                                    'exam' => $request->exam,
                                    'student' => $attemp->student_id
                                ]));
                            }
                        }else{
                            $attemps = ExamsEnterAttemps::where([
                                ['exam_id', $request->exam]
                            ])->get();

                            $students = [];

                            foreach($attemps as $attemp){
                                $students[] = ['name' => $attemp->student->name, 'id' => $attemp->student->id];
                            }

                            $student = Student::where([
                                ['id', $request->student],
                                ['level_id', $request->level]
                            ])->whereHas('attemps', function ($q) use ($request) {
                                $q->where('student_id', $request->student);
                            })->first();

                            !$student ? abort(404) : '';

                            array_push($arguments, 'student', 'attemps');
                        }
                    }else{    
                        return redirect()->back()->with('error', 'يجب تصحيح الإختبارات الخاصة بك فقط');
                    }
                }else{
                    abort(404);
                }
            }

            return view('admin.exams.correcting', compact(...$arguments));
        }

        abort(404);
    }

    public function add_choice(Request $request)
    {
        $question           = ExamQuestion::findOrFail($request->question_id);
        $newChoice          = $request->new_choice;
        $all_choices        = $question->body['options'];

        $all_choices[]      = $newChoice;

        if(count($all_choices) > 5){
            return response()->json($this->response(false, 'validation', ['messages' => ['لا يمكن أن يتواجد أكثر من 5 إختيارات بالسؤال الواحد']]));
        }

        $question->update([
            'body->options' => $all_choices
        ]);

        return response()->json($this->response(true, 'success', $question));
    }

    public function edit_choice(Request $request)
    {
        $question = ExamQuestion::findOrFail($request->question_id);
        $option_index = $request->index;
        $option = $request->new_text;

        if($option_index > 5 || !is_numeric($option_index)){
            return response()->json($this->response(false, 'validation', ['messages' => ['لقد حدث خطأ في ترتيب السؤال']]));
        }

        if(strlen($option) > 100 || strlen($option) < 1){
            return response()->json($this->response(false, 'validation', ['messages' => ['يجب ألا يزيد الإختيار عن 100 حرف ولا يقل عن  حرف واحد '], 'old_text' => $question->body['options'][$option_index]]));
        }

        $question->update([
            'body->options->' . $option_index => $option
        ]);

        return response()->json($this->response(true, 'success', $question));
    }

    public function delete_choice(Request $request)
    {
        $question = ExamQuestion::findOrFail($request->question_id);
        $option_index = $request->index;

        if($option_index > 5 || !is_numeric($option_index)){
            return response()->json($this->response(false, 'validation', ['لقد حدث خطأ في ترتيب السؤال']));
        }

        $options_array = $question->body['options'];
        unset($options_array[$option_index]);

        if(count($options_array) <= 1){
            return response()->json($this->response(false, 'validation', ['يجب أن يحتوي السؤال على إختيارين على الأقل']));
        }

        $question->update([
            'body->options' => $options_array
        ]);

        return response()->json($this->response(true, 'success', $question));
    }
}
