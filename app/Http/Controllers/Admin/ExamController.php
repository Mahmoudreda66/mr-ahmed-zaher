<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exams\Exam;
use App\Models\Exams\ExamQuestion;
use App\Models\Admin\Teacher;
use App\Models\Exams\ExamsAnswers;
use App\Models\Exams\ExamsCorrecting;
use App\Models\Exams\ExamSection;
use App\Models\Exams\ExamsEnterAttemps;
use App\Models\Exams\ExamsResults;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\DataTables\ExamDataTable;

class ExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:exams');
        $this->middleware('permission:show-exams')->only('index');
        $this->middleware('permission:add-exam')->only(['create', 'store']);
        $this->middleware('permission:view-exam')->only(['show', 'view']);
        $this->middleware('permission:edit-exam')->only('update');
        $this->middleware('permission:delete-exam')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ExamDataTable $table)
    {
        $table->isTeacher(auth()->user()->hasRole('teacher'));
        return $table->render('admin.exams.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $teachers = Teacher::with('profile')->select('profile_id', 'id', 'subject_id')->get();
        return view('admin.exams.create', compact('teachers'));
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
            'subject' => 'required|numeric',
            'teacher' => 'required|numeric',
            'level' => 'required|numeric',
            'date' => 'required|date',
            'exam_type' => 'boolean',
            'duration' => 'required|numeric|max:300|min:1'
        ], [
            'subject.required' => '?????? ?????? ?????? ???? ???????????? ????????????  ??????????????',
            'subject.numeric' => '?????? ?????? ?????? ???? ???????????? ????????????  ??????????????',
            'teacher.required' => '???? ?????????????? ????????????',
            'teacher.numeric' => '?????? ?????? ?????? ???? ???????????? ????????????',
            'level.required' => '???? ?????????????? ??????????????',
            'level.numeric' => '?????? ?????? ?????? ?????????? ???????????? ??????????????',
            'date.required' => '???? ?????????????? ??????????????',
            'date.date' => '???? ?????????????? ?????????????? ???????? ????????',
            'duration.required' => '???? ?????????????? ?????????? ??????????????',
            'duration.numeric' => '?????? ???? ?????????? ?????????? ???? ??????????',
            'duration.max' => '?????? ?????? ???????? ?????????? ???? 5 ??????????',
            'duration.min' => '?????? ?????? ?????????? ???? ???? 1 ??????????',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation);
        }

        $check = Exam::where([
            ['subject_id', $request->subject],
            ['teacher_id', $request->teacher],
            ['level_id', $request->level],
            ['date', $request->date],
            ['duration', $request->duration]
        ])->first();

        if ($check) {
            return redirect()->back()->with(['error' => '???????? ???????????? ???????? ???????????????? ????????????'])->withInput();
        }

        $exam = Exam::create([
            'subject_id' => $request->subject,
            'teacher_id' => $request->teacher,
            'level_id' => $request->level,
            'date' => $request->date,
            'duration' => $request->duration,
            'type' => $request->exam_type == 0 ? ($request->auto_correct ? '0' : '1') : 1,
            'exam_type' => $request->exam_type ?? 1,
            'header' => $request->header,
            'footer' => $request->footer
        ]);

        return redirect()->back()->with(['success' => '???? ?????? ???????????????? ??????????', 'id' => $exam->id, 'type' => $exam->exam_type]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $exam = Exam::findOrFail($id);

        if (auth()->user()->hasRole('teacher') && $exam->teacher_id == auth()->id()) {
            abort(403);
        }

        return view('admin.exams.prepare', compact('exam'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function edit(Exam $exam)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $validation = Validator::make($request->all(), [
            'duration' => 'required|numeric|max:300|min:1'
        ], [
            'duration.required' => '???? ?????????????? ?????????? ??????????????',
            'duration.numeric' => '?????? ???? ?????????? ?????????? ???? ??????????',
            'duration.max' => '?????? ?????? ???????? ?????????? ???? 5 ??????????',
            'duration.min' => '?????? ?????? ?????????? ???? ???? 1 ??????????',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation);
        }

        if ($request->has('auto_correct')) {

            $questions = ExamQuestion::where('exam_id', $id)->get();

            foreach ($questions as $question) {
                if ($question->type == 1 || $question->type == 2) {
                    return redirect()->back()->with(['error' => '???? ???????? ?????????? ?????? ???????????????? ?????? ?????????? ???????????? ?????? ???????? ?????????? ???????? ???? ?????? ?????????????? ???? ???? ????????']);
                }
            }

            $exam->update([
                'duration' => $request->duration,
                'type' => 0
            ]);
        } else {
            $exam->update([
                'duration' => $request->duration,
                'type' => 1
            ]);
        }

        return redirect()->back()->with(['success' => '???? ?????????? ???????????????? ??????????']);
    }

    public function deleteItems($items)
    {
        foreach ($items as $item) {
            $item->delete();
        }

        return;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $exam = Exam::findOrFail($request->id);

        $enterAttemps = ExamsEnterAttemps::where('exam_id', $exam->id)->get();
        $examQuestions = ExamQuestion::where('exam_id', $exam->id)->get();
        $examSections = ExamSection::where('exam_id', $exam->id)->get();
        $examCorrectings = ExamsCorrecting::whereHas('question', function ($q) use ($exam) {
            $q->where('exam_id', $exam->id);
        })->get();
        $examResults = ExamsResults::where('exam_id', $exam->id)->get();
        $examAnswers = ExamsAnswers::whereHas('question', function ($q) use ($exam) {
            $q->where('exam_id', $exam->id);
        })->get();

        $this->deleteItems($enterAttemps);
        $this->deleteItems($examQuestions);
        $this->deleteItems($examSections);
        $this->deleteItems($examCorrectings);
        $this->deleteItems($examResults);
        $this->deleteItems($examAnswers);

        $exam->delete();

        return redirect()->to(route('exams.index'))->with(['success' => '???? ?????? ???????????????? ??????????']);
    }

    public function view($id)
    {
        $exam = Exam::findOrFail($id);

        if ($exam->exam_type == 1) {
            abort(404);
        }

        return view('admin.exams.view', compact('exam'));
    }
}
