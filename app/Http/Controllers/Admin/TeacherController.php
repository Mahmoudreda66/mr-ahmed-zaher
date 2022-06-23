<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Level;
use App\Models\Admin\StudentTeachers;
use App\Models\User;
use App\Models\Admin\Subject;
use App\Models\Admin\Lesson;
use App\Models\Admin\Teacher;
use App\Models\Admin\TeachersAbsence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\DataTables\TeacherDataTable;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:teachers')->except(['show', 'get_students']);
        $this->middleware('permission:show-teachers')->only('index');
        $this->middleware('permission:add-teacher')->only(['create', 'store']);
        $this->middleware('permission:edit-teacher')->only(['update', 'edit']);
        $this->middleware('permission:delete-teacher')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TeacherDataTable $table)
    {
        return $table->render('admin.teachers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $levels = Level::orderBy('id', 'ASC')->select('id', 'name_ar')->get();
        $subjects = Subject::orderBy('name_ar', 'ASC')->select('id', 'name_ar')->get();
        return view('admin.teachers.create', compact('levels', 'subjects'));
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
            'name' => 'required|max:150|min:3',
            'subject' => 'required|numeric',
            'levels' => 'array|required',
            'mobile' => 'required|numeric|min:1000000|max:9999999999|unique:users,phone'
        ], [
            'name.required' => 'إسم المعلم مطلوب',
            'name.max' => 'العدد الأقصى للأحرف هو 150 حرف',
            'name.min' => 'العدد الأقل للأحرف هو 3 أحرف',
            'subject.required' => 'المادة مطلوبة',
            'subject.numeric' => 'قم بإختيار المادة بشكل صحيح',
            'levels.required' => 'قم بإختيار المرحلة بشكل صحيح',
            'levels.required' => 'المرحلة مطلوبة',
            'mobile.required' => 'رقم الهاتف مطلوب',
            'mobile.numeric' => 'يجب أن يتكون رقم الهاتف من أرقام فقط',
            'mobile.min' => 'أقل أرقام مسموح بها هي 7 أرقام',
            'mobile.max' => 'يجب أن يتكون رقم الهاتف من 11 رقم',
            'mobile.unique' => 'يوجد معلم بنفس رقم الهاتف هذا بالفعل',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation);
        }

        $check = User::where([
            ['name', $request->name],
            ['phone', $request->mobile]
        ])->first();

        if ($check) {
            return redirect()->back()->with(['error' => 'يوجد معلم بهذه البيانات بالفعل']);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $extension = $image->getClientOriginalExtension();
            $imageName = time() . '_' . $image->getClientOriginalName();
            define('IMAGE_MAX_SIZE', 5);
            $imageSize = $image->getSize() / 1048576;

            if (!$image->isValid()) {
                return redirect()->back()->with(['image_error' => 'لم يتم تحميل الصورة بشكل صحيح'])->withInput();
            }

            if (!in_array($extension, $allowed_extensions)) {
                return redirect()->back()->with(['image_error' => 'الإمتدادات المسموح بها فقط هي' . implode(', ', $allowed_extensions)])->withInput();
            }

            if ($imageSize > IMAGE_MAX_SIZE) {
                return redirect()->back()->with(['image_error' => 'أقصى حجم للصورة هو ' .  IMAGE_MAX_SIZE . 'ميجا'])->withInput();
            }

            Storage::putFileAs('images/teachers', $image, $imageName);
        }

        $teacherProfile = User::create([
            'name' => $request->name,
            'image' => $imageName ?? null,
            'phone' => $request->mobile,
            'password' => bcrypt($request->mobile),
        ]);

        $teacher = Teacher::create([
            'subject_id' => $request->subject,
            'profile_id' => $teacherProfile->id,
            'levels' => json_encode($request->levels),
        ]);

        $teacherProfile->attachRole('teacher');

        return redirect()->back()->with(['success' => 'تم إضافة المعلم بنجاح']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $teacher = Teacher::findOrFail($id);

        $teacherId = auth()->user()->teacher ? (auth()->user()->teacher->id) : (null);

        if(auth()->user()->hasRole('teacher') && $teacherId != $id && $teacherId){
            abort(403);
        }

        $absences = Teacher::where('id', $id)
        ->with('absences.group.lesson.level')
        ->first();

        $presentCount = $absences->absences->filter(function ($q) {
            return $q->status == 1;
        })->count();

        $absenceCount = $absences->absences->filter(function ($q) {
            return $q->status == 0;
        })->count();

        $lessons = Lesson::where('teacher_id', $id)
        ->get();

        return view('admin.teachers.show', compact('teacher', 'presentCount', 'absenceCount', 'lessons', 'absences'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $teacher = Teacher::findOrFail($id);
        $levels = Level::orderBy('id', 'ASC')->select('id', 'name_ar')->get();
        $subjects = Subject::orderBy('name_ar', 'ASC')->select('id', 'name_ar')->get();
        return view('admin.teachers.edit', compact('levels', 'subjects', 'teacher'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        $validation = Validator::make($request->all(), [
            'name' => 'required|max:150|min:3',
            'subject' => 'required|numeric',
            'levels' => 'array|required',
            'mobile' => ['required', 'numeric', 'min:1000000',
            'max:9999999999', Rule::unique('users', 'phone')->ignore($teacher->profile)]
        ], [
            'name.required' => 'إسم المعلم مطلوب',
            'name.max' => 'العدد الأقصى للأحرف هو 150 حرف',
            'name.min' => 'العدد الأقل للأحرف هو 3 أحرف',
            'subject.required' => 'المادة مطلوبة',
            'subject.numeric' => 'قم بإختيار المادة بشكل صحيح',
            'levels.required' => 'قم بإختيار المرحلة بشكل صحيح',
            'levels.required' => 'المرحلة مطلوبة',
            'mobile.required' => 'رقم الهاتف مطلوب',
            'mobile.numeric' => 'يجب أن يتكون رقم الهاتف من أرقام فقط',
            'mobile.min' => 'أقل أرقام مسموح بها هي 7 أرقام',
            'mobile.max' => 'يجب أن يتكون رقم الهاتف من 11 رقم',
            'mobile.unique' => 'يوجد معلم بنفس رقم الهاتف هذا بالفعل',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation);
        }

        $check = User::where([
            ['name', $request->name],
            ['phone', $request->mobile]
        ])->first();

        if ($check && $check->teacher->id != $teacher->id) {
            return redirect()->back()->with(['error' => 'يوجد معلم بهذه البيانات بالفعل']);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $extension = $image->getClientOriginalExtension();
            $imageName = time() . '_' . $image->getClientOriginalName();
            define('IMAGE_MAX_SIZE', 5);
            $imageSize = $image->getSize() / 1048576;

            if (!$image->isValid()) {
                return redirect()->back()->with(['image_error' => 'لم يتم تحميل الصورة بشكل صحيح'])->withInput();
            }

            if (!in_array($extension, $allowed_extensions)) {
                return redirect()->back()->with(['image_error' => 'الإمتدادات المسموح بها فقط هي' . implode(', ', $allowed_extensions)])->withInput();
            }

            if ($imageSize > IMAGE_MAX_SIZE) {
                return redirect()->back()->with(['image_error' => 'أقصى حجم للصورة هو ' .  IMAGE_MAX_SIZE . 'ميجا'])->withInput();
            }

            if ($teacher->profile->image) {
                if (Storage::exists('images/teachers/' . $teacher->profile->image)) {
                    Storage::delete('images/teachers/' . $teacher->profile->image);
                }
            }

            Storage::putFileAs('images/teachers', $image, $imageName);
        }

        User::find($teacher->profile_id)->update([
            'name' => $request->name,
            'image' => $imageName ?? $teacher->profile->image,
            'phone' => $request->mobile,
            'password' => bcrypt($request->mobile),
        ]);

        $teacher->update([
            'subject_id' => $request->subject,
            'levels' => json_encode($request->levels),
        ]);

        return redirect()->back()->with(['success' => 'تم تحديث المعلم بنجاح']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $teacher = Teacher::findOrFail($request->id);
        $profile = $teacher->profile->id;

        if ($teacher->profile->image) {
            if (Storage::exists('images/teachers/' . $teacher->profile->image)) {
                Storage::delete('images/teachers/' . $teacher->profile->image);
            }
        }

        User::findOrFail($profile)->delete();
        $teacher->delete();

        return redirect()->to(route('teachers.index'))->with(['success' => 'تم حذف المعلم بنجاح']);
    }

    public function get_students($id)
    {
        $teacher = Teacher::findOrFail($id);

        $info = StudentTeachers::with('student.level')
        ->where('teachers->' . $teacher->subject->name_en, $teacher->id)
        ->select('student_id')
        ->get();

        $counts = [];

        foreach (json_decode($teacher->levels, true) as $level) {
            $query = StudentTeachers::with('student.level')
            ->where('teachers->' . $teacher->subject->name_en, $teacher->id)
            ->whereHas('student', function ($q) use ($level) {
                return $q->where('level_id', $level);
            })
            ->select('student_id')
            ->count();

            $counts[$level] = $query;
        }

        return response()->json([
            'students_count' => count($info),
            'data' => $info,
            'counts' => $counts
        ]);
    }
}
