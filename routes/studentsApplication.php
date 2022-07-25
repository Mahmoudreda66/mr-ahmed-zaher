<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentsApplication\StudentApplicationController;
use App\Http\Controllers\StudentsApplication\StudentApplication;

Route::group([
	"prefix" => "students-application",
	"as" => "studentsApplication."
], function () {
	Route::redirect('/', 'students-application/info');

	Route::get("info", [StudentApplicationController::class, "create"])->name('home');
	Route::post("store", [StudentApplication::class, "store"])->name('store');
});