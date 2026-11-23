<?php

namespace App\Http\Controllers;

use App\Models\DiaDiem;
use App\Models\NoiDung;
use App\Models\Suat;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $filters = $request->validate([
            'loai' => ['nullable', 'in:phim,su_kien'],
            'muc' => ['nullable', 'in:dang_chieu,sap_chieu,su_kien'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $muc = $filters['muc'] ?? null;

        $contents = NoiDung::query()->congBo()
            ->with(['phim', 'suKien', 'suats' => fn ($query) => $query
                ->where('trang_thai', 'mo_ban')->where('bat_dau_luc', '>', now())
                ->with('phongChieu.diaDiem')->orderBy('bat_dau_luc')])
            ->when($filters['loai'] ?? null, fn ($query, $type) => $query->where('loai_noi_dung', $type))
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where('tieu_de', 'like', '%'.$search.'%'))
            ->when($muc === 'dang_chieu', fn ($query) => $query->where('loai_noi_dung', 'phim')->whereHas(
                'phim', fn ($p) => $p->whereDate('ngay_khoi_chieu', '<=', today())
            ))
            ->when($muc === 'sap_chieu', fn ($query) => $query->where('loai_noi_dung', 'phim')->whereHas(
                'phim', fn ($p) => $p->whereDate('ngay_khoi_chieu', '>', today())
            ))
            ->when($muc === 'su_kien', fn ($query) => $query->where('loai_noi_dung', 'su_kien'))
            ->orderBy('id')->paginate(9)->withQueryString();

        $stats = [
            'phim' => NoiDung::where('trang_thai', 'cong_bo')->where('loai_noi_dung', 'phim')->count(),
            'dang_chieu' => NoiDung::where('trang_thai', 'cong_bo')->where('loai_noi_dung', 'phim')
                ->whereHas('phim', fn ($p) => $p->whereDate('ngay_khoi_chieu', '<=', today()))->count(),
            'sap_chieu' => NoiDung::where('trang_thai', 'cong_bo')->where('loai_noi_dung', 'phim')
                ->whereHas('phim', fn ($p) => $p->whereDate('ngay_khoi_chieu', '>', today()))->count(),
            'su_kien' => NoiDung::where('trang_thai', 'cong_bo')->where('loai_noi_dung', 'su_kien')->count(),
            'rap' => DiaDiem::where('trang_thai', 'hoat_dong')->count(),
            'suat' => Suat::where('trang_thai', 'mo_ban')->where('bat_dau_luc', '>', now())->count(),
        ];

        $raps = DiaDiem::where('trang_thai', 'hoat_dong')
            ->withCount('phongChieus')
            ->orderBy('id')
            ->take(4)
            ->get();

        return view('home', compact('contents', 'stats', 'raps', 'muc'));
    }
}
