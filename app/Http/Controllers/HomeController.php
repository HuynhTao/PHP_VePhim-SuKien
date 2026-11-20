<?php

namespace App\Http\Controllers;

use App\Models\NoiDung;
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
            ->when($filters['q'] ?? null, fn ($query, $s) => $query->where('tieu_de', 'like', '%'.$s.'%'))
            // Phim đang chiếu: ngày khởi chiếu <= hôm nay. Sắp chiếu: > hôm nay.
            ->when($muc === 'dang_chieu', fn ($query) => $query->whereHas(
                'phim', fn ($p) => $p->whereDate('ngay_khoi_chieu', '<=', today())
            ))
            ->when($muc === 'sap_chieu', fn ($query) => $query->whereHas(
                'phim', fn ($p) => $p->whereDate('ngay_khoi_chieu', '>', today())
            ))
            ->when($muc === 'su_kien', fn ($query) => $query->where('loai_noi_dung', 'su_kien'))
            ->latest('id')
            ->paginate(9)
            ->withQueryString();

        return view('home', compact('contents', 'muc'));
    }
}
