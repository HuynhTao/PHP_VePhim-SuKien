@extends('layouts.app')
@section('title', 'Đơn ' . $donHang->ma_don_hang)
@section('content')
<div class="py-4">
  <a href="{{ route('orders.index') }}" class="d-block mb-3">← Quay lại lịch sử</a>

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
      <h1 class="h4 mb-1">Đơn {{ $donHang->ma_don_hang }}</h1>
      <div class="text-muted">Đặt lúc {{ $donHang->dat_luc?->format('d/m/Y H:i') }}</div>
    </div>
    @include('orders._badge', ['trangThai' => $donHang->trang_thai])
  </div>

  <div class="table-responsive mb-3">
    <table class="table align-middle">
      <thead>
        <tr><th>Mã vé</th><th>Phim / Sự kiện</th><th>Rạp / Phòng</th><th>Giờ chiếu</th><th>Ghế</th><th>Giá</th></tr>
      </thead>
      <tbody>
      @foreach($donHang->ves as $ve)
        @php($gs = $ve->gheTheoSuat)
        <tr>
          <td>{{ $ve->ma_ve }}</td>
          <td>{{ $gs->suat->noiDung->tieu_de }}</td>
          <td>{{ $gs->suat->phongChieu->diaDiem->ten_dia_diem }} – {{ $gs->suat->phongChieu->ten_phong }}</td>
          <td>{{ $gs->suat->bat_dau_luc->format('d/m/Y H:i') }}</td>
          <td>{{ $gs->gheNgoi->hang_ghe }}{{ $gs->gheNgoi->so_ghe }}</td>
          <td>{{ number_format($ve->gia_ban, 0, ',', '.') }}đ</td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>

  <p class="fs-5">Tổng tiền: <strong>{{ number_format($donHang->tong_tien, 0, ',', '.') }}đ</strong></p>
</div>
@endsection