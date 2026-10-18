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
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $contents = NoiDung::query()->where('trang_thai', 'cong_bo')
            ->with(['phim', 'suKien', 'suats' => fn ($query) => $query
                ->where('trang_thai', 'mo_ban')->where('bat_dau_luc', '>', now())
                ->with('phongChieu.diaDiem')->orderBy('bat_dau_luc')])
            ->when($filters['loai'] ?? null, fn ($query, $type) => $query->where('loai_noi_dung', $type))
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where('tieu_de', 'like', '%'.$search.'%'))
            ->latest('id')->paginate(9)->withQueryString();
        return view('home', compact('contents'));
    }
}

