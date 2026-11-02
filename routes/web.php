<?php

use App\Http\Controllers\Admin\DiaDiemController;
use App\Http\Controllers\Admin\GheNgoiController;
use App\Http\Controllers\Admin\PhongChieuController;
use App\Http\Controllers\Admin\SuatController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

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
    Route::resource('dia-diem', DiaDiemController::class)
        ->parameters(['dia-diem' => 'diaDiem'])
        ->except(['show']);
    Route::get('dia-diem/{diaDiem}/ban-do', [DiaDiemController::class, 'map'])->name('dia-diem.map');
    Route::resource('phong-chieu', PhongChieuController::class)->parameters(['phong-chieu' => 'phongChieu']);
    Route::post('phong-chieu/{phongChieu}/sinh-ghe', [GheNgoiController::class, 'generate'])->name('ghe.generate');
    Route::post('phong-chieu/{phongChieu}/ghe', [GheNgoiController::class, 'store'])->name('ghe.store');
    Route::put('phong-chieu/{phongChieu}/ghe/{gheNgoi}', [GheNgoiController::class, 'update'])->name('ghe.update');
    Route::delete('phong-chieu/{phongChieu}/ghe/{gheNgoi}', [GheNgoiController::class, 'destroy'])->name('ghe.destroy');
    Route::resource('suat', SuatController::class);

});
