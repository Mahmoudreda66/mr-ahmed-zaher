<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if(auth()->user()->hasPermission('show-dashboard')){
            return view('admin.dashboard');
        }else if(auth()->user()->hasRole('teacher') && !auth()->user()->hasPermission('show-dashboard') && auth()->user()->hasPermission('show-exams')){
            return redirect()->to(route('exams.index'));
        }else if(auth()->user()->hasRole('assistant') && !auth()->user()->hasPermission('show-dashboard') && auth()->user()->hasPermission('show-students')){
            return redirect()->to(route('students.index'));
        }else{
            auth()->logout();
            return redirect()->to(route('login'))->with(['error' => 'انت لا تملك اي صلاحيات بالدخول هنا']);
        }
    }
}
