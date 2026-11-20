@extends('layouts.app')
@section('title', 'Quản lý phòng chiếu')
@section('content')
<section class="admin-page">
@include('admin.m3-nav')
<div class="section-heading"><div><span class="eyebrow">KHÔNG GIAN TRẢI NGHIỆM</span><h1>Phòng chiếu & ghế</h1><p class="muted">{{ $phongs->total() }} phòng phù hợp</p></div><a class="btn" href="{{ route('admin.phong-chieu.create') }}">+ Thêm phòng</a></div>
<form class="filter-bar" method="GET">
<label class="sr-only" for="q">Tên phòng</label><input id="q" name="q" value="{{ request('q') }}" placeholder="Tìm tên phòng" maxlength="100">
<label class="sr-only" for="dia_diem_id">Rạp</label><select id="dia_diem_id" name="dia_diem_id"><option value="">Tất cả rạp</option>@foreach($raps as $rap)<option value="{{ $rap->id }}" @selected(request('dia_diem_id') == $rap->id)>{{ $rap->ten_dia_diem }}</option>@endforeach</select>
<label class="sr-only" for="trang_thai">Trạng thái</label><select id="trang_thai" name="trang_thai"><option value="">Tất cả trạng thái</option><option value="hoat_dong" @selected(request('trang_thai') === 'hoat_dong')>Hoạt động</option><option value="bao_tri" @selected(request('trang_thai') === 'bao_tri')>Bảo trì</option></select>
<button class="btn">Lọc</button><a href="{{ route('admin.phong-chieu.index') }}">Đặt lại</a>
</form>
<div class="table-wrap"><table class="m3-table"><thead><tr><th>Phòng</th><th>Rạp</th><th>Loại</th><th>Số ghế</th><th>Số suất</th><th>Trạng thái</th><th>Thao tác</th></tr></thead><tbody>
@forelse($phongs as $phong)<tr><td><a href="{{ route('admin.phong-chieu.show', $phong) }}">{{ $phong->ten_phong }}</a></td><td>{{ $phong->diaDiem->ten_dia_diem }}</td><td>{{ $phong->loai_phong === 'san_khau' ? 'Sân khấu' : $phong->loai_phong }}</td><td>{{ $phong->ghe_ngois_count }}</td><td>{{ $phong->suats_count }}</td><td>{{ $phong->trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Bảo trì' }}</td><td><div class="m3-actions"><a href="{{ route('admin.phong-chieu.show', $phong) }}">Sơ đồ ghế</a><a href="{{ route('admin.phong-chieu.edit', $phong) }}">Sửa</a><form method="POST" action="{{ route('admin.phong-chieu.destroy', $phong) }}" onsubmit="return confirm('Xóa phòng và toàn bộ ghế chưa sử dụng?')">@csrf @method('DELETE')<button class="btn btn-small btn-outline">Xóa</button></form></div></td></tr>
@empty<tr><td colspan="7">Chưa có phòng phù hợp.</td></tr>@endforelse
</tbody></table></div>
@include('partials.pagination', ['paginator' => $phongs])
</section>
@endsection
