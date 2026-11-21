@extends('layouts.app')
@section('title', $phong->exists ? 'Sửa phòng' : 'Thêm phòng')
@section('content')
<section class="form-card account-card m3-form">
<a class="back-link" href="{{ route('admin.phong-chieu.index') }}">← Danh sách phòng</a>
<h1>{{ $phong->exists ? 'Sửa phòng' : 'Tạo phòng & sinh ghế' }}</h1>
@if($raps->isEmpty())<p>Hãy <a href="{{ route('admin.dia-diem.create') }}">thêm rạp</a> trước khi tạo phòng.</p>@endif
<form method="POST" action="{{ $phong->exists ? route('admin.phong-chieu.update', $phong) : route('admin.phong-chieu.store') }}">
@csrf @if($phong->exists) @method('PUT') @endif
<div class="m3-grid">
<div><label for="dia_diem_id">Rạp</label><select id="dia_diem_id" name="dia_diem_id" required><option value="">Chọn rạp</option>@foreach($raps as $rap)<option value="{{ $rap->id }}" @selected(old('dia_diem_id', $phong->dia_diem_id) == $rap->id)>{{ $rap->ten_dia_diem }}{{ $rap->trang_thai === 'tam_dung' ? ' (tạm dừng)' : '' }}</option>@endforeach</select></div>
<div><label for="ten_phong">Tên phòng</label><input id="ten_phong" name="ten_phong" value="{{ old('ten_phong', $phong->ten_phong) }}" required maxlength="100"></div>
<div><label for="loai_phong">Loại phòng</label><select id="loai_phong" name="loai_phong">@foreach(['2D' => '2D', '3D' => '3D', 'IMAX' => 'IMAX', 'san_khau' => 'Sân khấu'] as $key => $label)<option value="{{ $key }}" @selected(old('loai_phong', $phong->loai_phong) === $key)>{{ $label }}</option>@endforeach</select></div>
<div><label for="trang_thai">Trạng thái</label><select id="trang_thai" name="trang_thai"><option value="hoat_dong" @selected(old('trang_thai', $phong->trang_thai) === 'hoat_dong')>Hoạt động</option><option value="bao_tri" @selected(old('trang_thai', $phong->trang_thai) === 'bao_tri')>Bảo trì</option></select></div>
@if(!$phong->exists)
<div><label for="so_hang">Số hàng (1–26)</label><input id="so_hang" name="so_hang" type="number" min="1" max="26" value="{{ old('so_hang', 5) }}" required></div>
<div><label for="ghe_moi_hang">Ghế mỗi hàng (1–50)</label><input id="ghe_moi_hang" name="ghe_moi_hang" type="number" min="1" max="50" value="{{ old('ghe_moi_hang', 10) }}" required></div>
<div><label for="so_hang_vip">Số hàng VIP cuối phòng</label><input id="so_hang_vip" name="so_hang_vip" type="number" min="0" max="26" value="{{ old('so_hang_vip', 1) }}" required></div>
<div><label>Tổng sức chứa dự kiến</label><output id="capacity">50 ghế</output></div>
<p class="m3-note wide">Mã ghế A1, A2…; tổng sức chứa = số hàng × ghế mỗi hàng. Có thể sửa từng ghế trước khi lập suất đầu tiên.</p>
@endif
</div>
<div class="form-actions"><button class="btn">{{ $phong->exists ? 'Lưu thay đổi' : 'Tạo phòng và ghế' }}</button><a href="{{ route('admin.phong-chieu.index') }}">Hủy</a></div>
</form></section>
@if(!$phong->exists)
<script>
const rows = document.getElementById('so_hang'), seats = document.getElementById('ghe_moi_hang');
function capacity() { document.getElementById('capacity').textContent = ((+rows.value || 0) * (+seats.value || 0)) + ' ghế'; }
rows.addEventListener('input', capacity); seats.addEventListener('input', capacity); capacity();
</script>
@endif
@endsection
