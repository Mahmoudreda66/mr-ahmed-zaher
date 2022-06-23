<?php

namespace App\Http\Controllers\Exams;

use App\Http\Controllers\Controller;
use App\Models\Exams\ExamSection;
use App\Models\Exams\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamSectionController extends Controller
{
    public function response($status, $data)
    {
        return ['status' => $status, 'data' => $data];
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
            'title' => 'required|max:255|min:3',
            'dir' => 'required|in:rtl,ltr'
        ], [
            'exam.required' => 'لقد حدث خطأ في تعريف الإختبار',
            'exam.numeric' => 'لقد حدث خطأ في تعريف الإختبار',
            'title.required' => 'قم بكتابة عنوان القسم',
            'title.max' => 'أقصى عدد أحرف للعنوان هو 255 حرف',
            'title.min' => 'أقل عدد أحرف  مسموح  بها هو 3 أحرف',
            'dir.required' => 'قم بإختيار إتجاه القسم',
            'dir.in' => 'قم بإختيار إتجاه القسم بشكل صحيح',
        ]);

        if($validation->fails()){
            return response()->json($this->response(false, $validation->errors()->all()));
        }

        $exam = Exam::findOrFail($request->exam);

        $section = ExamSection::create([
            'exam_id' => $request->exam,
            'title' => $request->title,
            'description' => empty($request->description) ? null : $request->description,
            'dir' => $request->dir
        ]);

        return response()->json($this->response(true, [$section, $exam->sections->count()]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\ExamSection  $examSection
     * @return \Illuminate\Http\Response
     */
    public function show(ExamSection $examSection)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\ExamSection  $examSection
     * @return \Illuminate\Http\Response
     */
    public function edit(ExamSection $examSection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\ExamSection  $examSection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $section = ExamSection::findOrFail($id);

        $validation = Validator::make($request->all(), [
            'title' => 'required|max:255|min:3',
            'dir' => 'required|in:rtl,ltr'
        ], [
            'title.required' => 'قم بكتابة عنوان القسم',
            'title.max' => 'أقصى عدد أحرف للعنوان هو 255 حرف',
            'title.min' => 'أقل عدد أحرف  مسموح  بها هو 3 أحرف',
            'dir.required' => 'قم بإختيار إتجاه القسم',
            'dir.in' => 'قم بإختيار إتجاه القسم بشكل صحيح',
        ]);

        if($validation->fails()){
            return response()->json($this->response(false, $validation->errors()->all()));
        }

        $updatedSection = $section->update($request->only('title', 'description', 'dir'));

        return response()->json($this->response(true, $section));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\ExamSection  $examSection
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        ExamSection::findOrFail($request->id)->delete();

        return redirect()->back();
    }
}
