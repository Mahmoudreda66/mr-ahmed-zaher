<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\TeachersAbsence;
use Illuminate\Http\Request;
use App\Models\Admin\Teacher;
use App\Models\Admin\Lesson;
use App\Models\Admin\LessonsGroups;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\DataTables\TeachersAbsencesDataTable;

class TeachersAbsenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:teachers-absences');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TeachersAbsencesDataTable $teachersTable)
    {
        $teachers = Teacher::with('profile')
        ->select('profile_id', 'subject_id', 'id')
        ->get();
        
        $latests = TeachersAbsence::orderBy('id', 'DESC')
        ->select('teacher_id', 'lessons_group_id', 'join_at', 'status', 'id')
        ->paginate(50);

        return $teachersTable->render('admin.teachers.absences', compact('teachers', 'latests'));
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
            'teacher' => 'required|numeric|exists:teachers,id',
            'lesson' => 'required|numeric|exists:lessons,id',
            'group' => 'required|numeric|exists:lessons_groups,id',
            'status' => 'required|boolean',
            'at' => 'required|date'
        ], [
            'teacher.required' => 'قم بإختيار المعلم',
            'teacher.numeric' => 'قم بإختيار المعلم بشكل صحيح',
            'lesson.required' => 'قم بإختيار الحصة',
            'lesson.numeric' => 'قم بإختيار الحصة بشكل صحيح',
            'group.required' => 'قم بإختيار المجموعة',
            'group.numeric' => 'يجب أن يتكون معرف المجموعة من أرقام',
            'group.exists' => 'لم يتم العثور على المجموعة',
            'status.required' => 'قم بإختيار حالة الغياب',
            'status.boolean' => 'قم بإختيار حالة الغياب بشكل صحيح',
            'at.required' => 'قم بكتابة تاريخ الحضور',
            'at.data' => 'قم بكتابة تاريخ الحضور بشكل صحيح'
        ]);

        if($validation->fails()){
            return redirect()->back()->withInput()->withErrors($validation);
        }

        $group = LessonsGroups::find($request->group);

        if($group->lesson_id != $request->lesson){
            return redirect()->back()->with(['error' => 'لم يتم العصور على مجموعة الحصة']);
        }

        TeachersAbsence::updateOrCreate([
            'teacher_id' => $request->teacher,
            'lessons_group_id' => $request->group,
            'join_at' => $request->at
        ], [
            'teacher_id' => $request->teacher,
            'lessons_group_id' => $request->group,
            'join_at' => $request->at,
            'status' => $request->status
        ]);

        return redirect()->back()->with(['success' => 'تم تسجيل حالة المعلم اليوم بنجاح']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\TeachersAbsence  $teachersAbsence
     * @return \Illuminate\Http\Response
     */
    public function show(TeachersAbsence $teachersAbsence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\TeachersAbsence  $teachersAbsence
     * @return \Illuminate\Http\Response
     */
    public function edit(TeachersAbsence $teachersAbsence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\TeachersAbsence  $teachersAbsence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TeachersAbsence $teachersAbsence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\TeachersAbsence  $teachersAbsence
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $record = TeachersAbsence::findOrFail($id)->delete();

        return redirect()->back()->with(['success' => 'تم حذف التسجيل بنجاح']);
    }

    public function toggle($id)
    {
        $record = TeachersAbsence::findOrFail($id);
        
        $record->update(['status' => $record->status == 0 ? 1 : 0]);

        return true;
    }
}
