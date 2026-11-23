@extends('layouts.app')
@section('title', 'Quản trị đơn ' . $donHang->ma_don_hang)
@section('content')
<section class="admin-page">
    @include('admin.m3-nav')
    <a href="{{ route('admin.orders.index') }}" class="back-link">← Quay lại danh sách đơn</a>

    <div class="section-heading">
        <div>
            <span class="eyebrow">CHI TIẾT ĐƠN HÀNG · MODULE 5</span>
            <h1>Đơn {{ $donHang->ma_don_hang }}</h1>
            <p class="muted">Đặt lúc {{ $donHang->dat_luc?->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            @include('orders._badge', ['trangThai' => $donHang->trang_thai])
        </div>
    </div>

    <div class="m5-card">
        <h2>Thông tin khách hàng</h2>
        <div>
            <strong>{{ $donHang->nguoiDung->ho_ten }}</strong> · {{ $donHang->nguoiDung->email }}
            @if($donHang->nguoiDung->so_dien_thoai) · {{ $donHang->nguoiDung->so_dien_thoai }} @endif
        </div>
    </div>

    @if($donHang->ves->isNotEmpty())
        <div class="table-wrap" style="margin-bottom:20px">
            <table>
                <thead>
                    <tr>
                        <th>Mã vé</th>
                        <th>Phim / Sự kiện</th>
                        <th>Rạp / Phòng</th>
                        <th>Giờ chiếu</th>
                        <th>Ghế</th>
                        <th>Giá</th>
                        <th>Trạng thái vé</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($donHang->ves as $ve)
                    @php($gs = $ve->gheTheoSuat)
                    <tr>
                        <td><strong>{{ $ve->ma_ve }}</strong></td>
                        <td>{{ $gs->suat->noiDung->tieu_de }}</td>
                        <td>{{ $gs->suat->phongChieu->diaDiem->ten_dia_diem }} – {{ $gs->suat->phongChieu->ten_phong }}</td>
                        <td>{{ $gs->suat->bat_dau_luc->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ $gs->gheNgoi->hang_ghe }}{{ $gs->gheNgoi->so_ghe }}</strong></td>
                        <td>{{ number_format($ve->gia_ban, 0, ',', '.') }}đ</td>
                        <td><span class="pill">{{ $ve->trang_thai }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @elseif($donHang->gheDangGiu->isNotEmpty())
        @include('orders._ghe_dang_giu')
    @endif

    <div class="m5-card">
        <p style="font-size:18px;margin-bottom:14px">Tổng tiền: <strong style="color:var(--green);font-size:22px">{{ number_format($donHang->tong_tien, 0, ',', '.') }}đ</strong></p>

        @if($donHang->thanhToans->isNotEmpty())
            <h2 style="margin-top:16px">Lịch sử giao dịch thanh toán</h2>
            <ul style="margin:0 0 18px;padding-left:20px">
                @foreach($donHang->thanhToans as $tt)
                    <li><strong>{{ strtoupper($tt->nha_cung_cap) }}</strong> · Mã GD: <code>{{ $tt->ma_giao_dich ?? '—' }}</code> · {{ number_format($tt->so_tien, 0, ',', '.') }}đ · {{ $tt->trang_thai }}</li>
                @endforeach
            </ul>
        @endif

        @if(in_array($donHang->trang_thai, ['cho_thanh_toan', 'da_thanh_toan']))
            <form method="POST" action="{{ route('admin.orders.status', $donHang) }}" class="form-actions" style="margin-top:12px">
                @csrf @method('PATCH')
                @if($donHang->trang_thai === 'cho_thanh_toan')
                    <button name="trang_thai" value="da_thanh_toan" class="btn">✓ Xác nhận đã thanh toán</button>
                @endif
                <button name="trang_thai" value="da_huy" class="btn btn-danger"
                        onclick="return confirm('Hủy đơn này và giải phóng ghế?')">✕ Hủy đơn</button>
            </form>
        @else
            <p class="muted" style="margin:0;font-size:13.5px">Đơn ở trạng thái cuối, không thể thay đổi.</p>
        @endif
    </div>
</section>
@endsection
