@extends('layouts.app')

@section('title', 'Quản lý rạp')

@section('content')
<section class="admin-page">
@include('admin.m3-nav')
    <div class="section-heading">
        <div>
            <span class="eyebrow">QUẢN TRỊ HỆ THỐNG</span>
            <h1>Quản lý rạp</h1>
            <p class="muted">{{ $raps->total() }} rạp phù hợp</p>
        </div>

        <a class="btn" href="{{ route('admin.dia-diem.create') }}">
            + Thêm rạp
        </a>
    </div>

    <form class="filter-bar"
          method="GET"
          action="{{ route('admin.dia-diem.index') }}">
        <label class="sr-only" for="q">Tìm rạp</label>
        <input id="q" name="q"
               value="{{ request('q') }}"
               placeholder="Tên rạp, địa chỉ, tỉnh/thành"
               maxlength="100">

        <label class="sr-only" for="trang_thai">Trạng thái</label>
        <select id="trang_thai" name="trang_thai">
            <option value="">Tất cả trạng thái</option>
            <option value="hoat_dong"
                @selected(request('trang_thai') === 'hoat_dong')>
                Hoạt động
            </option>
            <option value="tam_dung"
                @selected(request('trang_thai') === 'tam_dung')>
                Tạm dừng
            </option>
        </select>

        <button class="btn" type="submit">Tìm kiếm</button>
        <a href="{{ route('admin.dia-diem.index') }}">Đặt lại</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tên rạp</th>
                    <th>Địa chỉ</th>
                    <th>Điện thoại</th>
                    <th>Số phòng</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($raps as $rap)
                    <tr>
                        <td>{{ $rap->ten_dia_diem }}</td>
                        <td>
                            {{ $rap->dia_chi }},
                            {{ $rap->tinh_thanh }}
                        </td>
                        <td>{{ $rap->so_dien_thoai ?: '—' }}</td>
                        <td>{{ $rap->phong_chieus_count }}</td>
                        <td>
                            {{ $rap->trang_thai === 'hoat_dong'
                                ? 'Hoạt động' : 'Tạm dừng' }}
                        </td>
                        <td>
                            <a href="{{ route('admin.dia-diem.edit', $rap) }}">
                                Sửa
                            </a>

                            <a href="{{ route('admin.dia-diem.map', $rap) }}">Bản đồ</a>
                            <a href="{{ route('admin.phong-chieu.index', ['dia_diem_id' => $rap->id]) }}">Phòng</a>

                            <form method="POST"
                                  action="{{ route('admin.dia-diem.destroy', $rap) }}"
                                  onsubmit="return confirm('Bạn có chắc muốn xóa rạp này?')">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-small btn-outline"
                                        type="submit">
                                    Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">
                            Không tìm thấy rạp.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('partials.pagination', ['paginator' => $raps])
</section>
@endsection