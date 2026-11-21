<?php

namespace App\Services;

use App\Models\DonHang;
use App\Models\GheTheoSuat;
use App\Models\Suat;
use App\Models\ThanhToan;
use App\Models\User;
use App\Models\Ve;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DatVeService
{
    public const PHUT_GIU_GHE = 10;
    public const TOI_DA_GHE = 8;
    public const PHUONG_THUC = [
        'momo' => 'Ví MoMo',
        'vnpay' => 'VNPay QR',
        'the_ngan_hang' => 'Thẻ ATM / Visa / Mastercard',
    ];

    /** Nhả các ghế/đơn đã quá hạn giữ (gọi mỗi lần tải sơ đồ ghế). */
    public function nhaGheHetHan(?int $suatId = null): void
    {
        GheTheoSuat::where('trang_thai', 'dang_giu')->where('giu_den_luc', '<', now())
            ->when($suatId, fn ($q, $v) => $q->where('suat_id', $v))
            ->update(['trang_thai' => 'trong', 'giu_den_luc' => null, 'giu_boi_don_hang_id' => null]);
        DonHang::where('trang_thai', 'cho_thanh_toan')->where('het_han_luc', '<', now())
            ->update(['trang_thai' => 'het_han']);
    }

    /** Giữ ghế + tạo đơn chờ thanh toán. Chặn đặt trùng bằng khóa dòng. */
    public function giuGhe(User $user, Suat $suat, array $gheTheoSuatIds): DonHang
    {
        return DB::transaction(function () use ($user, $suat, $gheTheoSuatIds) {
            $suat = Suat::whereKey($suat->id)->lockForUpdate()->firstOrFail();
            if ($suat->trang_thai !== 'mo_ban' || $suat->bat_dau_luc->isPast()) {
                throw ValidationException::withMessages(['ghe' => 'Suất này đã đóng bán hoặc đã bắt đầu.']);
            }
            $this->nhaGheHetHan($suat->id);

            // Đơn chờ cũ của chính người này trong suất này: hủy để nhả ghế.
            $cu = DonHang::where('nguoi_dung_id', $user->id)->where('trang_thai', 'cho_thanh_toan')
                ->whereHas('gheDangGiu', fn ($q) => $q->where('suat_id', $suat->id))
                ->lockForUpdate()->get();
            foreach ($cu as $don) {
                $this->huyTrongTransaction($don);
            }

            $ids = array_values(array_unique($gheTheoSuatIds));
            $ghes = GheTheoSuat::where('suat_id', $suat->id)->whereIn('id', $ids)
                ->orderBy('id')->lockForUpdate()->get();
            if ($ghes->count() !== count($ids)) {
                throw ValidationException::withMessages(['ghe' => 'Có ghế không thuộc suất này.']);
            }
            if ($ghes->contains(fn ($g) => $g->trang_thai !== 'trong')) {
                throw ValidationException::withMessages(['ghe' => 'Một số ghế vừa được người khác đặt hoặc giữ. Vui lòng chọn lại.']);
            }

            $tamTinh = $ghes->sum(fn ($g) => (float) $g->gia_ve);
            $don = DonHang::create([
                'nguoi_dung_id' => $user->id,
                'ma_don_hang' => 'DV'.now()->format('ymd').strtoupper(Str::random(6)),
                'trang_thai' => 'cho_thanh_toan',
                'tam_tinh' => $tamTinh, 'tien_giam' => 0, 'tong_tien' => $tamTinh,
                'het_han_luc' => now()->addMinutes(self::PHUT_GIU_GHE),
                'dat_luc' => now(),
            ]);
            GheTheoSuat::whereIn('id', $ghes->pluck('id'))->update([
                'trang_thai' => 'dang_giu', 'giu_den_luc' => $don->het_han_luc, 'giu_boi_don_hang_id' => $don->id,
            ]);

            return $don;
        }, 3);
    }

    /** Thanh toán: ghế -> da_ban, tạo ve + thanh_toan, đơn -> da_thanh_toan. */
    public function thanhToan(DonHang $donHang, string $phuongThuc): DonHang
    {
        $ketQua = DB::transaction(function () use ($donHang, $phuongThuc) {
            $don = DonHang::whereKey($donHang->id)->lockForUpdate()->firstOrFail();
            if ($don->trang_thai !== 'cho_thanh_toan') {
                throw ValidationException::withMessages(['don_hang' => 'Đơn hàng không còn ở trạng thái chờ thanh toán.']);
            }
            $ghes = GheTheoSuat::where('giu_boi_don_hang_id', $don->id)->orderBy('id')->lockForUpdate()->get();
            if ($don->het_han_luc->isPast() || $ghes->isEmpty() || $ghes->contains(fn ($g) => $g->trang_thai !== 'dang_giu')) {
                $this->huyTrongTransaction($don, 'het_han');

                return null; // commit việc nhả ghế rồi mới báo lỗi
            }

            foreach ($ghes as $ghe) {
                Ve::create([
                    'don_hang_id' => $don->id, 'ghe_theo_suat_id' => $ghe->id,
                    'ma_ve' => 'VE'.strtoupper(Str::random(10)), 'trang_thai' => 'hieu_luc',
                    'gia_ban' => $ghe->gia_ve, 'phat_hanh_luc' => now(),
                ]);
            }
            GheTheoSuat::whereIn('id', $ghes->pluck('id'))->update([
                'trang_thai' => 'da_ban', 'giu_den_luc' => null, 'giu_boi_don_hang_id' => null,
            ]);
            ThanhToan::create([
                'don_hang_id' => $don->id, 'nha_cung_cap' => $phuongThuc,
                'ma_giao_dich' => 'GD'.strtoupper(Str::random(12)), 'so_tien' => $don->tong_tien,
                'trang_thai' => 'thanh_cong', 'thanh_toan_luc' => now(),
                'du_lieu_phan_hoi' => ['mo_phong' => true],
            ]);
            $don->update(['trang_thai' => 'da_thanh_toan', 'het_han_luc' => null]);

            return $don;
        }, 3);

        if (! $ketQua) {
            throw ValidationException::withMessages(['don_hang' => 'Đơn đã hết hạn giữ ghế. Vui lòng chọn ghế lại.']);
        }

        return $ketQua;
    }

    public function huy(DonHang $donHang): void
    {
        DB::transaction(function () use ($donHang) {
            $don = DonHang::whereKey($donHang->id)->lockForUpdate()->firstOrFail();
            if ($don->trang_thai === 'cho_thanh_toan') {
                $this->huyTrongTransaction($don);
            }
        });
    }

    /** Dữ liệu cho AJAX: trạng thái từng ghế của suất. */
    public function trangThaiGhe(Suat $suat, ?int $userId): array
    {
        $this->nhaGheHetHan($suat->id);

        return $suat->gheTheoSuats()->with('donHangGiu:id,nguoi_dung_id')->get()->map(fn ($g) => [
            'id' => $g->id,
            'trang_thai' => $g->trang_thai,
            'cua_toi' => $g->trang_thai === 'dang_giu' && $g->donHangGiu?->nguoi_dung_id === $userId,
        ])->all();
    }

    private function huyTrongTransaction(DonHang $don, string $trangThai = 'da_huy'): void
    {
        GheTheoSuat::where('giu_boi_don_hang_id', $don->id)->where('trang_thai', 'dang_giu')
            ->update(['trang_thai' => 'trong', 'giu_den_luc' => null, 'giu_boi_don_hang_id' => null]);
        $don->update(['trang_thai' => $trangThai]);
    }
}