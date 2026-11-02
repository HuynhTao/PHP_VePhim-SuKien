@extends('layouts.app')

@section('title', $rap->exists ? 'Sửa rạp' : 'Thêm rạp')

@section('content')
<section class="form-card account-card">
    <a class="back-link" href="{{ route('admin.dia-diem.index') }}">
        ← Danh sách rạp
    </a>

    <h1>{{ $rap->exists ? 'Sửa rạp' : 'Thêm rạp' }}</h1>

    <form method="POST"
          action="{{ $rap->exists
              ? route('admin.dia-diem.update', $rap)
              : route('admin.dia-diem.store') }}">
        @csrf

        @if($rap->exists)
            @method('PUT')
        @endif

        <label for="ten_dia_diem">Tên rạp</label>
        <input id="ten_dia_diem"
               name="ten_dia_diem"
               value="{{ old('ten_dia_diem', $rap->ten_dia_diem) }}"
               maxlength="255"
               required>

        <label for="dia_chi">Địa chỉ</label>
        <input id="dia_chi"
               name="dia_chi"
               value="{{ old('dia_chi', $rap->dia_chi) }}"
               maxlength="255"
               required>

        <label for="tinh_thanh">Tỉnh/thành phố</label>
        <input id="tinh_thanh"
               name="tinh_thanh"
               value="{{ old('tinh_thanh', $rap->tinh_thanh) }}"
               maxlength="100"
               required>

        <label for="so_dien_thoai">Số điện thoại</label>
        <input id="so_dien_thoai"
               name="so_dien_thoai"
               type="tel"
               value="{{ old('so_dien_thoai', $rap->so_dien_thoai) }}"
               maxlength="20">

        <label for="trang_thai">Trạng thái</label>
        <select id="trang_thai" name="trang_thai" required>
            <option value="hoat_dong"
                @selected(old('trang_thai', $rap->trang_thai) === 'hoat_dong')>
                Hoạt động
            </option>
            <option value="tam_dung"
                @selected(old('trang_thai', $rap->trang_thai) === 'tam_dung')>
                Tạm dừng
            </option>
        </select>

        <div class="form-actions">
            <button class="btn" type="submit">Lưu rạp</button>
            <a class="btn btn-outline"
               href="{{ route('admin.dia-diem.index') }}">
                Hủy
            </a>
        </div>
    </form>
</section>
@endsection