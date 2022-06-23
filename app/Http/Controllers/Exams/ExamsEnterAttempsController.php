<?php

namespace App\Http\Controllers\Exams;

use App\Http\Controllers\Controller;
use App\Models\Admin\Level;
use App\Models\Exams\Exam;
use App\Models\Exams\ExamsEnterAttemps;
use App\Models\Exams\ExamsAnswers;
use App\Models\Exams\ExamsCorrecting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamsEnterAttempsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:exams-attemps');
        $this->middleware('permission:delete-exam-attemp')->only('destroy');
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
            
            $arguments = [
                'attemps',
                'levelData',
                'levels'
            ];

            if($request->has('exam')){
                $exams = Exam::where('level_id', $request->level)->get();

                $attemps = ExamsEnterAttemps::whereHas('student', function ($q) use ($levelData) {
                    $q->where('level_id', $levelData->id);
                })
                ->where('exam_id', $request->exam)
                ->orderBy('id', 'DESC')
                ->paginate(30);

                $arguments[] = 'exams';
            }else{
                $attemps = ExamsEnterAttemps::whereHas('student', function ($q) use ($levelData) {
                    $q->where('level_id', $levelData->id);
                })
                ->orderBy('id', 'DESC')
                ->paginate(30);
            }

            return view('admin.exams.attemps', compact(...$arguments));
        }

        abort(404);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExamsEnterAttemps  $examsEnterAttemps
     * @return \Illuminate\Http\Response
     */
    public function show(ExamsEnterAttemps $examsEnterAttemps)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExamsEnterAttemps  $examsEnterAttemps
     * @return \Illuminate\Http\Response
     */
    public function edit(ExamsEnterAttemps $examsEnterAttemps)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExamsEnterAttemps  $examsEnterAttemps
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExamsEnterAttemps $examsEnterAttemps)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExamsEnterAttemps  $examsEnterAttemps
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $attemp = ExamsEnterAttemps::findOrFail($id);

        $student_answers = ExamsAnswers::where('student_id', $attemp->student->id)
        ->whereHas('question', function ($q) use ($attemp) {
            $q->where('exam_id', $attemp->exam_id);
        })->get();

        foreach($student_answers as $answer){
            $answer->delete();
        }

        $student_correcting_answers = ExamsCorrecting::where('student_id', $attemp->student->id)
        ->whereHas('question', function ($q) use ($attemp) {
            $q->where('exam_id', $attemp->exam_id);
        })->get();

        foreach($student_correcting_answers as $item){
            $item->delete();
        }

        $attemp->delete();

        return response()->json(['status' => true]);
    }
}
