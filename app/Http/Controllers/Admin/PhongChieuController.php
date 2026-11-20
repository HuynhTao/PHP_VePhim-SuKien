<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiaDiem;
use App\Models\PhongChieu;
use App\Services\QuanLyGhe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PhongChieuController extends Controller
{
    public function index(Request $request)
    {
        $f = $request->validate(['q' => 'nullable|string|max:100', 'dia_diem_id' => 'nullable|integer|exists:dia_diem,id', 'trang_thai' => 'nullable|in:hoat_dong,bao_tri']);
        $phongs = PhongChieu::with('diaDiem')->withCount('gheNgois', 'suats')
            ->when($f['q'] ?? null, fn ($q, $v) => $q->where('ten_phong', 'like', '%'.$v.'%'))
            ->when($f['dia_diem_id'] ?? null, fn ($q, $v) => $q->where('dia_diem_id', $v))
            ->when($f['trang_thai'] ?? null, fn ($q, $v) => $q->where('trang_thai', $v))
            ->latest('id')->paginate(10)->withQueryString();
        $raps = DiaDiem::orderBy('ten_dia_diem')->get();

        return view('admin.phong_chieu.index', compact('phongs', 'raps'));
    }

    public function create()
    {
        return $this->form(new PhongChieu(['loai_phong' => '2D', 'trang_thai' => 'hoat_dong']));
    }

    public function store(Request $request, QuanLyGhe $ghe)
    {
        $data = $this->validated($request);
        $grid = $request->validate(['so_hang' => 'required|integer|min:1|max:26', 'ghe_moi_hang' => 'required|integer|min:1|max:50', 'so_hang_vip' => 'required|integer|min:0|lte:so_hang']);
        $phong = DB::transaction(function () use ($data, $grid, $ghe) {
            DiaDiem::whereKey($data['dia_diem_id'])->lockForUpdate()->firstOrFail();
            $phong = PhongChieu::create($data);
            $ghe->generate($phong, $grid);

            return $phong;
        });

        return redirect()->route('admin.phong-chieu.show', $phong)->with('success', 'Đã tạo phòng và tự sinh ghế.');
    }

    public function show(PhongChieu $phongChieu)
    {
        $phongChieu->load('diaDiem')->loadCount('suats');
        $ghes = $phongChieu->gheNgois()->orderBy('hang_ghe')->orderBy('so_ghe')->get();

        return view('admin.phong_chieu.show', ['phong' => $phongChieu, 'ghes' => $ghes]);
    }

    public function edit(PhongChieu $phongChieu)
    {
        return $this->form($phongChieu);
    }

    public function update(Request $request, PhongChieu $phongChieu)
    {
        $data = $this->validated($request, $phongChieu);
        DB::transaction(function () use ($phongChieu, $data) {
            $phong = PhongChieu::whereKey($phongChieu->id)->lockForUpdate()->firstOrFail();
            if ($phong->dia_diem_id != $data['dia_diem_id'] && $phong->suats()->exists()) {
                throw ValidationException::withMessages(['dia_diem_id' => 'Phòng đã có suất, không thể chuyển sang rạp khác.']);
            }
            $phong->update($data);
        });

        return redirect()->route('admin.phong-chieu.show', $phongChieu)->with('success', 'Đã cập nhật phòng.');
    }

    public function destroy(PhongChieu $phongChieu)
    {
        DB::transaction(function () use ($phongChieu) {
            $phong = PhongChieu::whereKey($phongChieu->id)->lockForUpdate()->firstOrFail();
            if ($phong->suats()->exists() || $phong->gheNgois()->whereHas('gheTheoSuats')->exists()) {
                throw ValidationException::withMessages(['phong' => 'Phòng đã có suất hoặc ghế theo suất. Hãy chuyển sang bảo trì thay vì xóa.']);
            }
            $phong->gheNgois()->delete();
            $phong->delete();
        });

        return redirect()->route('admin.phong-chieu.index')->with('success', 'Đã xóa phòng và ghế chưa sử dụng.');
    }

    private function form(PhongChieu $phong)
    {
        $raps = DiaDiem::orderBy('ten_dia_diem')->get();

        return view('admin.phong_chieu.form', compact('phong', 'raps'));
    }

    private function validated(Request $request, ?PhongChieu $phong = null): array
    {
        return $request->validate([
            'dia_diem_id' => ['required', 'integer', 'exists:dia_diem,id'],
            'ten_phong' => ['required', 'string', 'max:100', Rule::unique('phong_chieu')->where('dia_diem_id', $request->input('dia_diem_id'))->ignore($phong?->id)],
            'loai_phong' => ['required', 'in:2D,3D,IMAX,san_khau'],
            'trang_thai' => ['required', 'in:hoat_dong,bao_tri'],
        ], ['ten_phong.unique' => 'Tên phòng đã tồn tại trong rạp này.']);
    }
}
