<?php

namespace Tests\Feature;

use App\Models\DiaDiem;
use App\Models\DonHang;
use App\Models\GheNgoi;
use App\Models\GheTheoSuat;
use App\Models\NoiDung;
use App\Models\Phim;
use App\Models\PhongChieu;
use App\Models\Suat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatVeTest extends TestCase
{
    use RefreshDatabase;

    private function taoSuatMau(): array
    {
        $rap = DiaDiem::create([
            'ten_dia_diem' => 'CineViet Test',
            'dia_chi' => '123 Nguyễn Huệ',
            'tinh_thanh' => 'TP. Hồ Chí Minh',
            'trang_thai' => 'hoat_dong',
        ]);
        $phong = PhongChieu::create([
            'dia_diem_id' => $rap->id,
            'ten_phong' => 'Phòng 01',
            'loai_phong' => '2D',
            'trang_thai' => 'hoat_dong',
        ]);
        $ghe1 = GheNgoi::create([
            'phong_chieu_id' => $phong->id,
            'hang_ghe' => 'A',
            'so_ghe' => 1,
            'loai_ghe' => 'thuong',
            'dang_su_dung' => true,
        ]);
        $ghe2 = GheNgoi::create([
            'phong_chieu_id' => $phong->id,
            'hang_ghe' => 'A',
            'so_ghe' => 2,
            'loai_ghe' => 'vip',
            'dang_su_dung' => true,
        ]);
        $noiDung = NoiDung::create([
            'loai_noi_dung' => 'phim',
            'tieu_de' => 'Trại Giam Hạnh Phúc',
            'mo_ta' => 'Phim mẫu test',
            'trang_thai' => 'cong_bo',
        ]);
        Phim::create([
            'noi_dung_id' => $noiDung->id,
            'thoi_luong_phut' => 120,
            'gioi_han_tuoi' => 'T16',
            'ngon_ngu' => 'Tiếng Việt',
            'the_loai' => 'Chính kịch',
            'ngay_khoi_chieu' => today(),
        ]);
        $suat = Suat::create([
            'noi_dung_id' => $noiDung->id,
            'phong_chieu_id' => $phong->id,
            'bat_dau_luc' => now()->addDay()->setTime(19, 30),
            'ket_thuc_luc' => now()->addDay()->setTime(21, 30),
            'trang_thai' => 'mo_ban',
        ]);
        $gts1 = GheTheoSuat::create([
            'suat_id' => $suat->id,
            'ghe_ngoi_id' => $ghe1->id,
            'gia_ve' => 90000,
            'trang_thai' => 'trong',
        ]);
        $gts2 = GheTheoSuat::create([
            'suat_id' => $suat->id,
            'ghe_ngoi_id' => $ghe2->id,
            'gia_ve' => 120000,
            'trang_thai' => 'trong',
        ]);

        return [$suat, $gts1, $gts2, $noiDung];
    }

    public function test_khach_chua_dang_nhap_xem_duoc_so_do_ghe_va_sau_khi_dang_nhap_quay_lai_dung_suat(): void
    {
        [$suat, $gts1] = $this->taoSuatMau();

        // Khách chưa đăng nhập bấm "Mua vé" -> xem được sơ đồ ghế (HTTP 200)
        $res = $this->get(route('datve.chon-ghe', $suat));
        $res->assertOk();
        $res->assertSee('Đăng nhập để giữ ghế');

        // Khách xem được trạng thái ghế realtime qua AJAX
        $this->getJson(route('datve.trang-thai', $suat))
            ->assertOk()
            ->assertJsonStructure(['ghes', 'luc']);

        // Nếu khách cố gửi POST giữ ghế khi chưa đăng nhập -> chuyển sang login kèm intended là GET chọn ghế
        $this->post(route('datve.giu-ghe', $suat), ['ghe_ids' => [$gts1->id]])
            ->assertRedirect(route('login'));

        $user = User::create([
            'ho_ten' => 'Khách Đặt Vé',
            'email' => 'khach@example.com',
            'mat_khau' => 'User@123456',
            'vai_tro' => 'user',
        ]);

        // Sau khi đăng nhập -> chuyển hướng về lại đúng trang chọn ghế của suất đó (không bị lỗi 405)
        $this->post(route('login.store'), [
            'email' => 'khach@example.com',
            'password' => 'User@123456',
        ])->assertRedirect(route('datve.chon-ghe', $suat));
    }

    public function test_luong_dat_ve_thanh_toan_lich_su_va_huy_ve_hoan_chinh(): void
    {
        [$suat, $gts1, $gts2] = $this->taoSuatMau();
        $user = User::create([
            'ho_ten' => 'Nguyễn Văn A',
            'email' => 'nguyenvana@example.com',
            'mat_khau' => 'User@123456',
            'vai_tro' => 'user',
        ]);

        // 1. Giữ ghế
        $resGiu = $this->actingAs($user)->post(route('datve.giu-ghe', $suat), [
            'ghe_ids' => [$gts1->id, $gts2->id],
        ]);
        $don = DonHang::firstOrFail();
        $resGiu->assertRedirect(route('datve.thanh-toan', $don));
        $this->assertEquals('cho_thanh_toan', $don->trang_thai);
        $this->assertEquals(210000, (float) $don->tong_tien);

        // 2. Người thứ 2 không thể đặt trùng ghế đang giữ
        $user2 = User::create([
            'ho_ten' => 'Người Thứ Hai',
            'email' => 'user2@example.com',
            'mat_khau' => 'User@123456',
            'vai_tro' => 'user',
        ]);
        $this->actingAs($user2)->post(route('datve.giu-ghe', $suat), [
            'ghe_ids' => [$gts1->id],
        ])->assertSessionHasErrors('ghe');

        // 3. Thanh toán MoMo
        $this->actingAs($user)->post(route('datve.thanh-toan.xac-nhan', $don), [
            'phuong_thuc' => 'momo',
        ])->assertRedirect(route('datve.thanh-cong', $don));

        $don->refresh();
        $this->assertEquals('da_thanh_toan', $don->trang_thai);
        $this->assertEquals(2, $don->ves()->count());
        $this->assertEquals('da_ban', $gts1->fresh()->trang_thai);

        // 4. Xem lịch sử đặt vé & hủy vé trước giờ chiếu > 2 tiếng (M5)
        $this->actingAs($user)->get(route('orders.index'))->assertOk()->assertSee($don->ma_don_hang);
        $this->actingAs($user)->get(route('orders.show', $don))->assertOk()->assertSee('Hủy đơn');

        $this->actingAs($user)->post(route('orders.cancel', $don))
            ->assertRedirect(route('orders.show', $don));

        $this->assertEquals('da_huy', $don->fresh()->trang_thai);
        $this->assertEquals('trong', $gts1->fresh()->trang_thai);
    }

    public function test_quan_tri_dashboard_don_hang_va_tim_kiem_nhanh(): void
    {
        [, , , $noiDung] = $this->taoSuatMau();
        $admin = User::create([
            'ho_ten' => 'Admin Test',
            'email' => 'admintest@example.com',
            'mat_khau' => 'Admin@123456',
            'vai_tro' => 'admin',
        ]);

        $this->get(route('noi-dung.show', $noiDung))->assertOk()->assertSee('Trại Giam Hạnh Phúc');
        $this->getJson(route('noi-dung.search', ['q' => 'Trại Giam']))->assertOk()->assertJsonCount(1);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Dashboard thống kê');
        $this->actingAs($admin)->get(route('admin.orders.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.noidung.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.dia-diem.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.phong-chieu.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.suat.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
    }
}
