<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\Suat;
use App\Models\User;
use App\Models\Ve;
use App\Services\DatVeService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(private DatVeService $datVe) {}

    public function __invoke()
    {
        $this->datVe->nhaGheHetHan();

        // --- Các ô số liệu ---
        $tongDoanhThu = DonHang::where('trang_thai', 'da_thanh_toan')->sum('tong_tien');
        $tongDon      = DonHang::count();
        $veDaBan      = Ve::where('trang_thai', 'hieu_luc')->count();
        $nguoiDung    = User::where('vai_tro', 'user')->count();
        $suatSapToi   = Suat::where('trang_thai', 'mo_ban')->where('bat_dau_luc', '>', now())->count();

        // --- Doanh thu 7 ngày gần nhất (ngày không có đơn = 0) ---
        $ngay = collect(range(6, 0))->map(fn ($i) => now()->subDays($i)->toDateString());

        $doanhThuTho = DonHang::where('trang_thai', 'da_thanh_toan')
            ->where('dat_luc', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(dat_luc) as ngay, SUM(tong_tien) as tong')
            ->groupBy('ngay')
            ->pluck('tong', 'ngay');

        $nhanNgay  = $ngay->map(fn ($d) => date('d/m', strtotime($d)))->values();
        $doanhThu  = $ngay->map(fn ($d) => (float) ($doanhThuTho[$d] ?? 0))->values();

        // --- Đơn theo trạng thái ---
        $tenTrangThai = [
            'cho_thanh_toan' => 'Chờ thanh toán',
            'da_thanh_toan'  => 'Đã thanh toán',
            'da_huy'         => 'Đã hủy',
            'het_han'        => 'Hết hạn',
        ];
        $donTho = DonHang::selectRaw('trang_thai, COUNT(*) as so')->groupBy('trang_thai')->pluck('so', 'trang_thai');
        $nhanTrangThai = collect($tenTrangThai)->values();
        $donTheoTrangThai = collect($tenTrangThai)->keys()->map(fn ($k) => (int) ($donTho[$k] ?? 0))->values();

        // --- Top 5 phim / sự kiện bán chạy ---
        $top = DB::table('ve')
            ->join('ghe_theo_suat', 've.ghe_theo_suat_id', '=', 'ghe_theo_suat.id')
            ->join('suat', 'ghe_theo_suat.suat_id', '=', 'suat.id')
            ->join('noi_dung', 'suat.noi_dung_id', '=', 'noi_dung.id')
            ->where('ve.trang_thai', 'hieu_luc')
            ->selectRaw('noi_dung.tieu_de, noi_dung.loai_noi_dung, COUNT(*) as so_ve, SUM(ve.gia_ban) as doanh_thu')
            ->groupBy('noi_dung.id', 'noi_dung.tieu_de', 'noi_dung.loai_noi_dung')
            ->orderByDesc('so_ve')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'tongDoanhThu', 'tongDon', 'veDaBan', 'nguoiDung', 'suatSapToi',
            'nhanNgay', 'doanhThu', 'nhanTrangThai', 'donTheoTrangThai', 'top'
        ));
    }
}
