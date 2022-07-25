<?php

namespace App\Http\Controllers\Videos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Student;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        if(auth('videos')->check()){
            return redirect()->to(route('videos.index'));
        }

        return view('videos.auth.login');
    }

    public function attempt(Request $request)
    {
        if(auth('videos')->check()){ // redirect if the student is logged in
            return redirect()->to(route('videos.index'));
        }

        $request->validate([ // validate request
            'code' => 'required|numeric|max:99999999|min:10000000'
        ], [
            'code.required' => 'قم بإدخال الكود الخاص بك',
            'code.numeric' => 'قم بإدخال الكود الخاص بك بشكل صحيح',
            'code.max' => 'قم بإدخال الكود الخاص بك بشكل صحيح',
            'code.min' => 'قم بإدخال الكود الخاص بك بشكل صحيح'
        ]);

        $student = Student::where('code', $request->code)->first();

        if($student){

            Auth::guard('videos')->login($student);

            return redirect()->to(route('videos.index'));

        }else{
            return redirect()->back()->withErrors(['code' => 'لم يتم العثور على الكود']);
        }
    }

    public function logout()
    {
        if(auth('videos')->check()){
            Auth::guard('videos')->logout();
        }

        return redirect()->to(route('videos.index'));
    }
}
