<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function updateStatus(Request $request, DonHang $donHang)
    {
        $data = $request->validate([
            'trang_thai' => ['required', 'in:da_thanh_toan,da_huy'],
        ]);

        // Chuyển trạng thái hợp lệ: từ trạng thái hiện tại -> các trạng thái được phép
        $hopLe = [
            'cho_thanh_toan' => ['da_thanh_toan', 'da_huy'],
            'da_thanh_toan'  => ['da_huy'],
        ];

        $loi = DB::transaction(function () use ($donHang, $data, $hopLe) {
            $don = DonHang::whereKey($donHang->id)->lockForUpdate()->firstOrFail();
            $don->load('ves.gheTheoSuat');
            $moi = $data['trang_thai'];

            if (! in_array($moi, $hopLe[$don->trang_thai] ?? [], true)) {
                return 'Không thể chuyển đơn từ trạng thái hiện tại sang trạng thái này.';
            }

            if ($moi === 'da_huy') {
                foreach ($don->ves as $ve) {
                    $ve->update(['trang_thai' => 'da_huy']);
                    $ve->gheTheoSuat->update(['trang_thai' => 'trong', 'giu_den_luc' => null]);
                }
            } else { // da_thanh_toan
                foreach ($don->ves as $ve) {
                    $ve->gheTheoSuat->update(['trang_thai' => 'da_ban', 'giu_den_luc' => null]);
                }
                $don->thanhToans()->where('trang_thai', 'cho_xu_ly')
                    ->update(['trang_thai' => 'thanh_cong', 'thanh_toan_luc' => now()]);
            }

            $don->update(['trang_thai' => $moi]);

            return null;
        });

        if ($loi) {
            return back()->withErrors(['trang_thai' => $loi]);
        }

        return back()->with('success', 'Đã cập nhật trạng thái đơn.');
    }
}