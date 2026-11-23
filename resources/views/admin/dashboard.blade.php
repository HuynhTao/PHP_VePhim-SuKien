@extends('layouts.app')
@section('title', 'Dashboard thống kê — Quản trị hệ thống')
@section('content')
<section class="admin-page">
    @include('admin.m3-nav')
    <div class="section-heading">
        <div>
            <span class="eyebrow">QUẢN TRỊ HỆ THỐNG · MODULE 5</span>
            <h1>Dashboard thống kê</h1>
            <p class="muted">Tổng hợp doanh thu, vé bán ra và hiệu suất suất chiếu theo thời gian thực</p>
        </div>
        <a class="btn btn-outline btn-small" href="{{ route('admin.orders.index') }}">Xem toàn bộ đơn hàng →</a>
    </div>

    <div class="m5-stats-grid">
        <div class="m5-stat-card">
            <div class="stat-label">Tổng doanh thu</div>
            <div class="stat-value accent">{{ number_format($tongDoanhThu, 0, ',', '.') }}đ</div>
        </div>
        <div class="m5-stat-card">
            <div class="stat-label">Tổng đơn hàng</div>
            <div class="stat-value">{{ $tongDon }}</div>
        </div>
        <div class="m5-stat-card">
            <div class="stat-label">Vé đã phát hành</div>
            <div class="stat-value">{{ $veDaBan }}</div>
        </div>
        <div class="m5-stat-card">
            <div class="stat-label">Thành viên · Suất mở bán</div>
            <div class="stat-value">{{ $nguoiDung }} · {{ $suatSapToi }}</div>
        </div>
    </div>

    <div class="m5-charts-grid">
        <div class="m5-card" style="margin-bottom:0">
            <h2>Biểu đồ doanh thu 7 ngày gần nhất (Đơn đã thanh toán)</h2>
            <canvas id="chartDoanhThu" height="130"></canvas>
        </div>
        <div class="m5-card" style="margin-bottom:0">
            <h2>Tỷ lệ đơn theo trạng thái</h2>
            <canvas id="chartTrangThai" height="200"></canvas>
        </div>
    </div>

    <div class="m5-card">
        <h2>Top 5 Phim / Sự kiện bán chạy nhất</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tên Phim / Sự kiện</th>
                        <th>Loại</th>
                        <th>Số vé đã bán</th>
                        <th>Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($top as $row)
                    <tr>
                        <td><strong>{{ $row->tieu_de }}</strong></td>
                        <td>{{ $row->loai_noi_dung === 'phim' ? 'Phim điện ảnh' : 'Sự kiện' }}</td>
                        <td><strong>{{ $row->so_ve }}</strong> vé</td>
                        <td><strong style="color:var(--green)">{{ number_format($row->doanh_thu, 0, ',', '.') }}đ</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-state">Chưa có vé nào được bán.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
  new Chart(document.getElementById('chartDoanhThu'), {
    type: 'bar',
    data: {
      labels: @json($nhanNgay),
      datasets: [{
        label: 'Doanh thu (VNĐ)',
        data: @json($doanhThu),
        backgroundColor: '#d82d8b',
        borderRadius: 8
      }]
    },
    options: {
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } }
    }
  });

  new Chart(document.getElementById('chartTrangThai'), {
    type: 'doughnut',
    data: {
      labels: @json($nhanTrangThai),
      datasets: [{
        data: @json($donTheoTrangThai),
        backgroundColor: ['#ff9f43', '#d82d8b', '#9ca3af', '#312e81']
      }]
    },
    options: {
      plugins: { legend: { position: 'bottom' } }
    }
  });
</script>
@endpush
