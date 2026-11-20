<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NoiDungRequest;
use App\Models\NoiDung;
use App\Models\Phim;
use App\Models\SuKien;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PhimSuKienController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'loai' => ['nullable', 'in:phim,su_kien'],
            'the_loai' => ['nullable', 'string', 'max:100'],
            'trang_thai' => ['nullable', 'in:nhap,cong_bo,an'],
        ]);

        $items = NoiDung::query()
            ->with(['phim', 'suKien'])
            ->withCount('suats')
            ->when($filters['q'] ?? null, fn ($q, $v) => $q->where('tieu_de', 'like', "%{$v}%"))
            ->when($filters['loai'] ?? null, fn ($q, $v) => $q->where('loai_noi_dung', $v))
            // Thể loại: tìm ở cả bảng phim lẫn bảng su_kien
            ->when($filters['the_loai'] ?? null, fn ($q, $v) => $q->where(fn ($w) => $w
                ->whereHas('phim', fn ($p) => $p->where('the_loai', 'like', "%{$v}%"))
                ->orWhereHas('suKien', fn ($s) => $s->where('the_loai', 'like', "%{$v}%"))
            ))
            ->when($filters['trang_thai'] ?? null, fn ($q, $v) => $q->where('trang_thai', $v))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.noi_dung.index', compact('items'));
    }

    public function create(Request $request)
    {
        $item = new NoiDung([
            'loai_noi_dung' => $request->query('loai') === 'su_kien' ? 'su_kien' : 'phim',
            'trang_thai' => 'nhap',
        ]);

        return view('admin.noi_dung.form', compact('item'));
    }

    public function store(NoiDungRequest $request)
    {
        $data = $request->validated();
        $path = $request->hasFile('anh_bia')
            ? $request->file('anh_bia')->store('posters', 'public')
            : null;

        try {
            DB::transaction(function () use ($data, $path) {
                $item = NoiDung::create([
                    'loai_noi_dung' => $data['loai_noi_dung'],
                    'tieu_de' => $data['tieu_de'],
                    'mo_ta' => $data['mo_ta'] ?? null,
                    'anh_bia' => $path,
                    'trang_thai' => $data['trang_thai'],
                ]);
                $this->saveDetail($item, $data);
            });
        } catch (\Throwable $e) {
            if ($path) {
                Storage::disk('public')->delete($path); // lỗi DB thì bỏ file vừa upload
            }
            throw $e;
        }

        return redirect()->route('admin.noidung.index')->with('success', 'Thêm phim/sự kiện thành công.');
    }

    public function edit(NoiDung $noiDung)
    {
        $noiDung->load(['phim', 'suKien']);

        return view('admin.noi_dung.form', ['item' => $noiDung]);
    }

    public function update(NoiDungRequest $request, NoiDung $noiDung)
    {
        $data = $request->validated();
        $oldPath = $noiDung->anh_bia;
        $newPath = $request->hasFile('anh_bia')
            ? $request->file('anh_bia')->store('posters', 'public')
            : null;

        try {
            DB::transaction(function () use ($noiDung, $data, $newPath) {
                $noiDung->update([
                    'tieu_de' => $data['tieu_de'],
                    'mo_ta' => $data['mo_ta'] ?? null,
                    'trang_thai' => $data['trang_thai'],
                    'anh_bia' => $newPath ?? $noiDung->anh_bia,
                ]);
                $this->saveDetail($noiDung, $data);
            });
        } catch (\Throwable $e) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $e;
        }

        if ($newPath && $oldPath) {
            Storage::disk('public')->delete($oldPath); // xóa poster cũ sau khi lưu thành công
        }

        return redirect()->route('admin.noidung.index')->with('success', 'Cập nhật thành công.');
    }

    public function destroy(NoiDung $noiDung)
    {
        // suat.noi_dung_id là restrictOnDelete nên phải chặn trước để báo lỗi thân thiện
        if ($noiDung->suats()->exists()) {
            throw ValidationException::withMessages([
                'noi_dung' => 'Nội dung đã có suất chiếu, không thể xóa. Hãy chuyển trạng thái sang "Ẩn".',
            ]);
        }

        $path = $noiDung->anh_bia;
        DB::transaction(fn () => $noiDung->delete()); // bảng phim/su_kien tự xóa theo (cascade)

        if ($path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()->route('admin.noidung.index')->with('success', 'Đã xóa.');
    }

    /** Ghi bảng chi tiết tương ứng (phim hoặc su_kien). */
    private function saveDetail(NoiDung $item, array $data): void
    {
        if ($item->loai_noi_dung === 'phim') {
            Phim::updateOrCreate(
                ['noi_dung_id' => $item->id],
                Arr::only($data, ['thoi_luong_phut', 'gioi_han_tuoi', 'ngon_ngu', 'the_loai', 'ngay_khoi_chieu'])
            );
        } else {
            SuKien::updateOrCreate(
                ['noi_dung_id' => $item->id],
                Arr::only($data, ['don_vi_to_chuc', 'the_loai', 'gioi_han_tuoi'])
            );
        }
    }
}
