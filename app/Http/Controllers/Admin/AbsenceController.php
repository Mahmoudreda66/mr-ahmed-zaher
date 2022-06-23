<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Absence;
use App\Models\Admin\Lesson;
use App\Models\Admin\Expenses;
use App\Models\Admin\Level;
use App\Models\Admin\Student;
use App\Models\Admin\LessonsGroups;
use App\Models\Admin\LessonsGroupsStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\DataTables\AbsenceDataTable;

class AbsenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:absences');
        $this->middleware('permission:last-students-absences-records')->only('latest_index');
        $this->middleware('permission:lessons-absence-mode')->only('lessons_absence');
        $this->middleware('permission:day-absence-mode')->only('day_absence');
        $this->middleware('permission:absence-report')->only('reports');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function lessons_absence(Request $request)
    {
        $levels = Level::orderBy('id', 'ASC')->get();

        if ($request->has('level') && $request->has('lesson') && $request->has('group') && $request->has('date')) {
            $level = Level::findOrFail($request->level);
            $lesson = Lesson::findOrFail($request->lesson);
            $group = LessonsGroups::findOrFail($request->group);

            if($lesson->level_id != $level->id || $group->lesson_id != $lesson->id){
                abort(404);
            }

            return view('admin.absences.lessons_absence', compact('levels'));
        }else{
            return view('admin.absences.lessons_absence', compact('levels'));
        }
    }

    public function day_absence()
    {
        $dayAbsences = Absence::where([
            ['join_at', date('Y-m-d')]
        ])->orderBy('id', 'DESC')
        ->with(['student.level', 'student.expenses' => function ($q) {
            return $q->where('month', date('m'));
        }])
        ->paginate(50);

        $levels = Level::all();

        return view('admin.absences.day_absence', compact('dayAbsences', 'levels'));
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

    public function response($status, $message, $data)
    {
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_lessons_absence(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'token' => 'required|exists:students,id',
            'group' => 'required|numeric|exists:lessons_groups,id',
            'date' => 'required|date'
        ], [
            'token.required' => 'المُعرف مطلوب',
            'token.exists' => 'لم يتم العثور على الطالب',
            'group.required' => 'المجموعة مطلوبة',
            'group.numeric' => 'يجب أن تتكون المجموعة من أرقام',
            'group.exists' => 'لم يتم العثور على المجموعة',
            'date.required' => 'قم بإختيار تاريخ الغياب',
            'date.date' => 'صيغة التاريخ غير صحيحة',
        ]);

        if($validation->fails()){
            return response()->json($this->response(false, 'validation', $validation->errors()->all()));
        }

        $student = Student::find($request->token);

        Absence::updateOrCreate([
            'student_id' => $student->id,
            'lessons_group_id' => $request->group,
            'join_at' => $request->date
        ], [
            'student_id' => $student->id,
            'lessons_group_id' => $request->group,
            'join_at' => $request->date,
            'status' => 1
        ]);

        return response()->json($this->response(true, 'success', $student));
    }

    public function store_lessons_absence_a_group(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'token' => 'required|exists:students,id',
            'group' => 'required|numeric|exists:lessons_groups,id',
            'date' => 'required|date'
        ], [
            'token.required' => 'المُعرف مطلوب',
            'token.exists' => 'لم يتم العثور على الطالب',
            'group.required' => 'المجموعة مطلوبة',
            'group.numeric' => 'يجب أن تتكون المجموعة من أرقام',
            'group.exists' => 'لم يتم العثور على المجموعة',
            'date.required' => 'قم بإختيار تاريخ الغياب',
            'date.date' => 'صيغة التاريخ غير صحيحة',
        ]);

        if($validation->fails()){
            return response()->json($this->response(false, 'validation', $validation->errors()->all()));
        }

        $student = Student::find($request->token);
        $group = LessonsGroups::find($request->group);
        $studentGroup = LessonsGroupsStudent::where('student_id', $student->id)
        ->whereHas('group.lesson', function ($q) use ($group) {
            $q->where('subject_id', $group->lesson->subject_id);
        })
        ->first();

        if(!$studentGroup){
            return response()->json($this->response(false, 'validation', ['الطالب غير مقترن بأي مجموعة']));
        }

        Absence::updateOrCreate([
            'student_id' => $student->id,
            'lessons_group_id' => $studentGroup->lessons_groups_id,
            'join_at' => $request->date
        ], [
            'student_id' => $student->id,
            'lessons_group_id' => $studentGroup->lessons_groups_id,
            'join_at' => $request->date,
            'status' => 1
        ]);

        return response()->json($this->response(true, 'success', $student));
    }

    public function store_day_absence(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'token' => 'required|exists:students,id',
            'date' => 'required|date'
        ], [
            'token.required' => 'قم بمسح رمز الإستجابة السريع',
            'token.exists' => 'لم يتم العثور على الطالب',
            'date.required' => 'التاريخ مطلوب',
            'date.date' => 'التاريخ غير صالح'
        ]);

        if($validation->fails()){
            return $this->response(false, 'validation', $validation->errors()->all());
        }

        $student = Student::where('id', $request->token)->with('level')->first();

        $check = Absence::where([
            ['student_id', $student->id],
            ['join_at', $request->date]
        ])->first();

        if(!$check){
            $item = Absence::create([
                'student_id' => $student->id,
                'join_at' => $request->date,
                'status' => 1
            ]);

            $hasPaid = Expenses::where([
                'student_id' => $student->id,
                'month' => date('m')
            ])
            ->whereYear('created_at', date('Y'))
            ->first();

            $absenceDays = Absence::where([
                'student_id' => $student->id,
                'status' => 0
            ])
            ->whereMonth('join_at', date('m'))
            ->count();

            return $this->response(true, 'success', [
                'student' => $student,
                'item' => $item,
                'hasPaid' => $hasPaid ? true : false,
                'absenceDays' => $absenceDays
            ]);
        }
    
        return $this->response(false, 'validation', ['تم تسجيل حضور الطالب اليوم بالفعل']);    
    }

    public function end_day(Request $request)
    {
        $validation = Validator($request->all(), [
            'date' => 'date|required',
            'level' => 'required|exists:levels,id'
        ], [
            'level.required' => 'المرحلة مطلوبة',
            'level.exists' => 'لم يتم العثور على المرحلة',
            'date.required' => 'التاريخ مطلوب',
            'date.date' => 'التاريخ غير صالح'
        ]);

        if($validation->fails()){
            return redirect()->back()->withErrors($validation)->with('open_end_modal', true);
        }

        $students = Student::where('level_id', $request->level)
        ->select('id')
        ->get();

        foreach($students as $student){
            Absence::firstOrCreate([
                'student_id' => $student->id,
                'join_at' => $request->date,
            ], [
                'student_id' => $student->id,
                'join_at' => $request->date,
                'status' => 0,
                'lessons_group_id' => null
            ]);
        }

        return redirect()->back()->with(['success' => 'تم تغييب الباقي بنجاح']);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function show(Absence $absence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function edit(Absence $absence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Absence $absence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'required|numeric'
        ], [
            'id.required' => 'لقد حدث خطأ غير متوقع في معرف التسجيل',
            'id.numeric' => 'لقد حدث خطأ غير متوقع في معرف التسجيل'
        ]);

        if($validation->fails()){
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $record = Absence::findOrFail($request->id)->delete();

        return redirect()->back()->with(['success' => 'تم حذف التسجيل بنجاح']);
    }

    public function end_lesson(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'tokens' => 'required|json',
            'group' => 'required|exists:lessons_groups,id',
            'date' => 'required|date'
        ], [
            'tokens.required' => 'شيفرات الطلاب مطلوبة',
            'tokens.json' => 'شيفرات الطلاب يجب أن تتكون من صيغة json',
            'group.required' => 'المجموعة مطلوبة',
            'group.exists' => 'لم يتم العثور على المجموعة',
            'date.required' => 'قم بإختيار تاريخ الغياب',
            'date.date' => 'صيغة التاريخ غير صحيحة',
        ]);

        if($validation->fails()){
            return response()->json($this->response(false, 'validation', $validation->errors()->all()));
        }

        $errors = 0;
        foreach (json_decode($request->tokens, true) as $token) {
            $student = Student::where('id', $token)->first();

            if(!$student){
                $errors++;
            }else{
                Absence::firstOrCreate([
                    'student_id' => $student->id,
                    'lessons_group_id' => $request->group,
                    'join_at' => $request->date,
                    'status' => 0
                ]);
            }
        }

        return $this->response(true, 'success', $errors);
    }

    public function latest_index(AbsenceDataTable $table)
    {
        return $table->render('admin.absences.latest_index');
    }

    public function toggle($id)
    {
        $record = Absence::findOrFail($id);

        if($record->status == 0){
            $record->update([
                'status' => 1
            ]);
        }else{
            $record->update([
                'status' => 0
            ]);
        }

        return true;
    }

    public function reports(Request $request)
    {
        if(!empty($request->level)){
            $levels = Level::orderBy('id', 'ASC')->where('works', 1)->get();

            if($request->has('from') && $request->has('to')){
                $validation = Validator::make($request->all(), [
                    'from' => 'date|nullable',
                    'to' => 'date|nullable'
                ]);

                if($validation->fails()){
                    abort(404);
                }

                $students = Student::where('level_id', $request->level)->get();

                $absences = Absence::with('student')
                ->whereHas('student', function ($q) use($request) {
                    $q->where('level_id', $request->level);
                })
                ->whereBetween('join_at', [$request->from, $request->to])
                ->orderBy('id', 'DESC')
                ->get();

                if($request->has('student') && $request->student != '*'){
                    $absences = Absence::with('student')
                    ->whereHas('student', function ($q) use($request) {
                        $q->where([
                            ['level_id', $request->level],
                            ['id', $request->student]
                        ]);
                    })
                    ->whereBetween('join_at', [$request->from, $request->to])
                    ->get();

                    $student = Student::where([
                        ['id', $request->student],
                        ['level_id', $request->level]
                    ])->first();

                    if(!$student){abort(404);}

                    return view('admin.absences.reports', compact('students', 'levels', 'student', 'absences'));
                }

                return view('admin.absences.reports', compact('students', 'levels', 'absences'));;
            }

            return view('admin.absences.reports', compact('levels'));
        }

        abort(404);
    }
}
