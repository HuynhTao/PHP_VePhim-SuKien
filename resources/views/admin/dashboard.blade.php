@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="py-4">
  <h1 class="h3 mb-3">Dashboard thống kê</h1>

  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
      <div class="text-muted small">Doanh thu</div>
      <div class="fs-4 fw-bold text-success">{{ number_format($tongDoanhThu, 0, ',', '.') }}đ</div>
    </div></div></div>
    <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
      <div class="text-muted small">Tổng đơn hàng</div>
      <div class="fs-4 fw-bold">{{ $tongDon }}</div>
    </div></div></div>
    <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
      <div class="text-muted small">Vé đã bán</div>
      <div class="fs-4 fw-bold">{{ $veDaBan }}</div>
    </div></div></div>
    <div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
      <div class="text-muted small">Người dùng · Suất sắp tới</div>
      <div class="fs-4 fw-bold">{{ $nguoiDung }} · {{ $suatSapToi }}</div>
    </div></div></div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-12 col-lg-8">
      <div class="card h-100"><div class="card-body">
        <h2 class="h6 mb-3">Doanh thu 7 ngày gần nhất (đơn đã thanh toán)</h2>
        <canvas id="chartDoanhThu" height="130"></canvas>
      </div></div>
    </div>
    <div class="col-12 col-lg-4">
      <div class="card h-100"><div class="card-body">
        <h2 class="h6 mb-3">Đơn theo trạng thái</h2>
        <canvas id="chartTrangThai" height="200"></canvas>
      </div></div>
    </div>
  </div>

  <div class="card"><div class="card-body">
    <h2 class="h6 mb-3">Top 5 phim / sự kiện bán chạy</h2>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>Tên</th><th>Loại</th><th>Số vé</th><th>Doanh thu</th></tr></thead>
        <tbody>
        @forelse($top as $row)
          <tr>
            <td>{{ $row->tieu_de }}</td>
            <td>{{ $row->loai_noi_dung === 'phim' ? 'Phim' : 'Sự kiện' }}</td>
            <td>{{ $row->so_ve }}</td>
            <td>{{ number_format($row->doanh_thu, 0, ',', '.') }}đ</td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-center text-muted py-4">Chưa có vé nào được bán.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div></div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
  new Chart(document.getElementById('chartDoanhThu'), {
    type: 'bar',
    data: {
      labels: @json($nhanNgay),
      datasets: [{ label: 'Doanh thu (đ)', data: @json($doanhThu), backgroundColor: '#136858' }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
  });

  new Chart(document.getElementById('chartTrangThai'), {
    type: 'doughnut',
    data: {
      labels: @json($nhanTrangThai),
      datasets: [{ data: @json($donTheoTrangThai), backgroundColor: ['#f1ad6c', '#136858', '#9aa5a0', '#33413f'] }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
  });
</script>
@endpush