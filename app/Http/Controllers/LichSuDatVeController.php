<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DatVeService;
class LichSuDatVeController extends Controller
{
    public function __construct(private DatVeService $datVe) {}
    // Chỉ được hủy khi còn ít nhất 2 giờ trước giờ chiếu
    private const GIO_TOI_THIEU_DE_HUY = 2;

    public function index(Request $request)
    {
        $donHangs = DonHang::where('nguoi_dung_id', $request->user()->id)
            ->withCount('ves')
            ->when($request->filled('trang_thai'), fn ($q) => $q->where('trang_thai', $request->trang_thai))
            ->latest('dat_luc')
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', compact('donHangs'));
    }

    public function show(Request $request, DonHang $donHang)
    {
        $this->authorizeOwner($request, $donHang);

        $donHang->load([
            'ves.gheTheoSuat.gheNgoi',
            'ves.gheTheoSuat.suat.noiDung',
            'ves.gheTheoSuat.suat.phongChieu.diaDiem',
            'thanhToans',
            'gheDangGiu.gheNgoi',
'gheDangGiu.suat.noiDung',
'gheDangGiu.suat.phongChieu.diaDiem',
        ]);

        return view('orders.show', [
            'donHang' => $donHang,
            'coTheHuy' => $this->coTheHuy($donHang),
        ]);
    }

    public function cancel(Request $request, DonHang $donHang)
    {
        $this->authorizeOwner($request, $donHang);

        $loi = DB::transaction(function () use ($donHang) {
            // Khóa dòng đơn để tránh bấm hủy 2 lần cùng lúc
            $don = DonHang::whereKey($donHang->id)->lockForUpdate()->firstOrFail();
            $don->load('ves.gheTheoSuat.suat');

            if (! $this->coTheHuy($don)) {
                return 'Đơn này không thể hủy (đã hủy, hết hạn hoặc quá gần giờ chiếu).';
            }

            foreach ($don->ves as $ve) {
                $ve->update(['trang_thai' => 'da_huy']);
                $ve->gheTheoSuat->update(['trang_thai' => 'trong', 'giu_den_luc' => null]);
            }
            $don->update(['trang_thai' => 'da_huy']);

            return null;
        });

        if ($loi) {
            return back()->withErrors(['huy' => $loi]);
        }

        return redirect()->route('orders.show', $donHang)->with('success', 'Đã hủy đơn và giải phóng ghế.');
    }

    private function authorizeOwner(Request $request, DonHang $donHang): void
    {
        abort_unless($donHang->nguoi_dung_id === $request->user()->id, 403);
    }

    private function coTheHuy(DonHang $don): bool
    {
        if (! in_array($don->trang_thai, ['cho_thanh_toan', 'da_thanh_toan'], true)) {
            return false;
        }

        $gioChieuSom = $don->ves
            ->map(fn ($ve) => $ve->gheTheoSuat?->suat?->bat_dau_luc)
            ->filter()
            ->min();

        return $gioChieuSom && now()->addHours(self::GIO_TOI_THIEU_DE_HUY)->lt($gioChieuSom);
    }
}