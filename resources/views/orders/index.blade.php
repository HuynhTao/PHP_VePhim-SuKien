@extends('layouts.app')
@section('title', 'Lịch sử đặt vé')
@section('content')
<div class="py-4">
  <h1 class="h3 mb-3">Lịch sử đặt vé</h1>

  <form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-4">
      <select name="trang_thai" class="form-select" onchange="this.form.submit()">
        <option value="">Tất cả trạng thái</option>
        @foreach(['cho_thanh_toan'=>'Chờ thanh toán','da_thanh_toan'=>'Đã thanh toán','da_huy'=>'Đã hủy','het_han'=>'Hết hạn'] as $k => $v)
          <option value="{{ $k }}" @selected(request('trang_thai') === $k)>{{ $v }}</option>
        @endforeach
      </select>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead><tr><th>Mã đơn</th><th>Ngày đặt</th><th>Số vé</th><th>Tổng tiền</th><th>Trạng thái</th><th></th></tr></thead>
      <tbody>
      @forelse($donHangs as $don)
        <tr>
          <td>{{ $don->ma_don_hang }}</td>
          <td>{{ $don->dat_luc?->format('d/m/Y H:i') }}</td>
          <td>{{ $don->ves_count }}</td>
          <td>{{ number_format($don->tong_tien, 0, ',', '.') }}đ</td>
          <td>@include('orders._badge', ['trangThai' => $don->trang_thai])</td>
          <td><a class="btn btn-sm btn-outline-success" href="{{ route('orders.show', $don) }}">Chi tiết</a></td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted py-5">Bạn chưa có đơn đặt vé nào.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  {{ $donHangs->links('pagination::bootstrap-5') }}
</div>
@endsection