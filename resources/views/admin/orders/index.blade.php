@extends('layouts.app')
@section('title', 'Quản lý đơn đặt vé')
@section('content')
<section class="admin-page">
    @include('admin.m3-nav')
    <div class="section-heading">
        <div>
            <span class="eyebrow">QUẢN TRỊ HỆ THỐNG · MODULE 5</span>
            <h1>Quản lý đơn đặt vé</h1>
            <p class="muted">{{ $donHangs->total() }} đơn phù hợp</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.orders.index') }}" class="filter-bar">
        <label class="sr-only" for="q">Tìm kiếm đơn</label>
        <input id="q" type="text" name="q" value="{{ request('q') }}"
               placeholder="🔎 Tìm mã đơn, tên hoặc email khách hàng..." maxlength="100">

        <label class="sr-only" for="trang_thai">Trạng thái</label>
        <select id="trang_thai" name="trang_thai">
            <option value="">Tất cả trạng thái</option>
            @foreach(['cho_thanh_toan'=>'Chờ thanh toán','da_thanh_toan'=>'Đã thanh toán','da_huy'=>'Đã hủy','het_han'=>'Hết hạn'] as $k => $v)
                <option value="{{ $k }}" @selected(request('trang_thai') === $k)>{{ $v }}</option>
            @endforeach
        </select>

        <input type="date" name="tu_ngay" value="{{ request('tu_ngay') }}" title="Từ ngày" style="width:auto">
        <input type="date" name="den_ngay" value="{{ request('den_ngay') }}" title="Đến ngày" style="width:auto">

        <button class="btn">Lọc đơn</button>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline btn-small">Đặt lại</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Ngày đặt</th>
                    <th>Số vé</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
            @forelse($donHangs as $don)
                <tr>
                    <td><strong>{{ $don->ma_don_hang }}</strong></td>
                    <td>
                        <strong>{{ $don->nguoiDung->ho_ten }}</strong><br>
                        <small class="muted">{{ $don->nguoiDung->email }}</small>
                    </td>
                    <td>{{ $don->dat_luc?->format('d/m/Y H:i') }}</td>
                    <td>{{ $don->ves_count ?: $don->ghe_dang_giu_count }}</td>
                    <td><strong>{{ number_format($don->tong_tien, 0, ',', '.') }}đ</strong></td>
                    <td>@include('orders._badge', ['trangThai' => $don->trang_thai])</td>
                    <td>
                        <a class="btn btn-small btn-outline" href="{{ route('admin.orders.show', $don) }}">Chi tiết</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">Không có đơn phù hợp.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @include('partials.pagination', ['paginator' => $donHangs])
</section>
@endsection
