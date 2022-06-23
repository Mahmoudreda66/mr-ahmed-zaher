<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Exams\AuthController;
use App\Http\Controllers\Exams\ExamController;
use App\Http\Controllers\Exams\ExamsAnswersController;
use App\Models\Admin\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

Route::group(['prefix' => 'students/exams'], function () {

	// caching the app name
    if(Schema::hasTable('settings')){
        cache()->add('app_name', \App\Models\Admin\Settings::where('name', 'place_name')->select('value')->first()['value'], 68400);
    }

	// auth routes
	Route::get('login', [AuthController::class, 'showLogin'])->name('exams.show-login');
	Route::post('login', [AuthController::class, 'login'])->name('exams.login');
	Route::post('login-by-id/{id}', function ($id) {
		$student = Student::findOrFail($id);

		Auth::guard('students')->logout();
		Auth::guard('students')->login($student);
		return redirect()->to(route('students.exams.index'));
	})->name('login_by_id');

	Route::group(['middleware' => 'examsAuth', 'as' => 'students.'], function () {

		// logout route
		Route::post('logout', [AuthController::class, 'logout'])->name('logout');

		// index route
		Route::get('', [ExamController::class, 'index'])->name('exams.index');

		// results index route
		Route::get('results', [ExamController::class, 'results_index'])->name('results.index');

		// show exams results route
		Route::get('results/{exam}', [ExamController::class, 'show_result'])->name('exams.results');

		// show exam route
		Route::get('{id}', [ExamController::class, 'show_exam'])->name('exams.show');

		// submit exam route
		Route::post('{id}', [ExamsAnswersController::class, 'store'])->name('exams.submit');

		// set student enter route
		Route::post('{exam}/enter', [ExamController::class, 'student_enter']);
	});
});