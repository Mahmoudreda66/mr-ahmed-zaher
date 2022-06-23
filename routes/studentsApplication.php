<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentsApplication\StudentApplicationController;

Route::group([
	"prefix" => "students-application",
	"as" => "studentsApplication."
], function () {
	Route::get("info", [StudentApplicationController::class, "create"])->name('home');
});