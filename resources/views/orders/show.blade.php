@extends('layouts.app')
@section('title', 'Chi tiết đơn ' . $donHang->ma_don_hang)
@section('content')
<section class="admin-page">
    <a href="{{ route('orders.index') }}" class="back-link">← Quay lại lịch sử đặt vé</a>

    <div class="section-heading">
        <div>
            <span class="eyebrow">CHI TIẾT ĐƠN ĐẶT VÉ · MODULE 5</span>
            <h1>Đơn {{ $donHang->ma_don_hang }}</h1>
            <p class="muted">Đặt lúc {{ $donHang->dat_luc?->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            @include('orders._badge', ['trangThai' => $donHang->trang_thai])
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
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @elseif($donHang->gheDangGiu->isNotEmpty())
        @include('orders._ghe_dang_giu')
    @endif

    <div class="m5-card">
        <p style="font-size:18px;margin-bottom:16px">Tổng tiền thanh toán: <strong style="color:var(--green);font-size:22px">{{ number_format($donHang->tong_tien, 0, ',', '.') }}đ</strong></p>

        @if($donHang->trang_thai === 'cho_thanh_toan' && $donHang->gheDangGiu->isNotEmpty())
            <div class="form-actions" style="margin-top:0">
                <a class="btn" href="{{ route('datve.thanh-toan', $donHang) }}">Tiếp tục thanh toán</a>
                <form method="POST" action="{{ route('datve.huy', $donHang) }}"
                      onsubmit="return confirm('Hủy giữ ghế và hủy đơn này?')">
                    @csrf
                    <button class="btn btn-outline">Hủy đơn</button>
                </form>
            </div>
        @elseif($coTheHuy)
            <form method="POST" action="{{ route('orders.cancel', $donHang) }}"
                  onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn này và trả lại ghế?')">
                @csrf
                <button class="btn btn-danger">Hủy đơn &amp; Hoàn ghế</button>
            </form>
        @else
            <p class="muted" style="margin:0;font-size:13.5px">Đơn này không thể hủy (chỉ hủy được trước giờ chiếu tối thiểu 2 giờ).</p>
        @endif
    </div>
</section>
@endsection
