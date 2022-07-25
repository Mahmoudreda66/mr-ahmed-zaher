<?php

namespace App\Http\Controllers\StudentsApplication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Level;
use App\Models\Admin\Settings;

class StudentApplicationController extends Controller
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
        $levels = Level::get();

        $appliactionStatus = Settings::where('name', 'enable_students_online_application')
        ->select('value')
        ->first()['value'];

        $confirmationStatus = Settings::where('name', 'must_confirm_students_application')
        ->select('value')
        ->first()['value'];

        $centerPhoneNumber = Settings::where('name', 'center_phone1')
        ->select('value')
        ->first()['value'];

        return view('studentsApplication.create',
            compact('levels', 'appliactionStatus', 'centerPhoneNumber', 'confirmationStatus'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
