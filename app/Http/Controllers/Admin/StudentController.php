<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Absence;
use App\Models\Admin\Expenses;
use App\Models\Admin\Level;
use App\Models\Admin\Student;
use App\Models\Admin\Subject;
use App\Models\Admin\Settings;
use App\Models\Admin\StudentTeachers;
use App\Models\Exams\ExamsEnterAttemps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\DataTables\StudentDataTable;
use App\DataTables\StudentsConfirmationDataTable;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:students');
        $this->middleware('permission:show-students')->only('index');
        $this->middleware('permission:add-student')->only(['create', 'store']);
        $this->middleware('permission:show-student')->only('show');
        $this->middleware('permission:students-list')->only('list');
        $this->middleware('permission:students-absence-list')->only('absence_list');
        $this->middleware('permission:students-expenses-list')->only('expenses_list');
        $this->middleware('permission:delete-student')->only('destroy');
        $this->middleware('permission:edit-student')->only(['edit', 'update']);
        $this->middleware('permission:filled-absence-list')->only(['filled_absence_list', 'fill_absence_list']);
        $this->middleware('permission:print-barcodes')->only('print_barcodes');
        $this->middleware('permission:students-application-list')->only('confirm_application');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, StudentDataTable $table)
    {
        return $table->render('admin.students.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $levels = Level::select('id', 'name_ar')->get();
        $choosing_teachers = Settings::where('name', 'students_must_choose_teachers')->select('value')->first()['value'];
        $subjects = Subject::orderBy('id', 'ASC')->get();

        return view('admin.students.create', compact('levels', 'choosing_teachers', 'subjects'));
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
            'name' => 'required|max:191|min:5',
            'level' => 'required|in:1,2,3,4,5,6',
            'mobile' => ['max:10000000000', new \App\Rules\numericOrNull],
            'mobile2' => ['max:10000000000', new \App\Rules\numericOrNull],
            'student_mobile' => ['max:10000000000', new \App\Rules\numericOrNull],
            'gender' => 'required|in:0,1',
            'given_money' => [new \App\Rules\numericOrNull],
            'edu_type' => 'required|boolean'
        ], [
            'name.required' => 'إسم الطالب مطلوب',
            'name.max' => 'يجب ألا يزيد الإسم عن 191 حرف',
            'name.min' => 'يجب ألا يقل الإسم عن 5 أحرف',
            'level.required' => 'المرحلة التعليمية مطلوبة',
            'level.in' => 'قم بأختيار المرحلة بشكل صحيح',
            'mobile.max' => 'يجب ألا يزيد رقم الهاتف عن 11 رقم',
            'mobile2.max' => 'يجب ألا يزيد رقم الهاتف الآخر عن 11 رقم',
            'student_mobile.max' => 'يجب ألا يزيد رقم هاتف الطالب عن 11 حرف',
            'mobile.max' => 'يجب ألا يزيد رقم الهاتف عن 11 رقم',
            'gender.required' => 'قم بإختيار الجنس',
            'gender.in' => 'قم بإختيار الجنس بشكل صحيح',
            'edu_type.required' => 'نوع التعليم مطلوب',
            'edu_type.boolean' => 'قيمة غير صالحة'
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation);
        }

        $preps = [1, 2, 3];
        $secondaries = [4, 5, 6];

        $students_must_choose_teachers = Settings::where('name', 'students_must_choose_teachers')
            ->select('value')
            ->first()['value'];

        $code = rand(10, 99) . rand(10, 99) . rand(10, 99) . rand(10, 99);
        $exists = Student::where('code', $code)->select('code')->first();

        do {
            $code = rand(10, 99) . rand(10, 99) . rand(10, 99) . rand(10, 99);
        } while ($exists);

        $deleted_at = null;

        if($request->has('confirm')){
            $deleted_at = now()->timestamp;
        }

        if (in_array($request->level, $preps)) { // prepratory student
            if ($students_must_choose_teachers) {
                $subjects = Subject::where('level', 0)->orWhere('level', 2)->get();

                $subjects_teachers = [];

                for ($i = 0; $i < count($subjects); $i++) {
                    $subject = $request->toArray()[$subjects[$i]['name_en']] ?? null;

                    if ($subject && $subject !== 'NULL') {
                        $subjects_teachers[$subjects[$i]['name_en']] = $subject;
                    }
                }

                unset($subjects_teachers['french']);
                unset($subjects_teachers['germany']);
            }

            $student = Student::create([
                'name' => $request->name,
                'level_id' => $request->level,
                'mobile' => !empty($request->mobile) ? $request->mobile : null,
                'mobile2' => !empty($request->mobile2) ? $request->mobile2 : null,
                'student_mobile' => !empty($request->student_mobile) ? $request->student_mobile : null,
                'gender' => $request->gender,
                'given_money' => $request->given_money,
                'code' => $code,
                'division' => null,
                'sub_language' => null,
                'edu_type' => $request->edu_type,
                'user_id' => auth()->user()->id,
                'deleted_at' => $deleted_at
            ]);

            if ($students_must_choose_teachers) {
                StudentTeachers::create([
                    'student_id' => $student->id,
                    'teachers' => $subjects_teachers
                ]);
            }
        } else if (in_array($request->level, $secondaries)) { // secondary student
            if($request->level == 5 || $request->level == 6){
                if($request->sub_language != "" && $request->division != ""){
                    $smallValidation = Validator::make($request->all(), [
                        'sub_language' => 'boolean',
                        'division' => 'required|boolean',
                    ], [
                        'sub_language.boolean' => 'قم بإختيار اللغة الثانية من بين الإختيارات المعطاة فقط',
                        'division.required' => 'قم بإختيار الشعبة',
                        'division.boolean' => 'قم بإختيار الشعبة بشكل صحيح',
                    ]);
                }
            }else if($request->level == 4){
                if($request->sub_language != ""){
                    $smallValidation = Validator::make($request->all(), [
                        'sub_language' => 'boolean',
                    ], [
                        'sub_language.boolean' => 'قم بإختيار اللغة الثانية من بين الإختيارات المعطاة فقط',
                    ]);
                }
            }

            if (isset($smallValidation) && $smallValidation->fails()) {
                return redirect()->back()->withInput()->withErrors($smallValidation);
            }

            if ($students_must_choose_teachers) {
                if ($request->division !== null) {
                    $subjects = Subject::where([['level', 1], ['division', $request->division]])->orWhere('level', 2)->get();
                } else {
                    $subjects = Subject::where('level', 1)->orWhere('level', 2)->orWhereNull('level')->get();
                }

                $subjects_teachers = [];

                for ($i = 0; $i < count($subjects); $i++) {
                    $subject = $request->toArray()[$subjects[$i]['name_en']] ?? null;

                    if ($subject && $subject !== 'NULL') {
                        $subjects_teachers[$subjects[$i]['name_en']] = $subject;
                    }
                }

                if ($request->sub_language === '0') { // french
                    unset($subjects_teachers['germany']);
                } else { // germany
                    unset($subjects_teachers['french']);
                }
            }

            $student = Student::create([
                'name' => $request->name,
                'level_id' => $request->level,
                'mobile' => !empty($request->mobile) ? $request->mobile : null,
                'mobile2' => !empty($request->mobile2) ? $request->mobile2 : null,
                'student_mobile' => !empty($request->student_mobile) ? $request->student_mobile : null,
                'gender' => $request->gender,
                'given_money' => $request->given_money,
                'code' => $code,
                'division' => $request->division,
                'sub_language' => $request->sub_language,
                'edu_type' => $request->edu_type,
                'user_id' => auth()->user()->id,
                'deleted_at' => $deleted_at
            ]);

            if ($students_must_choose_teachers) {
                StudentTeachers::create([
                    'student_id' => $student->id,
                    'teachers' => $subjects_teachers
                ]);
            }
        }

        if($request->has('back_to')){
            if($request->back_to === "parents"){

                auth()->guard('parents')->login($student);

                return redirect()->to(route('parents.home'))->with('success', 'تم حفظ البيانات بنجاح');

            }else if($request->back_to === "students_application"){

                return redirect()->to(route('studentsApplication.home'))->with([
                    'success' => 'true',
                    'open_modal' => true,
                    'student_code' => $student->code
                ]);

            }
        }else{
            $print_after_add_student = Settings::where('name', 'print_after_add_student')->select('value')->first()['value'];

            if ($print_after_add_student) {
                return redirect()->back()->with(['success' => 'تم إضافة الطالب بنجاح', 'print' => $student->id]);
            }

            return redirect()->back()->with(['success' => 'تم إضافة الطالب بنجاح']);
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $student = Student::with('subjects')->findOrFail($id);

        $absences = Absence::where([
            ['student_id', $student->id],
            ['status', 0]
        ])
            ->latest()
            ->get();

        $exams = ExamsEnterAttemps::with('result')
            ->where('student_id', $student->id)
            ->get();

        return view('admin.students.show', compact('student', 'absences', 'exams'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $levels = Level::select('id', 'name_ar')->get();
        $choosing_teachers = Settings::where('name', 'students_must_choose_teachers')->select('value')->first()['value'];
        $subjects = Subject::orderBy('id', 'ASC')->get();

        return view('admin.students.edit', compact('student', 'levels', 'choosing_teachers', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validation = Validator::make($request->all(), [
            'name' => 'required|max:191|min:5',
            'level' => 'required|in:1,2,3,4,5,6',
            'mobile' => ['max:10000000000', new \App\Rules\numericOrNull],
            'mobile2' => ['max:10000000000', new \App\Rules\numericOrNull],
            'student_mobile' => ['max:10000000000', new \App\Rules\numericOrNull],
            'gender' => 'required|in:0,1',
            'given_money' => [new \App\Rules\numericOrNull],
            'edu_type' => 'required|boolean'
        ], [
            'name.required' => 'إسم الطالب مطلوب',
            'name.max' => 'يجب ألا يزيد الإسم عن 191 حرف',
            'name.min' => 'يجب ألا يقل الإسم عن 5 أحرف',
            'level.required' => 'المرحلة التعليمية مطلوبة',
            'level.in' => 'قم بأختيار المرحلة بشكل صحيح',
            'mobile.max' => 'يجب ألا يزيد رقم الهاتف عن 11 رقم',
            'mobile2.max' => 'يجب ألا يزيد رقم الهاتف الآخر عن 11 رقم',
            'student_mobile.max' => 'يجب ألا يزيد رقم هاتف الطالب عن 11 حرف',
            'mobile.max' => 'يجب ألا يزيد رقم الهاتف عن 11 رقم',
            'gender.required' => 'قم بإختيار الجنس',
            'gender.in' => 'قم بإختيار الجنس بشكل صحيح',
            'edu_type.required' => 'نوع التعليم مطلوب',
            'edu_type.boolean' => 'قيمة غير صالحة'
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation);
        }

        $preps = [1, 2, 3];
        $secondaries = [4, 5, 6];

        $students_must_choose_teachers = Settings::where('name', 'students_must_choose_teachers')
            ->select('value')
            ->first()['value'];

        if (in_array($request->level, $preps)) { // prepratory student
            if ($students_must_choose_teachers) {
                $subjects = Subject::where('level', 0)->orWhere('level', 2)->get();

                $subjects_teachers = [];

                for ($i = 0; $i < count($subjects); $i++) {
                    $subject = $request->toArray()[$subjects[$i]['name_en']] ?? null;

                    if ($subject && $subject !== 'NULL') {
                        $subjects_teachers[$subjects[$i]['name_en']] = $subject;
                    }
                }

                unset($subjects_teachers['french']);
                unset($subjects_teachers['germany']);
            }

            $student->update([
                'name' => $request->name,
                'level_id' => $request->level,
                'mobile' => !empty($request->mobile) ? $request->mobile : null,
                'mobile2' => !empty($request->mobile2) ? $request->mobile2 : null,
                'student_mobile' => !empty($request->student_mobile) ? $request->student_mobile : null,
                'gender' => $request->gender,
                'given_money' => $request->given_money,
                'division' => null,
                'sub_language' => null,
                'edu_type' => $request->edu_type,
            ]);

            if ($students_must_choose_teachers) {
                StudentTeachers::where('student_id', $id)->updateOrCreate([
                    'student_id' => $student->id,
                ], [
                    'student_id' => $student->id,
                    'teachers' => $subjects_teachers
                ]);
            }
        } else if (in_array($request->level, $secondaries)) { // secondary student
            if($request->level == 5 || $request->level == 6){
                if($request->sub_language != "" && $request->division != ""){
                    $smallValidation = Validator::make($request->all(), [
                        'sub_language' => 'boolean',
                        'division' => 'required|boolean',
                    ], [
                        'sub_language.boolean' => 'قم بإختيار اللغة الثانية من بين الإختيارات المعطاة فقط',
                        'division.required' => 'قم بإختيار الشعبة',
                        'division.boolean' => 'قم بإختيار الشعبة بشكل صحيح',
                    ]);
                }
            }else if($request->level == 4){
                if($request->sub_language != ""){
                    $smallValidation = Validator::make($request->all(), [
                        'sub_language' => 'boolean',
                    ], [
                        'sub_language.boolean' => 'قم بإختيار اللغة الثانية من بين الإختيارات المعطاة فقط',
                    ]);
                }
            }

            if (isset($smallValidation) && $smallValidation->fails()) {
                return redirect()->back()->withInput()->withErrors($smallValidation);
            }

            if ($students_must_choose_teachers) {
                if ($request->division !== null) {
                    $subjects = Subject::where([['level', 1], ['division', $request->division]])->orWhere('level', 2)->get();
                } else {
                    $subjects = Subject::where('level', 1)->orWhere('level', 2)->orWhereNull('level')->get();
                }

                $subjects_teachers = [];

                for ($i = 0; $i < count($subjects); $i++) {
                    $subject = $request->toArray()[$subjects[$i]['name_en']] ?? null;

                    if ($subject && $subject !== 'NULL') {
                        $subjects_teachers[$subjects[$i]['name_en']] = $subject;
                    }
                }

                if ($request->sub_language === '0') { // french
                    unset($subjects_teachers['germany']);
                } else { // germany
                    unset($subjects_teachers['french']);
                }
            }

            $subLanguage = null;
            $division = null;
            ($request->sub_language != 0 && $request->sub_language != 1) ? ($subLanguage = null) : ($subLanguage = $request->sub_language);
            ($request->division != 0 && $request->division != 1) ? ($division = null) : ($division = $request->division);

            $student->update([
                'name' => $request->name,
                'level_id' => $request->level,
                'mobile' => !empty($request->mobile) ? $request->mobile : null,
                'mobile2' => !empty($request->mobile2) ? $request->mobile2 : null,
                'student_mobile' => !empty($request->student_mobile) ? $request->student_mobile : null,
                'gender' => $request->gender,
                'given_money' => $request->given_money,
                'division' => $division,
                'sub_language' => $subLanguage,
                'edu_type' => $request->edu_type,
            ]);

            if ($students_must_choose_teachers) {
                StudentTeachers::where('student_id', $id)->updateOrCreate([
                    'student_id' => $student->id,
                ], [
                    'student_id' => $student->id,
                    'teachers' => $subjects_teachers
                ]);
            }
        }

        return redirect()->to(route('students.index'))->with(['success' => 'تم تحديث بيانات  الطالب بنجاح']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $student = Student::withTrashed()->findOrFail($request->id);
        Expenses::where('student_id', $student->id)->forceDelete();

        $student->forceDelete();
        
        if($request->has('redirect_to')){

            if($request->redirect_to === 'students_confirmation'){
                return redirect()->to(route('confirm_application'))->with(['success' => 'تم حذف الطالب بنجاح']);
            }

        }else{
            return redirect()->to(route('students.index'))->with(['success' => 'تم حذف الطالب بنجاح']);
        }
    }

    public function print($id)
    {
        $student = Student::findOrFail($id);

        return view('admin.students.print', compact('student'));
    }

    public function list(Request $request)
    {
        if (!$request->level) {
            abort(404);
        }

        $level = Level::findOrFail($request->level);

        $students = Student::where('level_id', $level->id)->orderBy('name', 'ASC')->get();

        return view('admin.students.list')->with(['students' => $students, 'level' => $level->name_ar]);
    }

    public function absence_list(Request $request)
    {
        if (!$request->level) {
            abort(404);
        }

        $level = Level::findOrFail($request->level);

        $students = Student::where('level_id', $level->id)
        ->orderBy('edu_type', 'DESC')
        ->orderBy('division', 'ASC')
        ->get();

        return view('admin.students.absence-list')->with(['students' => $students, 'level' => $level->name_ar]);
    }

    public function search_a(Request $request)
    {
        if (!empty($request->value)) {
            $value = $request->value;

            if(is_numeric($value)){
                if (!empty($request->levelId)) {
                    $students = Student::where([
                        ['name', 'like', '%' . $value . '%'],
                        ['level_id', $request->levelId]
                    ])
                        ->orWhere([
                            ['id', '=', $value],
                            ['level_id', $request->levelId]
                        ])
                        ->orWhere([
                            ['code', '=', $value],
                            ['level_id', $request->levelId]
                        ])
                        ->get();

                } else {
                    $students = Student::where('name', 'like', '%' . $value . '%')
                        ->orWhere('id', '=', $value)
                        ->orWhere('code', '=', $value)
                        ->select('id', 'name')
                        ->get();
                }
            }else{
                if (!empty($request->levelId)) {
                    $students = Student::where([
                        ['name', 'like', '%' . $value . '%'],
                        ['level_id', $request->levelId]
                    ])->get();

                } else {
                    $students = Student::where('name', 'like', '%' . $value . '%')
                    ->select('id', 'name')
                    ->get();
                }
            }

            return response()->json([
                'status' => true,
                'message' => null,
                'data' => $students
            ]);
        }
    }

    public function print_card($id)
    {
        $student = Student::findOrFail($id);
        
        return view('admin.students.print_card', compact('student'));
    }

    public function print_barcodes(Request $request)
    {
        if ($request->has('level') && !empty($request->level)) {
            $level = Level::findOrFail($request->level);

            $students = Student::where('level_id', $level->id)->select('level_id', 'id', 'code', 'name')->get();

            return view('admin.students.print_barcodes', compact('level', 'students'));
        }

        abort(404);
    }

    public function fill_absence_list(Request $request)
    {
        if($request->has('level') && $request->has('month')){
            $levelData = Level::findOrFail($request->level);

            $validation = Validator::make($request->all(), [
                'month' => 'required|numeric|min:1|max:12',
                'year' => 'required|numeric|min:' . (date('Y') - 10) . '|max:' . date('Y')
            ], [
                'month.required' => 'الشهر مطلوب',
                'month.numeric' => 'يجب أن يتكون التاريخ من أرقام فقط',
                'month.min' => 'يجب ألا يقل الشهر عن 1',
                'month.max' => 'يجب ألا يزيد الشهر عن 12',
                'year.required' => 'السنة مطلوبة',
                'year.numeric' => 'يجب أن تتكون السنة من أرقام فقط',
                'year.max' => 'يجب ألا تزيد السنة عن ' . date('Y'),
                'year.min' => 'يجب ألا تثل  السنة عن ' . (date('Y') - 10),
            ]);

            if($validation->fails()){
                return redirect()->back()->withErrors($validation);
            }

            $students = Student::where('level_id', $levelData->id)
            ->with('absence_list', 'expenses')
            ->orderBy('edu_type', 'DESC')
            ->orderBy('division', 'ASC')
            ->select('id', 'name', 'division', 'edu_type')
            ->get();

            $result = [];

            foreach($students as $student){
                $absenceList = $student->absence_list->filter(function ($q) use ($request) {
                    return explode("-", $q->join_at)[1] == $request->month; // record month
                })->toArray();

                $expensesStatus = $student->expenses->filter(function ($q) use ($request) {
                    return $q->month == $request->month;
                })->first();

                array_push($result, [
                    'student' => $student,
                    "absenceList" => $absenceList,
                    "expensesStatus" => $expensesStatus ? true : false
                ]);
            }

            return view('admin.solid_pages.filled-absence-list', compact('result', 'levelData'));
        }else{
            abort(404);
        }

    }

    public function filled_absence_list(Request $request)
    {
        $levels = Level::all();
        if($request->has('level') && $request->has('month')){
            Level::findOrFail($request->level);

            $validation = Validator::make($request->all(), [
                'month' => 'numeric|min:1|max:12'
            ], [
                'month.numeric' => 'يجب أن يتكون التاريخ من أرقام فقط',
                'month.min' => 'يجب ألا يقل الشهر عن 1',
                'month.max' => 'يجب ألا يزيد الشهر عن 12'
            ]);

            if($validation->fails()){
                abort(404);
            }

            return view('admin.students.filled-absence-list', compact('levels'));
        }else{
            return view('admin.students.filled-absence-list', compact('levels')); 
        }
    }

    public function confirm_application(StudentsConfirmationDataTable $table)
    {
        return $table->render('admin.students.confirm_application');
    }

    public function update_confirm_application($id)
    {
        $student = Student::withTrashed()->findOrFail($id);

        $student->restore();

        return redirect()->back()->with(['success' => 'تم إضافة الطالب بنجاح']);
    }
}
