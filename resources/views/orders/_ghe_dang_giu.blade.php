{{-- Ghế đang được giữ của đơn chờ thanh toán (chưa có vé) --}}
<div class="alert error" style="margin-top:0;margin-bottom:18px;background:#fef3c7;color:#92400e;border-color:#fde68a">
  ⏳ Đơn đang giữ ghế{{ $donHang->het_han_luc ? ' đến '.$donHang->het_han_luc->format('H:i d/m/Y') : '' }}. Vé chỉ được phát hành sau khi thanh toán.
</div>
<div class="table-wrap" style="margin-bottom:18px">
  <table>
    <thead>
      <tr><th>Phim / Sự kiện</th><th>Rạp / Phòng</th><th>Giờ chiếu</th><th>Ghế</th><th>Giá</th></tr>
    </thead>
    <tbody>
    @foreach($donHang->gheDangGiu as $gs)
      <tr>
        <td><strong>{{ $gs->suat->noiDung->tieu_de }}</strong></td>
        <td>{{ $gs->suat->phongChieu->diaDiem->ten_dia_diem }} – {{ $gs->suat->phongChieu->ten_phong }}</td>
        <td>{{ $gs->suat->bat_dau_luc->format('d/m/Y H:i') }}</td>
        <td><strong>{{ $gs->gheNgoi->hang_ghe }}{{ $gs->gheNgoi->so_ghe }}</strong></td>
        <td>{{ number_format($gs->gia_ve, 0, ',', '.') }}đ</td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>
