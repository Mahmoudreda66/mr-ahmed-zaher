<?php

namespace App\Http\Controllers\Exams;

use App\Http\Controllers\Controller;
use App\Models\Admin\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Settings;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (auth('students')->check()) {
            return redirect()->to(route('parents.home'));
        }

        $logo = Settings::where('name', 'center_logo')->first()['value'];

        return view('exams.auth.login', compact('logo'));
    }

    public function login(Request $request)
    {
        if (auth('students')->check()) {
            return redirect()->to(route('students.home'));
        }
        
        $request->validate([
            'code' => 'required|numeric|max:99999999|min:10000000'
        ], [
            'code.required' => 'قم بإدخال الكود الخاص بك',
            'code.numeric' => 'قم بإدخال الكود الخاص بك بشكل صحيح',
            'code.max' => 'قم بإدخال الكود الخاص بك بشكل صحيح',
            'code.min' => 'قم بإدخال الكود الخاص بك بشكل صحيح'
        ]);

        $student = Student::where('code', $request->code)->first();
        if ($student) {
            Auth::guard('students')->login($student);
            return redirect()->to(route('students.exams.index'));
        }

        return redirect()->back()->with(['error' => 'لم يتم العثور على هذا الكود']);
    }

    public function logout()
    {
        if (auth('students')->check()) {
            auth('students')->logout();
            return redirect()->to(route('exams.show-login'));
        }

        return redirect()->back();
    }
}
