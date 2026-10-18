<?php

namespace Database\Seeders;

use App\Models\{User, DiaDiem, PhongChieu, GheNgoi, NoiDung, Phim, SuKien, Suat, GheTheoSuat, DonHang, Ve, ThanhToan};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('DemoSeeder chỉ được chạy trong môi trường local/testing.');
        }
        DB::transaction(function () {
            User::firstOrCreate(['email' => 'admin@example.com'], [
                'ho_ten' => 'Quản trị viên', 'mat_khau' => 'Admin@123456', 'vai_tro' => 'admin',
                'so_dien_thoai' => '0900000001', 'email_verified_at' => now(),
            ]);
            $user = User::firstOrCreate(['email' => 'user@example.com'], [
                'ho_ten' => 'Người dùng mẫu', 'mat_khau' => 'User@123456', 'vai_tro' => 'user',
                'so_dien_thoai' => '0900000002',
            ]);
            $venue = DiaDiem::firstOrCreate(['ten_dia_diem' => 'CineViet Central'], [
                'dia_chi' => '123 Nguyễn Huệ, Quận 1', 'tinh_thanh' => 'TP. Hồ Chí Minh',
                'so_dien_thoai' => '02800000000', 'trang_thai' => 'hoat_dong',
            ]);
            $rooms = collect(['Phòng chiếu 01' => '2D', 'Khán phòng sự kiện' => 'san_khau'])->map(function ($type, $name) use ($venue) {
                $room = PhongChieu::firstOrCreate(['dia_diem_id' => $venue->id, 'ten_phong' => $name], ['loai_phong' => $type, 'trang_thai' => 'hoat_dong']);
                foreach (['A', 'B', 'C'] as $row) {
                    for ($number = 1; $number <= 8; $number++) {
                        GheNgoi::firstOrCreate(['phong_chieu_id' => $room->id, 'hang_ghe' => $row, 'so_ghe' => $number], ['loai_ghe' => $row === 'C' ? 'vip' : 'thuong', 'dang_su_dung' => true]);
                    }
                }
                return $room;
            })->values();
            $items = [
                ['Chuyến đi mùa hạ', 'phim', 'Hành trình trở về quê nhà mở ra những cuộc gặp gỡ và một mùa hè đáng nhớ.', 105, 90000],
                ['Phía bên kia thành phố', 'phim', 'Một câu chuyện về tình bạn và những người trẻ tìm kiếm hướng đi cho riêng mình.', 118, 100000],
                ['Đêm nhạc dưới ánh sao', 'su_kien', 'Không gian acoustic ấm áp với những giai điệu quen thuộc và các nghệ sĩ trẻ.', 120, 250000],
            ];
            foreach ($items as $i => [$title, $type, $description, $duration, $price]) {
                $content = NoiDung::firstOrCreate(['tieu_de' => $title, 'loai_noi_dung' => $type], ['mo_ta' => $description, 'trang_thai' => 'cong_bo']);
                if ($type === 'phim') {
                    Phim::firstOrCreate(['noi_dung_id' => $content->id], ['thoi_luong_phut' => $duration, 'gioi_han_tuoi' => 'P', 'ngon_ngu' => 'Tiếng Việt', 'ngay_khoi_chieu' => today()]);
                } else {
                    SuKien::firstOrCreate(['noi_dung_id' => $content->id], ['don_vi_to_chuc' => 'CineViet Studio', 'the_loai' => 'Âm nhạc', 'gioi_han_tuoi' => 'P']);
                }
                $room = $rooms[$type === 'phim' ? 0 : 1];
                $start = now()->addDays($i + 1)->setTime(19, 0);
                $session = Suat::firstOrCreate(['noi_dung_id' => $content->id, 'phong_chieu_id' => $room->id], ['bat_dau_luc' => $start, 'ket_thuc_luc' => $start->copy()->addMinutes($duration), 'trang_thai' => 'mo_ban']);
                foreach ($room->gheNgois as $seat) {
                    GheTheoSuat::firstOrCreate(['suat_id' => $session->id, 'ghe_ngoi_id' => $seat->id], ['gia_ve' => $price + ($seat->loai_ghe === 'vip' ? 30000 : 0), 'trang_thai' => 'trong']);
                }
                if ($i === 0 && ! DonHang::where('ma_don_hang', 'DEMO-0001')->exists()) {
                    $seat = $session->gheTheoSuats()->where('trang_thai', 'trong')->whereDoesntHave('ve')->lockForUpdate()->first();
                    if (! $seat) {
                        continue;
                    }
                    $order = DonHang::create(['nguoi_dung_id' => $user->id, 'ma_don_hang' => 'DEMO-0001', 'trang_thai' => 'da_thanh_toan', 'tam_tinh' => $seat->gia_ve, 'tien_giam' => 0, 'tong_tien' => $seat->gia_ve, 'dat_luc' => now()]);
                    $seat->update(['trang_thai' => 'da_ban']);
                    Ve::create(['don_hang_id' => $order->id, 'ghe_theo_suat_id' => $seat->id, 'ma_ve' => 'DEMO-VE-0001', 'trang_thai' => 'hieu_luc', 'gia_ban' => $seat->gia_ve, 'phat_hanh_luc' => now()]);
                    ThanhToan::create(['don_hang_id' => $order->id, 'nha_cung_cap' => 'demo', 'ma_giao_dich' => 'DEMO-TT-0001', 'so_tien' => $seat->gia_ve, 'trang_thai' => 'thanh_cong', 'thanh_toan_luc' => now(), 'du_lieu_phan_hoi' => ['du_lieu_mau' => true]]);
                }
            }
        });
    }
}
