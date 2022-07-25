<?php

use App\Http\Controllers\Videos\VideoController;
use App\Http\Controllers\Videos\AuthController;

Route::group([
	'as' => 'videos.',
	'prefix' => 'videos'
], function () {

	Route::redirect('/', '/videos/index');

	Route::get('login', [AuthController::class, 'login'])->name('login');
	
	Route::post('login', [AuthController::class, 'attempt'])->name('attempt_login');

	Route::group(['middleware' => 'videosAuth'], function () {
		Route::post('logout', [AuthController::class, 'logout'])->name('logout');
		Route::get('index', [VideoController::class, 'index'])->name('index');
		Route::get('play/{video}', [VideoController::class, 'show'])->name('show');
	});

});