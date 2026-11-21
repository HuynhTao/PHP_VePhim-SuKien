@extends('layouts.app')
@section('title', 'Đơn ' . $donHang->ma_don_hang)
@section('content')
<div class="py-4">
  <a href="{{ route('admin.orders.index') }}" class="d-block mb-3">← Quay lại danh sách đơn</a>

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
      <h1 class="h4 mb-1">Đơn {{ $donHang->ma_don_hang }}</h1>
      <div class="text-muted">Đặt lúc {{ $donHang->dat_luc?->format('d/m/Y H:i') }}</div>
    </div>
    @include('orders._badge', ['trangThai' => $donHang->trang_thai])
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <h2 class="h6">Khách hàng</h2>
      <div>{{ $donHang->nguoiDung->ho_ten }} · {{ $donHang->nguoiDung->email }}
        @if($donHang->nguoiDung->so_dien_thoai) · {{ $donHang->nguoiDung->so_dien_thoai }} @endif
      </div>
    </div>
  </div>

@if($donHang->ves->isNotEmpty())
  <div class="table-responsive mb-3">
    <table class="table align-middle">
      <thead>
        <tr><th>Mã vé</th><th>Phim / Sự kiện</th><th>Rạp / Phòng</th><th>Giờ chiếu</th><th>Ghế</th><th>Giá</th><th>TT vé</th></tr>
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
          <td>{{ $ve->trang_thai }}</td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
@elseif($donHang->gheDangGiu->isNotEmpty())
  @include('orders._ghe_dang_giu')
@endif

  <p class="fs-5">Tổng tiền: <strong>{{ number_format($donHang->tong_tien, 0, ',', '.') }}đ</strong></p>

  @if($donHang->thanhToans->isNotEmpty())
    <h2 class="h6">Giao dịch thanh toán</h2>
    <ul class="mb-3">
      @foreach($donHang->thanhToans as $tt)
        <li>{{ $tt->nha_cung_cap }} · {{ $tt->ma_giao_dich ?? '—' }} · {{ number_format($tt->so_tien, 0, ',', '.') }}đ · {{ $tt->trang_thai }}</li>
      @endforeach
    </ul>
  @endif

  @if(in_array($donHang->trang_thai, ['cho_thanh_toan', 'da_thanh_toan']))
    <form method="POST" action="{{ route('admin.orders.status', $donHang) }}" class="d-flex gap-2 flex-wrap">
      @csrf @method('PATCH')
      @if($donHang->trang_thai === 'cho_thanh_toan')
        <button name="trang_thai" value="da_thanh_toan" class="btn btn-success">Xác nhận đã thanh toán</button>
      @endif
      <button name="trang_thai" value="da_huy" class="btn btn-danger"
              onclick="return confirm('Hủy đơn và nhả ghế?')">Hủy đơn</button>
    </form>
  @else
    <p class="text-muted small">Đơn ở trạng thái cuối, không thể đổi.</p>
  @endif
</div>
@endsection