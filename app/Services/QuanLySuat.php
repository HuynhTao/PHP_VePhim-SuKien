<?php

namespace App\Services;

use App\Models\NoiDung;
use App\Models\PhongChieu;
use App\Models\Suat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuanLySuat
{
    public function save(array $data, ?Suat $existing = null): Suat
    {
        return DB::transaction(function () use ($data, $existing) {
            // Khóa phòng kể cả khi chưa có suất: hai thao tác tạo đồng thời phải xếp hàng.
            $ids = array_unique(array_filter([$data['phong_chieu_id'], $existing?->phong_chieu_id]));
            $rooms = PhongChieu::whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $room = $rooms->get($data['phong_chieu_id']);
            if (! $room) {
                throw ValidationException::withMessages(['phong_chieu_id' => 'Phòng không còn tồn tại.']);
            }
            $suat = $existing ? Suat::whereKey($existing->id)->lockForUpdate()->firstOrFail() : new Suat;
            if ($existing && $suat->phong_chieu_id !== $existing->phong_chieu_id) {
                throw ValidationException::withMessages(['suat' => 'Suất vừa được thay đổi. Hãy tải lại trang.']);
            }
            if ($existing) {
                $this->assertUnbooked($suat);
            }
            if ($data['trang_thai'] === 'mo_ban' && ($room->trang_thai !== 'hoat_dong' || $room->diaDiem->trang_thai !== 'hoat_dong')) {
                throw ValidationException::withMessages(['phong_chieu_id' => 'Chỉ mở bán tại rạp và phòng đang hoạt động.']);
            }
            $content = NoiDung::with('phim', 'suKien')->findOrFail($data['noi_dung_id']);
            if ($content->trang_thai !== 'cong_bo') {
                throw ValidationException::withMessages(['noi_dung_id' => 'Nội dung phải được công bố trước khi lập suất.']);
            }
            $start = Carbon::parse($data['bat_dau_luc']);
            $end = Carbon::parse($data['ket_thuc_luc']);
            if ($content->loai_noi_dung === 'phim') {
                if (! $content->phim || $end->lt($start->copy()->addMinutes($content->phim->thoi_luong_phut))) {
                    throw ValidationException::withMessages(['ket_thuc_luc' => 'Giờ kết thúc phải đủ thời lượng phim.']);
                }
                if ($start->toDateString() < $content->phim->ngay_khoi_chieu->toDateString()) {
                    throw ValidationException::withMessages(['bat_dau_luc' => 'Suất không được trước ngày khởi chiếu.']);
                }
            } elseif (! $content->suKien) {
                throw ValidationException::withMessages(['noi_dung_id' => 'Nội dung thiếu thông tin sự kiện.']);
            }
            if ($data['trang_thai'] !== 'huy') {
                $conflict = Suat::where('phong_chieu_id', $room->id)->where('trang_thai', '!=', 'huy')
                    ->when($existing, fn ($q) => $q->where('id', '!=', $existing->id))
                    ->where('bat_dau_luc', '<', $end)->where('ket_thuc_luc', '>', $start)->exists();
                if ($conflict) {
                    throw ValidationException::withMessages(['bat_dau_luc' => 'Trùng lịch: phòng đã có suất trong khoảng thời gian này.']);
                }
            }
            // Unique của schema M1 vẫn cấm cùng giờ bắt đầu kể cả suất đã hủy.
            if (Suat::where('phong_chieu_id', $room->id)->where('bat_dau_luc', $start)
                ->when($existing, fn ($q) => $q->where('id', '!=', $existing->id))->exists()) {
                throw ValidationException::withMessages(['bat_dau_luc' => 'Phòng đã có bản ghi cùng giờ bắt đầu (kể cả suất đã hủy). Hãy sửa/xóa bản ghi cũ.']);
            }
            $seats = $room->gheNgois()->where('dang_su_dung', true)->orderBy('id')->get();
            if ($seats->isEmpty()) {
                throw ValidationException::withMessages(['phong_chieu_id' => 'Phòng chưa có ghế đang sử dụng.']);
            }
            $suat->fill(collect($data)->except(['gia_thuong', 'gia_vip'])->all());
            $suat->save();
            // Chỉ rebuild khi chưa giữ/bán/phát hành vé; không ảnh hưởng dữ liệu M4.
            $suat->gheTheoSuats()->delete();
            foreach ($seats as $seat) {
                $suat->gheTheoSuats()->create([
                    'ghe_ngoi_id' => $seat->id,
                    'gia_ve' => $data[$seat->loai_ghe === 'vip' ? 'gia_vip' : 'gia_thuong'],
                    'trang_thai' => $data['trang_thai'] === 'huy' ? 'tam_khoa' : 'trong',
                ]);
            }

            return $suat;
        }, 3);
    }

    public function delete(Suat $suat): void
    {
        DB::transaction(function () use ($suat) {
            PhongChieu::whereKey($suat->phong_chieu_id)->lockForUpdate()->firstOrFail();
            $locked = Suat::whereKey($suat->id)->lockForUpdate()->firstOrFail();
            $this->assertUnbooked($locked);
            $locked->gheTheoSuats()->delete();
            $locked->delete();
        });
    }

    private function assertUnbooked(Suat $suat): void
    {
        // Lock cùng các hàng mà M4 sẽ dùng để giữ/bán ghế.
        $rows = $suat->gheTheoSuats()->orderBy('id')->lockForUpdate()->get();
        if ($rows->contains(fn ($row) => in_array($row->trang_thai, ['dang_giu', 'da_ban'], true))
            || $suat->gheTheoSuats()->whereHas('ve')->exists()) {
            throw ValidationException::withMessages(['suat' => 'Suất có ghế đang giữ, đã bán hoặc vé đã phát hành; không thể sửa/xóa. Luồng hủy và hoàn vé do module đặt vé xử lý.']);
        }
    }
}
