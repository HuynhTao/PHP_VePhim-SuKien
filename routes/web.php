<?php

use App\Http\Controllers\Admin\DiaDiemController;
use App\Http\Controllers\Admin\GheNgoiController;
use App\Http\Controllers\Admin\PhongChieuController;
use App\Http\Controllers\Admin\SuatController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DatVeController;
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

// ===== M4 – Đặt vé =====
Route::get('/dat-ve', [DatVeController::class, 'index'])->name('datve.index');
Route::middleware(['auth', 'role:admin,user'])->prefix('dat-ve')->name('datve.')->group(function () {
    Route::get('suat/{suat}', [DatVeController::class, 'chonGhe'])->name('chon-ghe');
    Route::get('suat/{suat}/trang-thai', [DatVeController::class, 'trangThai'])->name('trang-thai');
    Route::post('suat/{suat}/giu-ghe', [DatVeController::class, 'giuGhe'])->middleware('throttle:20,1')->name('giu-ghe');
    Route::get('don-hang/{donHang}/thanh-toan', [DatVeController::class, 'thanhToan'])->name('thanh-toan');
    Route::post('don-hang/{donHang}/thanh-toan', [DatVeController::class, 'xacNhan'])->name('thanh-toan.xac-nhan');
    Route::post('don-hang/{donHang}/huy', [DatVeController::class, 'huy'])->name('huy');
    Route::get('don-hang/{donHang}/thanh-cong', [DatVeController::class, 'thanhCong'])->name('thanh-cong');
});
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
