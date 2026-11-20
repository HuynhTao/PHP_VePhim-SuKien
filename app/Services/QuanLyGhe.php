<?php

namespace App\Services;

use App\Models\GheNgoi;
use App\Models\PhongChieu;
use Illuminate\Validation\ValidationException;

class QuanLyGhe
{
    // Gọi trong transaction, sau khi khóa phòng: giữ sơ đồ nhất quán với các suất.
    public function assertEditable(PhongChieu $phong): void
    {
        if ($phong->suats()->exists() || $phong->gheNgois()->whereHas('gheTheoSuats')->exists()) {
            throw ValidationException::withMessages(['ghe' => 'Phòng đã có suất: không thể thay đổi sơ đồ ghế. Hãy dùng phòng mới nếu cần sơ đồ khác.']);
        }
    }

    public function generate(PhongChieu $phong, array $data): void
    {
        $this->assertEditable($phong);
        $phong->gheNgois()->delete();
        for ($row = 0; $row < $data['so_hang']; $row++) {
            for ($number = 1; $number <= $data['ghe_moi_hang']; $number++) {
                GheNgoi::create([
                    'phong_chieu_id' => $phong->id,
                    'hang_ghe' => chr(65 + $row), 'so_ghe' => $number,
                    'loai_ghe' => $row >= $data['so_hang'] - $data['so_hang_vip'] ? 'vip' : 'thuong',
                    'dang_su_dung' => true,
                ]);
            }
        }
    }
}
