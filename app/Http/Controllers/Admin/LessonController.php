<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Lesson;
use App\Models\Admin\Teacher;
use App\Models\Admin\LessonsGroups;
use App\Models\Admin\TeachersAbsence;
use App\Models\Admin\LessonsGroupsStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\DataTables\LessonDataTable;

class LessonController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:lessons')->except('show');
        $this->middleware('permission:show-lessons')->only('index');
        $this->middleware('permission:add-lesson')->only(['create', 'store']);
        $this->middleware('permission:edit-lesson')->only(['edit', 'update']);
        $this->middleware('permission:delete-lesson')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LessonDataTable $table)
    {
        return $table->render('admin.lessons.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $teachers = Teacher::with('profile')->select('id', 'profile_id', 'subject_id')->get();
        return view('admin.lessons.create', compact('teachers'));
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
            'level' => 'required|numeric|exists:levels,id',
            'subject' => 'required|numeric|exists:subjects,id',
            'times' => 'required|json',
            'duration' => 'required|numeric|max:500|min:5'
        ], [
            'teacher.required' => 'قم بإختيار المعلم',
            'teacher.numeric' => 'قم بإختيار المعلم بشكل صحيح',
            'teacher.exists' => 'لم يتم العثور على المعلم',
            'level.required' => 'قم بإختيار الصف الدراسي',
            'level.numeric' => 'قم بإختيار الصف الدراسي بشكل صحيح',
            'level.exists' => 'لم يتم العثور على المرحلة',
            'subject.required' => 'يجب إدخال المادة',
            'subject.numeric' => 'يجب إدخال المادة',
            'subject.exists' => 'لم يتم العثور على المادة',
            'times.required' => 'يجب إختيار مواعيد الحصة',
            'times.array' => 'يجب إدخال المواعيد على هيئة مصفوفة',
            'duration.required' => 'قم بكتابة المدة الزمنية',
            'duration.numeric' => 'يجب أن تتكون المدة من أرقام فقط',
            'duration.max' => 'أقصى مدة مسموح بهاهي 500  دقيقة',
            'duration.min' => 'أقل مدة مسموح بها هي 5 دقيقة',
        ]);

        if($validation->fails()){
            return redirect()->back()->withInput()->withErrors($validation);
        }

        $lesson = Lesson::create([
            'subject_id' => $request->subject,
            'teacher_id' => $request->teacher,
            'level_id' => $request->level,
            'duration' => $request->duration
        ]);

        $times = json_decode($request->times);
        foreach($times as $item){
            if(!isset($item->groupName) || !isset($item->days) || !isset($item->times)){
                return redirect()->back()->with(['error' => 'صيغة كتابة المجموعة غير صحيحة']);
            }

            $itemTimeTable = [];
            foreach($item->days as $index => $day){
                $itemTimeTable[$day] = $item->times[$index];
            }

            LessonsGroups::create([
                'group_name' => $item->groupName,
                'lesson_id' => $lesson->id,
                'times' => $itemTimeTable
            ]);
        }

        return redirect()->back()->with(['success' => 'تم إضافة الحصة بنجاح']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\Lesson  $lesson
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lesson = Lesson::with('groups')->findOrFail($id);

        $teacherId = auth()->user()->teacher ? (auth()->user()->teacher->id) : (null);

        if(auth()->user()->hasRole('teacher') && $lesson->teacher_id != $teacherId && $teacherId){
            abort(403);
        }

        $teacherAbsences = TeachersAbsence::where('teacher_id', $lesson->teacher_id)
        ->whereHas('group', function ($item) use($id) {
            return $item->where('lesson_id', $id);
        })
        ->get();

        return view('admin.lessons.show', compact('lesson', 'teacherAbsences'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\Lesson  $lesson
     * @return \Illuminate\Http\Response
     */
    public function edit(Lesson $lesson)
    {
        // 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\Lesson  $lesson
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lesson $lesson)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\Lesson  $lesson
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);

        foreach($lesson->groups as $group){
            LessonsGroupsStudent::where('lessons_groups_id', $group->id)->delete();
        }

        $lesson->delete();

        return redirect()->to(route('lessons.index'))->with(['success' => 'تم حذف الحصة بنجاح']);
    }

}
