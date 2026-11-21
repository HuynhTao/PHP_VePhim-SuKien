{{-- Ghế đang được giữ của đơn chờ thanh toán (chưa có vé) --}}
<div class="alert alert-warning">
  Đơn đang giữ ghế{{ $donHang->het_han_luc ? ' đến '.$donHang->het_han_luc->format('H:i d/m/Y') : '' }}. Vé chỉ được phát hành sau khi thanh toán.
</div>
<div class="table-responsive mb-3">
  <table class="table align-middle">
    <thead>
      <tr><th>Phim / Sự kiện</th><th>Rạp / Phòng</th><th>Giờ chiếu</th><th>Ghế</th><th>Giá</th></tr>
    </thead>
    <tbody>
    @foreach($donHang->gheDangGiu as $gs)
      <tr>
        <td>{{ $gs->suat->noiDung->tieu_de }}</td>
        <td>{{ $gs->suat->phongChieu->diaDiem->ten_dia_diem }} – {{ $gs->suat->phongChieu->ten_phong }}</td>
        <td>{{ $gs->suat->bat_dau_luc->format('d/m/Y H:i') }}</td>
        <td>{{ $gs->gheNgoi->hang_ghe }}{{ $gs->gheNgoi->so_ghe }}</td>
        <td>{{ number_format($gs->gia_ve, 0, ',', '.') }}đ</td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>