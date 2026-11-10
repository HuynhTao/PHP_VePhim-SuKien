<?php

namespace Database\Seeders;

use App\Models\DiaDiem;
use App\Models\NoiDung;
use App\Models\Phim;
use App\Models\PhongChieu;
use App\Models\SuKien;
use App\Services\QuanLyGhe;
use App\Services\QuanLySuat;
use Illuminate\Database\Seeder;
use Illuminate\Validation\ValidationException;

class DemoRapLonSeeder extends Seeder
{
    public function run(): void
    {
        $rap = DiaDiem::firstOrCreate(['ten_dia_diem' => 'CineViet Landmark'], [
            'dia_chi' => '720A Điện Biên Phủ, Bình Thạnh', 'tinh_thanh' => 'TP. Hồ Chí Minh',
            'so_dien_thoai' => '02811112222', 'trang_thai' => 'hoat_dong',
        ]);

        // [tên phòng, loại, số hàng, ghế mỗi hàng, số hàng VIP]
        $cauHinh = [
            ['IMAX Hall 1', 'IMAX', 10, 16, 3],
            ['Phòng 3D 02', '3D', 8, 14, 2],
            ['Khán phòng lớn', 'san_khau', 10, 18, 3],
        ];
        $phongs = [];
        foreach ($cauHinh as [$ten, $loai, $hang, $cot, $vip]) {
            $p = PhongChieu::firstOrCreate(
                ['dia_diem_id' => $rap->id, 'ten_phong' => $ten],
                ['loai_phong' => $loai, 'trang_thai' => 'hoat_dong']
            );
            if ($p->wasRecentlyCreated) {
                app(QuanLyGhe::class)->generate($p, ['so_hang' => $hang, 'ghe_moi_hang' => $cot, 'so_hang_vip' => $vip]);
            }
            $phongs[$loai] = $p;
        }

        // Thêm phim và sự kiện
        $phims = [
            ['Mưa trên phố cũ', 112, 'P', 'Chuyện tình lặng lẽ giữa hai người xa lạ trong những cơn mưa Sài Gòn.'],
            ['Hành tinh cuối cùng', 128, 'T13', 'Chuyến phiêu lưu vũ trụ của một phi hành đoàn tìm nơi ở mới cho loài người.'],
            ['Bí mật căn gác', 98, 'T16', 'Một gia đình chuyển nhà và phát hiện căn gác khóa kín giấu điều không ngờ.'],
            ['Gánh xiếc rong', 105, 'P', 'Hành trình của đoàn xiếc nhỏ đi khắp miền Tây mang tiếng cười đến mọi nhà.'],
            ['Đường đua rực lửa', 120, 'T13', 'Tay đua trẻ quay lại đường đua để giành lại danh dự cho đội của mình.'],
            ['Cô gái đến từ ngày mai', 115, 'T13', 'Cô gái nhận ra mình có thể nhìn thấy trước một ngày và bắt đầu thay đổi tương lai.'],
        ];
        foreach ($phims as [$ten, $phut, $tuoi, $moTa]) {
            $nd = NoiDung::firstOrCreate(['tieu_de' => $ten, 'loai_noi_dung' => 'phim'], ['mo_ta' => $moTa, 'trang_thai' => 'cong_bo']);
            Phim::firstOrCreate(['noi_dung_id' => $nd->id], [
                'thoi_luong_phut' => $phut, 'gioi_han_tuoi' => $tuoi, 'ngon_ngu' => 'Tiếng Việt', 'ngay_khoi_chieu' => today(),
            ]);
        }
        $sk = NoiDung::firstOrCreate(['tieu_de' => 'Liveshow Mùa thu xa', 'loai_noi_dung' => 'su_kien'], [
            'mo_ta' => 'Đêm nhạc với dàn nghệ sĩ trẻ cùng ban nhạc sống, không gian ấm áp đầy cảm xúc.', 'trang_thai' => 'cong_bo',
        ]);
        SuKien::firstOrCreate(['noi_dung_id' => $sk->id], ['don_vi_to_chuc' => 'CineViet Studio', 'the_loai' => 'Âm nhạc', 'gioi_han_tuoi' => 'P']);

        // Tự gán poster nếu có file public/images/posters/{id}.jpg|png|webp
        foreach (NoiDung::all() as $nd) {
            foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                if (file_exists(public_path("images/posters/{$nd->id}.$ext"))) {
                    $nd->update(['anh_bia' => "images/posters/{$nd->id}.$ext"]);
                    break;
                }
            }
        }

        // Tạo suất 5 ngày tới
        $service = app(QuanLySuat::class);
        $dsPhim = NoiDung::with('phim')->where('trang_thai', 'cong_bo')->where('loai_noi_dung', 'phim')->orderBy('id')->get()->values();
        $dsSuKien = NoiDung::where('trang_thai', 'cong_bo')->where('loai_noi_dung', 'su_kien')->orderBy('id')->get()->values();
        $gio = [[10, 0], [13, 30], [17, 0], [20, 30]];

        for ($ngay = 0; $ngay < 5; $ngay++) {
            foreach ($gio as $s => [$h, $m]) {
                $this->luu($service, $dsPhim[($s + $ngay) % $dsPhim->count()], $phongs['IMAX'], $ngay, $h, $m, 110000, 160000);
                $this->luu($service, $dsPhim[($s + $ngay + 1) % $dsPhim->count()], $phongs['3D'], $ngay, $h, $m, 95000, 135000);
            }
            foreach ($dsSuKien as $k => $e) {
                $this->luu($service, $e, $phongs['san_khau'], $ngay, 19 + $k * 0, 0, 250000, 350000, 150);
            }
        }
    }

    private function luu(QuanLySuat $service, NoiDung $nd, PhongChieu $phong, int $ngay, int $h, int $m, int $gia, int $giaVip, ?int $phutMacDinh = null): void
    {
        $bd = now()->addDays($ngay)->setTime($h, $m);
        if ($bd->isPast()) {
            return;
        }
        $phut = $nd->phim->thoi_luong_phut ?? $phutMacDinh ?? 120;
        try {
            $service->save([
                'noi_dung_id' => $nd->id, 'phong_chieu_id' => $phong->id,
                'bat_dau_luc' => $bd->format('Y-m-d\TH:i'),
                'ket_thuc_luc' => $bd->copy()->addMinutes($phut)->format('Y-m-d\TH:i'),
                'trang_thai' => 'mo_ban', 'gia_thuong' => $gia, 'gia_vip' => $giaVip,
            ]);
        } catch (ValidationException $e) {
            // đã tồn tại / trùng lịch -> bỏ qua
        }
    }
}