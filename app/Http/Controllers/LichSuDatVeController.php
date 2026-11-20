<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use Illuminate\Http\Request;

class LichSuDatVeController extends Controller
{
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
        abort_unless($donHang->nguoi_dung_id === $request->user()->id, 403);

        $donHang->load([
            'ves.gheTheoSuat.gheNgoi',
            'ves.gheTheoSuat.suat.noiDung',
            'ves.gheTheoSuat.suat.phongChieu.diaDiem',
            'thanhToans',
        ]);

        return view('orders.show', compact('donHang'));
    }
}