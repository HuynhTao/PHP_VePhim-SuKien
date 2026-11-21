<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiaDiem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DiaDiemController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'trang_thai' => ['nullable', 'in:hoat_dong,tam_dung'],
        ]);

        $raps = DiaDiem::query()
            ->withCount('phongChieus')
            ->when($filters['q'] ?? null, function ($query, $q) {
                $query->where(function ($query) use ($q) {
                    $query->where('ten_dia_diem', 'like', "%{$q}%")
                        ->orWhere('dia_chi', 'like', "%{$q}%")
                        ->orWhere('tinh_thanh', 'like', "%{$q}%");
                });
            })
            ->when(
                $filters['trang_thai'] ?? null,
                fn ($query, $status) => $query->where('trang_thai', $status)
            )
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.dia_diem.index', compact('raps'));
    }

    public function map(DiaDiem $diaDiem)
    {
        return view('admin.dia_diem.map', ['rap' => $diaDiem]);
    }

    public function create()
    {
        $rap = new DiaDiem(['trang_thai' => 'hoat_dong']);

        return view('admin.dia_diem.form', compact('rap'));
    }

    public function store(Request $request)
    {
        DiaDiem::create($this->validated($request));

        return redirect()->route('admin.dia-diem.index')
            ->with('success', 'Thêm rạp thành công.');
    }

    public function edit(DiaDiem $diaDiem)
    {
        return view('admin.dia_diem.form', ['rap' => $diaDiem]);
    }

    public function update(Request $request, DiaDiem $diaDiem)
    {
        $diaDiem->update($this->validated($request));

        return redirect()->route('admin.dia-diem.index')
            ->with('success', 'Cập nhật rạp thành công.');
    }

    public function destroy(DiaDiem $diaDiem)
    {
        DB::transaction(function () use ($diaDiem) {
            $rap = DiaDiem::whereKey($diaDiem->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($rap->phongChieus()->exists()) {
                throw ValidationException::withMessages([
                    'rap' => 'Rạp đã có phòng, không thể xóa. Bạn có thể chuyển sang tạm dừng.',
                ]);
            }

            $rap->delete();
        });

        return redirect()->route('admin.dia-diem.index')
            ->with('success', 'Xóa rạp thành công.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'ten_dia_diem' => ['required', 'string', 'max:255'],
            'dia_chi' => ['required', 'string', 'max:255'],
            'tinh_thanh' => ['required', 'string', 'max:100'],
            'so_dien_thoai' => [
                'nullable',
                'regex:/^[0-9+() .-]{8,20}$/',
            ],
            'trang_thai' => ['required', 'in:hoat_dong,tam_dung'],
        ], [
            'required' => ':attribute không được để trống.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'so_dien_thoai.regex' => 'Số điện thoại không hợp lệ.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
        ], [
            'ten_dia_diem' => 'Tên rạp',
            'dia_chi' => 'Địa chỉ',
            'tinh_thanh' => 'Tỉnh/thành phố',
            'so_dien_thoai' => 'Số điện thoại',
            'trang_thai' => 'Trạng thái',
        ]);
    }
}
