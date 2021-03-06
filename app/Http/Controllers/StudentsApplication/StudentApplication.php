<?php

namespace App\Http\Controllers\StudentsApplication;

use App\Http\Controllers\Controller;
use App\Models\Admin\Student;
use App\Models\Admin\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentApplication extends Controller
{
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

        $code = rand(10, 99) . rand(10, 99) . rand(10, 99) . rand(10, 99);
        $exists = Student::where('code', $code)->select('code')->first();

        do {
            $code = rand(10, 99) . rand(10, 99) . rand(10, 99) . rand(10, 99);
        } while ($exists);

        $deleted_at = null;

        $confirmationStatus = Settings::where('name', 'must_confirm_students_application')->first()['value'];

        if($confirmationStatus == 1){
            $deleted_at = now()->timestamp;
        }

        if (in_array($request->level, $preps)) { // prepratory student

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
                'user_id' => null,
                'deleted_at' => $deleted_at
            ]);

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
                'user_id' => null,
                'deleted_at' => $deleted_at
            ]);

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
            return redirect()->back()->with([
                'error' => 'طلب غير صالح'
            ]);
        }
    }
}
