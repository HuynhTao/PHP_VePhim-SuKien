@extends('layouts.app')
@section('title', 'Sơ đồ ghế')
@section('content')
<section class="admin-page">
@include('admin.m3-nav')
<div class="section-heading"><div><span class="eyebrow">{{ $phong->diaDiem->ten_dia_diem }}</span><h1>{{ $phong->ten_phong }}</h1><p class="muted">{{ $ghes->count() }} ghế · {{ $ghes->where('dang_su_dung', true)->count() }} đang sử dụng · {{ $phong->suats_count }} suất</p></div><a class="btn btn-outline" href="{{ route('admin.phong-chieu.edit', $phong) }}">Sửa phòng</a></div>
<div class="m3-panel"><div class="screen">MÀN HÌNH / SÂN KHẤU</div><div class="seat-scroll">
@forelse($ghes->groupBy('hang_ghe') as $row => $seats)<div class="seat-row"><strong class="seat">{{ $row }}</strong>@foreach($seats as $ghe)<span @class(['seat', 'vip' => $ghe->loai_ghe === 'vip', 'disabled' => !$ghe->dang_su_dung]) title="{{ $ghe->loai_ghe === 'vip' ? 'VIP' : 'Thường' }}{{ !$ghe->dang_su_dung ? ' — Ngừng sử dụng' : '' }}">{{ $ghe->hang_ghe }}{{ $ghe->so_ghe }}</span>@endforeach</div>@empty<p>Phòng chưa có ghế.</p>@endforelse
</div><p class="m3-note">Xanh: thường · Vàng: VIP · Xám: ngừng sử dụng. Đây là sơ đồ ghế cố định, không phải trạng thái bán vé.</p></div>
@if($phong->suats_count > 0)
<div class="alert">Phòng đã có suất nên sơ đồ được khóa để giữ nguyên thông tin ghế. <a href="{{ route('admin.suat.index', ['phong_chieu_id' => $phong->id]) }}">Xem các suất</a>.</div>
@else
<details class="m3-panel"><summary><strong>Tạo lại toàn bộ sơ đồ ghế</strong></summary><p class="m3-note">Thao tác thay thế các ghế hiện tại. Chỉ dùng trước khi phòng có suất.</p>
<form method="POST" action="{{ route('admin.ghe.generate', $phong) }}" onsubmit="return confirm('Thay thế toàn bộ sơ đồ ghế hiện tại?')">@csrf
<div class="m3-grid"><div><label for="so_hang">Số hàng</label><input id="so_hang" name="so_hang" type="number" min="1" max="26" value="{{ old('so_hang', 5) }}" required></div><div><label for="ghe_moi_hang">Ghế mỗi hàng</label><input id="ghe_moi_hang" name="ghe_moi_hang" type="number" min="1" max="50" value="{{ old('ghe_moi_hang', 10) }}" required></div><div><label for="so_hang_vip">Số hàng VIP cuối phòng</label><input id="so_hang_vip" name="so_hang_vip" type="number" min="0" max="26" value="{{ old('so_hang_vip', 1) }}" required></div></div><button class="btn form-submit">Tạo lại ghế</button></form></details>
<div class="m3-panel"><h2>Thêm một ghế</h2><form method="POST" action="{{ route('admin.ghe.store', $phong) }}">@csrf
<div class="m3-grid"><div><label for="new_row">Hàng (A–Z)</label><input id="new_row" name="hang_ghe" maxlength="1" pattern="[A-Za-z]" value="{{ old('hang_ghe', 'A') }}" required></div><div><label for="new_number">Số ghế</label><input id="new_number" name="so_ghe" type="number" min="1" max="50" value="{{ old('so_ghe', 1) }}" required></div><div><label for="new_type">Loại ghế</label><select id="new_type" name="loai_ghe"><option value="thuong" @selected(old('loai_ghe') === 'thuong')>Thường</option><option value="vip" @selected(old('loai_ghe') === 'vip')>VIP</option></select></div></div><input type="hidden" name="dang_su_dung" value="1"><button class="btn form-submit">Thêm ghế</button></form></div>
<details class="m3-panel"><summary><strong>Sửa / xóa từng ghế</strong></summary><p class="m3-note">Mỗi dòng có nút Lưu riêng. Ngừng sử dụng một ghế nếu muốn bỏ vị trí đó khỏi các suất được tạo sau này.</p><div class="seat-edit-list">
@foreach($ghes as $ghe)
<form id="seat-{{ $ghe->id }}" method="POST" action="{{ route('admin.ghe.update', [$phong, $ghe]) }}">@csrf @method('PUT')</form>
<div class="seat-editor">
<input form="seat-{{ $ghe->id }}" name="hang_ghe" aria-label="Hàng của ghế {{ $ghe->hang_ghe }}{{ $ghe->so_ghe }}" value="{{ $ghe->hang_ghe }}" required maxlength="1" pattern="[A-Za-z]">
<input form="seat-{{ $ghe->id }}" name="so_ghe" aria-label="Số ghế {{ $ghe->id }}" type="number" value="{{ $ghe->so_ghe }}" required min="1" max="50">
<select form="seat-{{ $ghe->id }}" name="loai_ghe" aria-label="Loại ghế {{ $ghe->id }}"><option value="thuong" @selected($ghe->loai_ghe === 'thuong')>Thường</option><option value="vip" @selected($ghe->loai_ghe === 'vip')>VIP</option></select>
<select form="seat-{{ $ghe->id }}" name="dang_su_dung" aria-label="Sử dụng ghế {{ $ghe->id }}"><option value="1" @selected($ghe->dang_su_dung)>Sử dụng</option><option value="0" @selected(!$ghe->dang_su_dung)>Ngừng dùng</option></select>
<div class="m3-actions"><button form="seat-{{ $ghe->id }}" class="btn btn-small">Lưu</button><form method="POST" action="{{ route('admin.ghe.destroy', [$phong, $ghe]) }}" onsubmit="return confirm('Xóa ghế này?')">@csrf @method('DELETE')<button class="btn btn-small btn-outline">Xóa</button></form></div></div>
@endforeach
</div></details>
@endif
</section>
@endsection
