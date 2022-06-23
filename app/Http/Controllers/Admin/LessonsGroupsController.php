<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\LessonsGroups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Admin\LessonsGroupsStudent;
use App\Models\Admin\StudentTeachers;
use App\Models\Admin\Student;
use App\Models\Exams\Exam;

class LessonsGroupsController extends Controller
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
        $validation = Validator::make($request->all(), [
            'group_name' => 'max:191',
            'lesson_id' => 'required|numeric|exists:lessons,id',
            'group_times' => 'required|json'
        ], [
            'group_name.max' => 'أقصى عدد أحرف مسموح بها هو 191 حرف لإسم المجموعة',
            'lesson_id.required' => 'معرف الحصة مطلوب',
            'lesson_id.numeric' => 'يجب أن يتكون معرف الحصة من أرقام',
            'lesson_id.exists' => 'لم يتم العثور على الحصة',
            'group_times.required' => 'مواعيد المجموعة مطلوبة',
            'group_times.json' => 'يجب أن تتكون مواعيد المجموعة من صيرغ json',
        ]);

        if($validation->fails()){
            return redirect()->back()->withInput()->withErrors($validation)->with(['show_add_group' => true]);
        }

        $times = json_decode($request->group_times);
        $itemTimeTable = [];

        foreach($times->days as $index => $day){
            $itemTimeTable[$day] = $times->times[$index];
        }

        LessonsGroups::create([
            'group_name' => $request->group_name ?? "",
            'lesson_id' => $request->lesson_id,
            'times' => $itemTimeTable,
        ]);

        return redirect()->back()->with(['success' => 'تم إضافة المجموعة  بنجاح']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\LessonsGroups  $lessonsGroups
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = LessonsGroups::findOrFail($id);
        $students = Student::where('level_id', $group->lesson->level_id)->get();

        return view('admin.lessons.groups.show', compact('group', 'students'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\LessonsGroups  $lessonsGroups
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
     * @param  \App\Models\Admin\LessonsGroups  $lessonsGroups
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $group = LessonsGroups::findOrFail($request->id);

        $validation = Validator::make($request->all(), [
            'group_name' => 'max:191',
            'group_times' => 'required|json'
        ], [
            'group_name.max' => 'أقصى عدد أحرف مسموح بها هو 191 حرف لإسم المجموعة',
            'group_times.required' => 'مواعيد المجموعة مطلوبة',
            'group_times.json' => 'يجب أن تتكون مواعيد المجموعة من صيرغ json',
        ]);

        if($validation->fails()){
            return redirect()->back()->withInput()->withErrors($validation)->with(['show_edit_group' => true]);
        }

        $times = json_decode($request->group_times);
        $itemTimeTable = [];

        foreach($times->days as $index => $day){
            $itemTimeTable[$day] = $times->times[$index];
        }

        $group->update([
            'group_name' => $request->group_name ?? "",
            'times' => $itemTimeTable,
        ]);

        return redirect()->back()->with(['success' => 'تم تعديل  المجموعة  بنجاح']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\LessonsGroups  $lessonsGroups
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        lessonsGroups::findOrFail($id)->delete();

        return redirect()->to(route('lessons.index'))->with(['success' => 'تم حذف المجموعة بنجاح']);

    }

    public function link_students_to_group (Request $request)
    {
        $validation = Validator::make($request->all(), [
            'students' => 'required|array',
            'group_id' => 'required|numeric|exists:lessons_groups,id'
        ], [
            'students.required' => 'معرف الطالب مطلوب',
            'students.array' => 'يجب أن تتكون قائمة الطلاب من مصفوفة',
            'group_id.required' => 'معرف المجموعة  مطلوب',
            'group_id.numeric' => 'يجب أن يتكون معرف المجموعة  من أرقام فقط',
            'group_id.exists' => 'لم يتم العثور على المجموعة ',
        ]);

        if($validation->fails()){
            return redirect()->back()->withInput()->withErrors($validation)->with(['open_add_modal' => true]);
        }

        $group = LessonsGroups::find($request->group_id);

        foreach($request->students as $student){
            $student = Student::find($student);

            if(!$student){
                return redirect()->back()->with('error', 'لم يتم العثور على الطالب');
            }

            $check = LessonsGroupsStudent::where('student_id', $student->id)
            ->whereHas('group.lesson', function ($q) use ($group) {
                $q->where('subject_id', $group->lesson->subject_id);
            })
            ->first();

            if($check){
                $check->update([
                    'student_id' => $student->id,
                    'lessons_groups_id' => $request->group_id
                ]);
            }else{
                LessonsGroupsStudent::create([
                    'student_id' => $student->id,
                    'lessons_groups_id' => $request->group_id
                ]);
            }

            $studentTeacherExists = StudentTeachers::where('student_id', $student->id)->first();

            if($studentTeacherExists){
                $studentTeachers = $studentTeacherExists->teachers;

                if(isset($studentTeachers[$group->lesson->subject->name_en])){
                    $studentTeachers[$group->lesson->subject->name_en] = $group->lesson->teacher->id;
                }else{
                    $studentTeachers[$group->lesson->subject->name_en] = $group->lesson->teacher->id;
                }

                $studentTeacherExists->update([
                    'teachers' => $studentTeachers
                ]);
            }else{
                $studentTeachers = [
                    $group->lesson->subject->name_en => $group->lesson->teacher->id
                ];

                StudentTeachers::create([
                    'student_id' => $student->id,
                    'teachers' => $studentTeachers
                ]);
            }
        }

        return redirect()->back()->with(['open_add_modal' => true, 'success' => 'تم إضافة الطالب بنجاح']);
    }

    public function get_groups_by_exam(Exam $exam)
    {
        return LessonsGroups::whereHas('lesson', function ($q) use($exam) {
            $q->where([
                ['level_id', $exam->level_id],
                ['teacher_id', $exam->teacher_id]
            ]);
        })
        ->select('id', 'group_name')
        ->get();
    }
}