<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiaDiem;
use App\Models\NoiDung;
use App\Models\PhongChieu;
use App\Models\Suat;
use App\Services\QuanLySuat;
use Illuminate\Http\Request;

class SuatController extends Controller
{
    public function index(Request $request)
    {
        $f = $request->validate([
            'q' => 'nullable|string|max:100', 'dia_diem_id' => 'nullable|integer|exists:dia_diem,id',
            'phong_chieu_id' => 'nullable|integer|exists:phong_chieu,id', 'ngay' => 'nullable|date_format:Y-m-d',
            'trang_thai' => 'nullable|in:mo_ban,dong,huy',
        ]);
        $suats = Suat::with('noiDung', 'phongChieu.diaDiem')->withCount('gheTheoSuats')
            ->when($f['q'] ?? null, fn ($q, $v) => $q->whereHas('noiDung', fn ($q) => $q->where('tieu_de', 'like', '%'.$v.'%')))
            ->when($f['dia_diem_id'] ?? null, fn ($q, $v) => $q->whereHas('phongChieu', fn ($q) => $q->where('dia_diem_id', $v)))
            ->when($f['phong_chieu_id'] ?? null, fn ($q, $v) => $q->where('phong_chieu_id', $v))
            ->when($f['ngay'] ?? null, fn ($q, $v) => $q->whereDate('bat_dau_luc', $v))
            ->when($f['trang_thai'] ?? null, fn ($q, $v) => $q->where('trang_thai', $v))
            ->orderByDesc('bat_dau_luc')->orderByDesc('id')->paginate(10)->withQueryString();
        $raps = DiaDiem::orderBy('ten_dia_diem')->get();
        $phongs = PhongChieu::with('diaDiem')->orderBy('ten_phong')->get();

        return view('admin.suat.index', compact('suats', 'raps', 'phongs'));
    }

    public function create()
    {
        return $this->form(new Suat(['trang_thai' => 'mo_ban']));
    }

    public function store(Request $request, QuanLySuat $service)
    {
        $suat = $service->save($this->validated($request));

        return redirect()->route('admin.suat.show', $suat)->with('success', 'Đã tạo suất và giá vé cho từng ghế.');
    }

    public function show(Suat $suat)
    {
        $suat->load('noiDung', 'phongChieu.diaDiem', 'gheTheoSuats.gheNgoi');

        return view('admin.suat.show', compact('suat'));
    }

    public function edit(Suat $suat)
    {
        return $this->form($suat);
    }

    public function update(Request $request, Suat $suat, QuanLySuat $service)
    {
        $service->save($this->validated($request), $suat);

        return redirect()->route('admin.suat.show', $suat)->with('success', 'Đã cập nhật suất.');
    }

    public function destroy(Suat $suat, QuanLySuat $service)
    {
        $service->delete($suat);

        return redirect()->route('admin.suat.index')->with('success', 'Đã xóa suất chưa có vé/giữ ghế.');
    }

    private function form(Suat $suat)
    {
        $noidungs = NoiDung::with('phim')->where('trang_thai', 'cong_bo')->orderBy('tieu_de')->get();
        $phongs = PhongChieu::with('diaDiem')->withCount(['gheNgois' => fn ($q) => $q->where('dang_su_dung', true)])->orderBy('ten_phong')->get();
        $prices = ['thuong' => 90000, 'vip' => 120000];
        if ($suat->exists) {
            foreach ($prices as $type => $default) {
                $prices[$type] = $suat->gheTheoSuats()->whereHas('gheNgoi', fn ($q) => $q->where('loai_ghe', $type))->value('gia_ve') ?? $default;
            }
        }

        return view('admin.suat.form', compact('suat', 'noidungs', 'phongs', 'prices'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'noi_dung_id' => 'required|integer|exists:noi_dung,id',
            'phong_chieu_id' => 'required|integer|exists:phong_chieu,id',
            'bat_dau_luc' => 'required|date_format:Y-m-d\TH:i',
            'ket_thuc_luc' => 'required|date_format:Y-m-d\TH:i|after:bat_dau_luc',
            'trang_thai' => 'required|in:mo_ban,dong,huy',
            'gia_thuong' => 'required|numeric|min:0|max:9999999999.99|decimal:0,2',
            'gia_vip' => 'required|numeric|min:0|max:9999999999.99|decimal:0,2',
        ], ['ket_thuc_luc.after' => 'Giờ kết thúc phải sau giờ bắt đầu.']);
    }
}
