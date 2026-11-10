<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GheNgoi;
use App\Models\PhongChieu;
use App\Services\QuanLyGhe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GheNgoiController extends Controller
{
    public function generate(Request $request, PhongChieu $phongChieu, QuanLyGhe $service)
    {
        $data = $request->validate(['so_hang' => 'required|integer|min:1|max:26', 'ghe_moi_hang' => 'required|integer|min:1|max:50', 'so_hang_vip' => 'required|integer|min:0|lte:so_hang']);
        DB::transaction(function () use ($phongChieu, $service, $data) {
            $phong = PhongChieu::whereKey($phongChieu->id)->lockForUpdate()->firstOrFail();
            $service->generate($phong, $data);
        });

        return back()->with('success', 'Đã tạo lại sơ đồ ghế.');
    }

    public function store(Request $request, PhongChieu $phongChieu, QuanLyGhe $service)
    {
        DB::transaction(function () use ($request, $phongChieu, $service) {
            $phong = PhongChieu::whereKey($phongChieu->id)->lockForUpdate()->firstOrFail();
            $service->assertEditable($phong);
            $phong->gheNgois()->create($this->validated($request, $phong));
        });

        return back()->with('success', 'Đã thêm ghế.');
    }

    public function update(Request $request, PhongChieu $phongChieu, GheNgoi $gheNgoi, QuanLyGhe $service)
    {
        abort_unless($gheNgoi->phong_chieu_id === $phongChieu->id, 404);
        DB::transaction(function () use ($request, $phongChieu, $gheNgoi, $service) {
            $phong = PhongChieu::whereKey($phongChieu->id)->lockForUpdate()->firstOrFail();
            $service->assertEditable($phong);
            $gheNgoi->update($this->validated($request, $phong, $gheNgoi));
        });

        return back()->with('success', 'Đã cập nhật ghế.');
    }

    public function destroy(PhongChieu $phongChieu, GheNgoi $gheNgoi, QuanLyGhe $service)
    {
        abort_unless($gheNgoi->phong_chieu_id === $phongChieu->id, 404);
        DB::transaction(function () use ($phongChieu, $gheNgoi, $service) {
            $phong = PhongChieu::whereKey($phongChieu->id)->lockForUpdate()->firstOrFail();
            $service->assertEditable($phong);
            $gheNgoi->delete();
        });

        return back()->with('success', 'Đã xóa ghế.');
    }

    private function validated(Request $request, PhongChieu $phong, ?GheNgoi $ghe = null): array
    {
        $request->merge(['hang_ghe' => strtoupper(trim((string) $request->input('hang_ghe')))]);

        return $request->validate([
            'hang_ghe' => ['required', 'regex:/^[A-Z]$/'],
            'so_ghe' => ['required', 'integer', 'min:1', 'max:50', Rule::unique('ghe_ngoi')->where('phong_chieu_id', $phong->id)->where('hang_ghe', $request->input('hang_ghe'))->ignore($ghe?->id)],
            'loai_ghe' => ['required', 'in:thuong,vip'],
            'dang_su_dung' => ['required', 'boolean'],
        ], ['so_ghe.unique' => 'Mã ghế đã tồn tại trong phòng.', 'hang_ghe.regex' => 'Hàng ghế phải là một chữ từ A đến Z.']);
    }
}
