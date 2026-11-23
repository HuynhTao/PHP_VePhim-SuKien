<?php

namespace App\Http\Controllers;

use App\Models\NoiDung;
use Illuminate\Http\Request;

class NoiDungController extends Controller
{
    /** Trang chi tiết phim/sự kiện + danh sách suất chiếu còn mở bán. */
    public function show(NoiDung $noiDung)
    {
        abort_unless($noiDung->trang_thai === 'cong_bo' || auth()->user()?->isAdmin(), 404);

        $noiDung->load(['phim', 'suKien']);

        $suats = $noiDung->suats()
            ->where('trang_thai', 'mo_ban')
            ->where('bat_dau_luc', '>', now())
            ->with('phongChieu.diaDiem')
            ->withCount(['gheTheoSuats as ghe_trong_count' => fn ($q) => $q->where('trang_thai', 'trong')])
            ->orderBy('bat_dau_luc')
            ->get()
            ->groupBy(fn ($suat) => $suat->bat_dau_luc->format('Y-m-d'));

        return view('noi_dung.show', compact('noiDung', 'suats'));
    }

    /** AJAX: gợi ý tìm kiếm nhanh ở trang chủ, trả về JSON. */
    public function quickSearch(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2 || mb_strlen($q) > 100) {
            return response()->json([]);
        }

        $items = NoiDung::query()->congBo()
            ->where('tieu_de', 'like', '%'.$q.'%')
            ->orderBy('tieu_de')
            ->limit(6)
            ->get()
            ->map(fn (NoiDung $n) => [
                'id' => $n->id,
                'tieu_de' => $n->tieu_de,
                'loai' => $n->loai_noi_dung === 'phim' ? 'Phim' : 'Sự kiện',
                'anh' => $n->anh_bia_url,
                'url' => route('noi-dung.show', $n),
            ]);

        return response()->json($items);
    }
}
