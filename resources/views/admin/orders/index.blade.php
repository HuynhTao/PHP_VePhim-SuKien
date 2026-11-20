@extends('layouts.app')
@section('title', 'Quản lý đơn đặt vé')
@section('content')
<div class="py-4">
  <h1 class="h3 mb-1">Quản lý đơn đặt vé</h1>
  <p class="text-muted">{{ $donHangs->total() }} đơn phù hợp</p>

  <form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-4">
      <input type="text" name="q" value="{{ request('q') }}" class="form-control"
             placeholder="Tìm mã đơn, tên hoặc email" maxlength="100">
    </div>
    <div class="col-6 col-md-2">
      <select name="trang_thai" class="form-select">
        <option value="">Tất cả trạng thái</option>
        @foreach(['cho_thanh_toan'=>'Chờ thanh toán','da_thanh_toan'=>'Đã thanh toán','da_huy'=>'Đã hủy','het_han'=>'Hết hạn'] as $k => $v)
          <option value="{{ $k }}" @selected(request('trang_thai') === $k)>{{ $v }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-6 col-md-2"><input type="date" name="tu_ngay" value="{{ request('tu_ngay') }}" class="form-control" title="Từ ngày"></div>
    <div class="col-6 col-md-2"><input type="date" name="den_ngay" value="{{ request('den_ngay') }}" class="form-control" title="Đến ngày"></div>
    <div class="col-6 col-md-2 d-flex gap-2">
      <button class="btn btn-success flex-fill">Lọc</button>
      <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Đặt lại</a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr><th>Mã đơn</th><th>Khách hàng</th><th>Ngày đặt</th><th>Số vé</th><th>Tổng tiền</th><th>Trạng thái</th><th></th></tr>
      </thead>
      <tbody>
      @forelse($donHangs as $don)
        <tr>
          <td>{{ $don->ma_don_hang }}</td>
          <td>{{ $don->nguoiDung->ho_ten }}<br><small class="text-muted">{{ $don->nguoiDung->email }}</small></td>
          <td>{{ $don->dat_luc?->format('d/m/Y H:i') }}</td>
          <td>{{ $don->ves_count }}</td>
          <td>{{ number_format($don->tong_tien, 0, ',', '.') }}đ</td>
          <td>@include('orders._badge', ['trangThai' => $don->trang_thai])</td>
          <td><a class="btn btn-sm btn-outline-success" href="{{ route('admin.orders.show', $don) }}">Chi tiết</a></td>
        </tr>
      @empty
        <tr><td colspan="7" class="text-center text-muted py-5">Không có đơn phù hợp.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  {{ $donHangs->links('pagination::bootstrap-5') }}
</div>
@endsection