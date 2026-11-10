<?php

namespace Database\Seeders;

use App\Models\NoiDung;
use App\Models\PhongChieu;
use App\Services\QuanLySuat;
use Illuminate\Database\Seeder;
use Illuminate\Validation\ValidationException;

class DemoSuatSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(QuanLySuat::class);
        $contents = NoiDung::with('phim')->where('trang_thai', 'cong_bo')->orderBy('id')->get();

        foreach ($contents as $k => $content) {
            $phong = PhongChieu::where('loai_phong', $content->loai_noi_dung === 'phim' ? '!=' : '=', 'san_khau')
                ->where('trang_thai', 'hoat_dong')->orderBy('id')->first();
            if (! $phong) {
                continue;
            }
            $phut = $content->phim->thoi_luong_phut ?? 120;
            $gio = $content->loai_noi_dung === 'phim' ? ($k % 2 === 0 ? [9, 15, 21] : [12, 18]) : [19];

            for ($ngay = 0; $ngay < 5; $ngay++) {
                foreach ($gio as $h) {
                    $bd = now()->addDays($ngay)->setTime($h, 0);
                    try {
                        $service->save([
                            'noi_dung_id' => $content->id, 'phong_chieu_id' => $phong->id,
                            'bat_dau_luc' => $bd->format('Y-m-d\TH:i'),
                            'ket_thuc_luc' => $bd->copy()->addMinutes($phut)->format('Y-m-d\TH:i'),
                            'trang_thai' => 'mo_ban', 'gia_thuong' => 90000, 'gia_vip' => 120000,
                        ]);
                    } catch (ValidationException $e) {
                        // trùng lịch / đã tồn tại -> bỏ qua
                    }
                }
            }
        }
    }
}