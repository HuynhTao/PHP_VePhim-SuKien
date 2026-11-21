<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DatVeService;

class DonHangController extends Controller
{
    public function index(Request $request)
    {
        $donHangs = DonHang::with('nguoiDung')
            ->withCount('ves')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = trim($request->q);
                $query->where(function ($sub) use ($q) {
                    $sub->where('ma_don_hang', 'like', "%{$q}%")
                        ->orWhereHas('nguoiDung', fn ($u) => $u
                            ->where('email', 'like', "%{$q}%")
                            ->orWhere('ho_ten', 'like', "%{$q}%"));
                });
            })
            ->when($request->filled('trang_thai'), fn ($query) => $query->where('trang_thai', $request->trang_thai))
            ->when($request->filled('tu_ngay'), fn ($query) => $query->whereDate('dat_luc', '>=', $request->tu_ngay))
            ->when($request->filled('den_ngay'), fn ($query) => $query->whereDate('dat_luc', '<=', $request->den_ngay))
            ->latest('dat_luc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', compact('donHangs'));
    }

    public function show(DonHang $donHang)
    {
        $donHang->load([
            'nguoiDung',
            'ves.gheTheoSuat.gheNgoi',
            'ves.gheTheoSuat.suat.noiDung',
            'ves.gheTheoSuat.suat.phongChieu.diaDiem',
            'thanhToans',
        ]);

        return view('admin.orders.show', compact('donHang'));
    }

    public function updateStatus(Request $request, DonHang $donHang, DatVeService $datVe)
{
    $moi = $request->validate([
        'trang_thai' => ['required', 'in:da_thanh_toan,da_huy'],
    ])['trang_thai'];

    // 1) Đơn chờ thanh toán: chưa có vé, ghế đang được giữ -> để service của M4 xử lý
    if ($donHang->trang_thai === 'cho_thanh_toan') {
        if ($moi === 'da_thanh_toan') {
            $datVe->thanhToan($donHang, 'admin_xac_nhan'); // tạo vé + thanh toán, ghế -> đã bán
        } else {
            $datVe->huy($donHang);                          // nhả ghế đang giữ
        }

        return back()->with('success', 'Đã cập nhật trạng thái đơn.');
    }

    // 2) Đơn đã thanh toán: chỉ được hủy -> hủy vé + nhả ghế
    if ($donHang->trang_thai === 'da_thanh_toan' && $moi === 'da_huy') {
        DB::transaction(function () use ($donHang) {
            $don = DonHang::whereKey($donHang->id)->lockForUpdate()->firstOrFail();
            if ($don->trang_thai !== 'da_thanh_toan') {
                return;
            }
            $don->load('ves.gheTheoSuat');
            foreach ($don->ves as $ve) {
                $ve->update(['trang_thai' => 'da_huy']);
                $ve->gheTheoSuat->update(['trang_thai' => 'trong', 'giu_den_luc' => null]);
            }
            $don->update(['trang_thai' => 'da_huy']);
        });

        return back()->with('success', 'Đã cập nhật trạng thái đơn.');
    }

    return back()->withErrors(['trang_thai' => 'Không thể chuyển đơn từ trạng thái hiện tại sang trạng thái này.']);
}
}