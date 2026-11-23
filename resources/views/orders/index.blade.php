@extends('layouts.app')
@section('title', 'Lịch sử đặt vé — Vé của tôi')
@section('content')
<section class="admin-page">
    <div class="section-heading">
        <div>
            <span class="eyebrow">TÀI KHOẢN CỦA TÔI · MODULE 5</span>
            <h1>Lịch sử đặt vé</h1>
            <p class="muted">Bạn có {{ $donHangs->total() }} đơn đặt vé</p>
        </div>
        <a class="btn" href="{{ route('datve.index') }}">+ Đặt vé mới</a>
    </div>

    <form method="GET" action="{{ route('orders.index') }}" class="filter-bar">
        <label class="sr-only" for="trang_thai">Trạng thái đơn</label>
        <select id="trang_thai" name="trang_thai" onchange="this.form.submit()">
            <option value="">Tất cả trạng thái</option>
            @foreach(['cho_thanh_toan'=>'Chờ thanh toán','da_thanh_toan'=>'Đã thanh toán','da_huy'=>'Đã hủy','het_han'=>'Hết hạn'] as $k => $v)
                <option value="{{ $k }}" @selected(request('trang_thai') === $k)>{{ $v }}</option>
            @endforeach
        </select>
        <button class="btn">Lọc</button>
        @if(request()->filled('trang_thai'))
            <a class="btn btn-outline btn-small" href="{{ route('orders.index') }}">Đặt lại</a>
        @endif
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Mã đơn</th>
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
                    <td>{{ $don->dat_luc?->format('d/m/Y H:i') }}</td>
                    <td>{{ $don->ves_count }}</td>
                    <td><strong>{{ number_format($don->tong_tien, 0, ',', '.') }}đ</strong></td>
                    <td>@include('orders._badge', ['trangThai' => $don->trang_thai])</td>
                    <td>
                        <a class="btn btn-small btn-outline" href="{{ route('orders.show', $don) }}">Chi tiết</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-state">Bạn chưa có đơn đặt vé nào.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @include('partials.pagination', ['paginator' => $donHangs])
</section>
@endsection
