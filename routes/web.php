<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/dang-ky', [AuthController::class, 'register'])->name('register');
    Route::post('/dang-ky', [AuthController::class, 'store'])->middleware('throttle:6,1')->name('register.store');
    Route::get('/dang-nhap', [AuthController::class, 'login'])->name('login');
    Route::post('/dang-nhap', [AuthController::class, 'authenticate'])->middleware('throttle:6,1')->name('login.store');
});

Route::post('/dang-xuat', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::view('/tai-khoan', 'auth.account')->middleware(['auth', 'role:admin,user'])->name('account');

Route::prefix('quan-tri')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::redirect('/', '/quan-tri/nguoi-dung');
    Route::resource('nguoi-dung', UserController::class)->parameters(['nguoi-dung' => 'user'])->names('users');
});
