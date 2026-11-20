<?php

namespace Database\Seeders;

use App\Models\NoiDung;
use App\Models\Phim;
use App\Models\SuKien;
use Illuminate\Database\Seeder;

/** Dữ liệu mẫu cho M2: có phim đang chiếu, phim sắp chiếu và 1 sự kiện. */
class M2PhimSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('M2PhimSeeder chỉ chạy ở môi trường local/testing.');
        }

        $phims = [
            ['Hành tinh ngủ say', 'Một nhóm nhà khoa học đánh thức hành tinh cổ xưa và phải trả giá.', 112, 'T13', 'Tiếng Việt', 'Khoa học viễn tưởng', today()->subDays(10)],
            ['Mùa mưa trở lại', 'Chuyện tình lặng lẽ của hai người xa lạ trong những ngày mưa Sài Gòn.', 98, 'P', 'Tiếng Việt', 'Tình cảm', today()->subDays(3)],
            ['Thành phố không ngủ', 'Một đêm dài, một vụ án và những lựa chọn không thể quay lại.', 124, 'T16', 'Tiếng Anh - Phụ đề Việt', 'Hình sự', today()->addDays(7)],
            ['Bản nhạc cuối cùng', 'Người nghệ sĩ già quay lại sân khấu lần cuối để giữ lời hứa cũ.', 105, 'T13', 'Tiếng Việt', 'Chính kịch', today()->addDays(14)],
        ];

        foreach ($phims as [$ten, $moTa, $phut, $tuoi, $ngonNgu, $theLoai, $ngay]) {
            $nd = NoiDung::firstOrCreate(
                ['tieu_de' => $ten, 'loai_noi_dung' => 'phim'],
                ['mo_ta' => $moTa, 'trang_thai' => 'cong_bo']
            );
            Phim::firstOrCreate(['noi_dung_id' => $nd->id], [
                'thoi_luong_phut' => $phut, 'gioi_han_tuoi' => $tuoi,
                'ngon_ngu' => $ngonNgu, 'the_loai' => $theLoai, 'ngay_khoi_chieu' => $ngay,
            ]);
        }

        $sk = NoiDung::firstOrCreate(
            ['tieu_de' => 'Liên hoan âm nhạc cuối năm', 'loai_noi_dung' => 'su_kien'],
            ['mo_ta' => 'Đêm nhạc quy tụ nhiều nghệ sĩ trẻ, không gian mở và giao lưu cùng khán giả.', 'trang_thai' => 'cong_bo']
        );
        SuKien::firstOrCreate(['noi_dung_id' => $sk->id], [
            'don_vi_to_chuc' => 'CineViet Studio', 'the_loai' => 'Âm nhạc', 'gioi_han_tuoi' => 'P',
        ]);
    }
}
