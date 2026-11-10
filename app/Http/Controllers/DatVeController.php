<?php

namespace App\Http\Controllers;

use App\Http\Requests\GiuGheRequest;
use App\Http\Requests\ThanhToanRequest;
use App\Models\DiaDiem;
use App\Models\DonHang;
use App\Models\NoiDung;
use App\Models\Suat;
use App\Services\DatVeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DatVeController extends Controller
{
    public function __construct(private DatVeService $datVe) {}

    /** Trang chọn suất: phim - rạp - ngày - giờ. */
    public function index(Request $request)
    {
        $f = $request->validate([
            'q' => 'nullable|string|max:100',
            'dia_diem_id' => 'nullable|integer|exists:dia_diem,id',
            'ngay' => 'nullable|date_format:Y-m-d',
        ]);
        $ngay = isset($f['ngay']) ? Carbon::createFromFormat('Y-m-d', $f['ngay'])->startOfDay() : today();
        if ($ngay->lt(today())) {
            $ngay = today();
        }

        $locSuat = fn ($q) => $q->where('trang_thai', 'mo_ban')
            ->whereBetween('bat_dau_luc', [$ngay->copy()->startOfDay(), $ngay->copy()->endOfDay()])
            ->where('bat_dau_luc', '>', now())
            ->when($f['dia_diem_id'] ?? null, fn ($q, $v) => $q->whereHas('phongChieu', fn ($q) => $q->where('dia_diem_id', $v)));

        $contents = NoiDung::where('trang_thai', 'cong_bo')
            ->whereHas('suats', $locSuat)
            ->with(['phim', 'suKien', 'suats' => fn ($q) => $locSuat($q)->with('phongChieu.diaDiem')
                ->withCount(['gheTheoSuats', 'gheTheoSuats as ghe_trong_count' => fn ($q) => $q->where('trang_thai', 'trong')])
                ->orderBy('bat_dau_luc')])
            ->when($f['q'] ?? null, fn ($q, $v) => $q->where('tieu_de', 'like', '%'.$v.'%'))
            ->orderBy('tieu_de')->paginate(6)->withQueryString();

        $ngays = collect(range(0, 6))->map(fn ($i) => today()->addDays($i));
        $raps = DiaDiem::where('trang_thai', 'hoat_dong')->orderBy('ten_dia_diem')->get();

        return view('datve.index', compact('contents', 'ngay', 'ngays', 'raps'));
    }

    /** Sơ đồ ghế + tính tổng tiền. */
    public function chonGhe(Request $request, Suat $suat)
    {
        $nguoiDungId = $request->user()?->getAuthIdentifier();
        $suatId = $suat->getKey();

        $this->datVe->nhaGheHetHan($suatId);

        if ($suat->trang_thai !== 'mo_ban' || $suat->bat_dau_luc->isPast()) {
            return redirect()->route('datve.index')->withErrors([
                'suat' => 'Suất này đã đóng bán hoặc đã bắt đầu.'
            ]);
        }

        $suat->load('noiDung.phim', 'phongChieu.diaDiem');

        $ghes = $suat->gheTheoSuats()
            ->with('gheNgoi', 'donHangGiu:id,nguoi_dung_id')
            ->get()
            ->sortBy(fn ($g) => $g->gheNgoi->hang_ghe . str_pad($g->gheNgoi->so_ghe, 3, '0', STR_PAD_LEFT))
            ->groupBy(fn ($g) => $g->gheNgoi->hang_ghe);

        $donCho = DonHang::where('nguoi_dung_id', $nguoiDungId)
            ->where('trang_thai', 'cho_thanh_toan')
            ->whereHas('gheDangGiu', fn ($q) => $q->where('suat_id', $suatId))
            ->first();

        return view('datve.chon_ghe', compact('suat', 'ghes', 'donCho'));
    }

    /** AJAX: trạng thái ghế gần thời gian thực. */
    public function trangThai(Request $request, Suat $suat)
    {
        return response()->json([
            'ghes' => $this->datVe->trangThaiGhe(
                $suat,
                $request->user()?->getAuthIdentifier()
            ),
            'luc' => now()->format('H:i:s'),
        ]);
    }

    public function giuGhe(GiuGheRequest $request, Suat $suat)
    {
        $don = $this->datVe->giuGhe($request->user(), $suat, $request->validated('ghe_ids'));

        return redirect()->route('datve.thanh-toan', $don);
    }

    public function thanhToan(DonHang $donHang)
    {
        Gate::authorize('pay', $donHang);
        $this->datVe->nhaGheHetHan();
        $donHang->refresh();
        if ($donHang->trang_thai === 'da_thanh_toan') {
            return redirect()->route('datve.thanh-cong', $donHang);
        }
        if ($donHang->trang_thai !== 'cho_thanh_toan') {
            return redirect()->route('datve.index')->withErrors(['don_hang' => 'Đơn hàng đã hết hạn hoặc đã bị hủy.']);
        }
        $donHang->load('gheDangGiu.gheNgoi', 'gheDangGiu.suat.noiDung', 'gheDangGiu.suat.phongChieu.diaDiem');
        $suat = $donHang->gheDangGiu->first()?->suat;
        if (! $suat) {
            return redirect()->route('datve.index')->withErrors(['don_hang' => 'Đơn hàng không còn ghế được giữ.']);
        }

        return view('datve.thanh_toan', ['don' => $donHang, 'suat' => $suat, 'phuongThuc' => DatVeService::PHUONG_THUC]);
    }

    public function xacNhan(ThanhToanRequest $request, DonHang $donHang)
    {
        Gate::authorize('pay', $donHang);
        $this->datVe->thanhToan($donHang, $request->validated('phuong_thuc'));

        return redirect()->route('datve.thanh-cong', $donHang)->with('success', 'Đặt vé thành công!');
    }

    public function huy(DonHang $donHang)
    {
        Gate::authorize('pay', $donHang);
        $suatId = $donHang->gheDangGiu()->value('suat_id');
        $this->datVe->huy($donHang);
        $msg = 'Đã hủy đơn và nhả ghế.';

        return $suatId
            ? redirect()->route('datve.chon-ghe', $suatId)->with('success', $msg)
            : redirect()->route('datve.index')->with('success', $msg);
    }

    public function thanhCong(DonHang $donHang)
    {
        Gate::authorize('view', $donHang);
        if ($donHang->trang_thai !== 'da_thanh_toan') {
            return redirect()->route('datve.index');
        }
        $donHang->load('ves.gheTheoSuat.gheNgoi', 'ves.gheTheoSuat.suat.noiDung', 'ves.gheTheoSuat.suat.phongChieu.diaDiem', 'thanhToans');

        return view('datve.thanh_cong', ['don' => $donHang, 'phuongThuc' => DatVeService::PHUONG_THUC]);
    }
}