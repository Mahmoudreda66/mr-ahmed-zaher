<?php

namespace App\Http\Controllers\Exams;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exams\ExamsCorrecting;
use Illuminate\Support\Facades\Validator;

class ExamsCorrectingController extends Controller
{
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
        $validator = Validator::make($request->all(), [
            'student' => 'required|numeric|exists:students,id',
            'question_id' => 'required|numeric|exists:exams_questions,id',
            'question_answer' => 'required|boolean'
        ], [
            'student.required' => 'معرف الطالب مطلوب',
            'student.numeric' => 'لقد حدث خطأ في معرف الطالب',
            'student.exists' => 'لم يتم العثور على الطالب',
            'question_id.required' => 'معرف السؤال مطلوب',
            'question_id.numeric' => 'لقد حدث خطأ في معرف السؤال',
            'question_id.exists' => 'لم يتم العثور على السؤال',
            'question_answer.required' => 'قم بإختيار حالة الإجابة',
            'question_answer.boolean' => 'لقد حدث خطأ في حالة الإجابة',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation',
                'data' => $validator->errors()->all()
            ]);
        }

        $questionAnswer = $request->question_answer == 0 ? 'false' : 'true';
        $questionComment = $request->comment ?? '';

        $answer = ExamsCorrecting::updateOrCreate([
            'student_id' => $request->student,
            'exams_question_id' => $request->question_id
        ], [
            'student_id' => $request->student,
            'exams_question_id' => $request->question_id,
            'body' => [
                'status' => $questionAnswer,
                'comment' => $questionComment
            ]
        ]);

        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $answer
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
