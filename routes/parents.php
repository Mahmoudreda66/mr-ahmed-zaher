<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Parents\AuthController;
use App\Http\Controllers\Parents\HomeController;

Route::group(['prefix' => 'parents', 'as' => 'parents.'], function () {
	Route::get('login', [AuthController::class, 'login'])->name('login');
	Route::post('login', [AuthController::class, 'check_login'])->name('check_login');
	Route::post('logout', [AuthController::class, 'logout'])->name('logout');

	Route::redirect('', '/parents/home');

	Route::group(['middleware' => 'parentsAuth'], function () {
		Route::get('home', [HomeController::class, 'index'])->name('home');
	});
});