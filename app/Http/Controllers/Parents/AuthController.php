<?php

namespace App\Http\Controllers\Parents;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Student;
use App\Models\Admin\Settings;

class AuthController extends Controller
{
    public function login()
    {
        if (auth('parents')->check()) {
            return redirect()->to(route('parents.home'));
        }

        $logo = Settings::where('name', 'center_logo')->first()['value'];

        return view('parents.auth.login', compact('logo'));
    }

    public function check_login(Request $request)
    {
        if (auth('parents')->check()) {
            return redirect()->to(route('parents.home'));
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
            Auth::guard('parents')->login($student);
            return redirect()->to(route('parents.home'));
        }

        return redirect()->back()->with(['error' => 'لم يتم العثور على هذا الكود']);
    }

    public function logout()
    {
        if (auth('parents')->check()) {
            auth('parents')->logout();
            return redirect()->to(route('parents.login'));
        }

        return redirect()->back();
    }
}
