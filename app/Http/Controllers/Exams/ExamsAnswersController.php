<?php

namespace App\Http\Controllers\Exams;

use App\Http\Controllers\Controller;
use App\Models\Exams\ExamsAnswers;
use App\Models\Exams\ExamQuestion;
use App\Models\Exams\Exam;
use App\Models\Exams\ExamsResults;
use App\Models\Exams\ExamsEnterAttemps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamsAnswersController extends Controller
{
    public function getQuestion($id)
    {
        $question = ExamQuestion::find($id);

        if (!$question) {
            return null;
        }

        return $question;
    }

    public function questionJsonSort($answer, $col_name = 'answer', $comment = null)
    {
        $server = \Request::server();
        return [
            $col_name => $answer,
            'comment' => $comment,
            'agent' => $server['HTTP_USER_AGENT']
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $checkIfEntered = ExamsEnterAttemps::where([
            ['student_id', auth('students')->user()->id],
            ['exam_id', $id]
        ])->first();

        if (!$checkIfEntered) {
            abort(403);
        }

        if (!$exam->status) {
            abort(404);
        }

        $request = $request->except('_token');
        $questions_ids = [];

        foreach ($request as $question => $value) {
            $questions_ids[] = explode('=', $question)[1];
        }

        // send question to database
        foreach ($questions_ids as $questionId) {
            $question = $this->getQuestion($questionId);

            ExamsAnswers::create([
                'student_id' => auth('students')->user()->id,
                'exams_question_id' => $question->id,
                'body' => $this->questionJsonSort($request['question_id=' . $question->id])
            ]);
        }

        // correct the exam
        if ($exam->type == 0) { // auto correct
            $full_mark = ExamQuestion::where('exam_id', $id)->get()->count();
            $correct_answers_count = 0;
            $wrong_answers_count = $full_mark;

            foreach ($questions_ids as $questionId) {
                $question = $this->getQuestion($questionId);

                if ($question->type == 0 || $question->type == 3) {
                    if ($question->answer == $request['question_id=' . $question->id]) { // true answer
                        $correct_answers_count++;
                        $wrong_answers_count--;
                    }
                }
            }

            $getAttemp = ExamsEnterAttemps::where([
                ['exam_id', $id],
                ['student_id', auth('students')->user()->id]
            ])->first();

            ExamsResults::create([
                'student_id' => auth('students')->user()->id,
                'exam_id' => $id,
                'mark' => [
                    'full_mark' => $full_mark,
                    'correct_answers' => $correct_answers_count,
                    'wrong_answers' => $wrong_answers_count
                ],
                'exams_enter_attemps_id' => $getAttemp->id
            ]);

            return redirect()->to(route('students.exams.results', $id));
        }
        return redirect()->to(route('students.exams.index'))->with(['exam_done' => 'تم تأدية الإختبار بنجاح']);
    }
}
