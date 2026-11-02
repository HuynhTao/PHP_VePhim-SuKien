@extends('layouts.app')
@section('title', 'Quản lý suất chiếu')
@section('content')
<section class="admin-page">
@include('admin.m3-nav')
<div class="section-heading"><div><span class="eyebrow">LỊCH CHIẾU & BIỂU DIỄN</span><h1>Quản lý suất</h1><p class="muted">{{ $suats->total() }} suất phù hợp · Giờ Việt Nam</p></div><a class="btn" href="{{ route('admin.suat.create') }}">+ Thêm suất</a></div>
<form class="filter-bar" method="GET">
<label class="sr-only" for="q">Tên phim hoặc sự kiện</label><input id="q" name="q" value="{{ request('q') }}" maxlength="100" placeholder="Tên phim / sự kiện">
<label class="sr-only" for="dia_diem_id">Rạp</label><select id="dia_diem_id" name="dia_diem_id"><option value="">Tất cả rạp</option>@foreach($raps as $rap)<option value="{{ $rap->id }}" @selected(request('dia_diem_id') == $rap->id)>{{ $rap->ten_dia_diem }}</option>@endforeach</select>
<label class="sr-only" for="phong_chieu_id">Phòng</label><select id="phong_chieu_id" name="phong_chieu_id"><option value="">Tất cả phòng</option>@foreach($phongs as $phong)<option value="{{ $phong->id }}" @selected(request('phong_chieu_id') == $phong->id)>{{ $phong->diaDiem->ten_dia_diem }} — {{ $phong->ten_phong }}</option>@endforeach</select>
<label class="sr-only" for="ngay">Ngày bắt đầu</label><input id="ngay" name="ngay" type="date" value="{{ request('ngay') }}">
<label class="sr-only" for="trang_thai">Trạng thái</label><select id="trang_thai" name="trang_thai"><option value="">Tất cả trạng thái</option>@foreach(['mo_ban' => 'Mở bán', 'dong' => 'Đóng', 'huy' => 'Hủy'] as $key => $label)<option value="{{ $key }}" @selected(request('trang_thai') === $key)>{{ $label }}</option>@endforeach</select>
<button class="btn">Lọc</button><a href="{{ route('admin.suat.index') }}">Đặt lại</a>
</form>
<div class="table-wrap"><table class="m3-table"><thead><tr><th>Nội dung</th><th>Rạp / phòng</th><th>Bắt đầu</th><th>Kết thúc</th><th>Ghế</th><th>Trạng thái</th><th>Thao tác</th></tr></thead><tbody>
@forelse($suats as $suat)<tr><td><a href="{{ route('admin.suat.show', $suat) }}">{{ $suat->noiDung->tieu_de }}</a><br><small>{{ $suat->noiDung->loai_noi_dung === 'phim' ? 'Phim' : 'Sự kiện' }}</small></td><td>{{ $suat->phongChieu->diaDiem->ten_dia_diem }}<br>{{ $suat->phongChieu->ten_phong }}</td><td>{{ $suat->bat_dau_luc->format('d/m/Y H:i') }}</td><td>{{ $suat->ket_thuc_luc->format('d/m/Y H:i') }}</td><td>{{ $suat->ghe_theo_suats_count }}</td><td>{{ ['mo_ban' => 'Mở bán', 'dong' => 'Đóng', 'huy' => 'Hủy'][$suat->trang_thai] }}</td><td><div class="m3-actions"><a href="{{ route('admin.suat.show', $suat) }}">Xem</a><a href="{{ route('admin.suat.edit', $suat) }}">Sửa</a><form method="POST" action="{{ route('admin.suat.destroy', $suat) }}" onsubmit="return confirm('Xóa suất và các ghế theo suất chưa có vé?')">@csrf @method('DELETE')<button class="btn btn-small btn-outline">Xóa</button></form></div></td></tr>
@empty<tr><td colspan="7">Không có suất phù hợp.</td></tr>@endforelse
</tbody></table></div>
@include('partials.pagination', ['paginator' => $suats])
</section>
@endsection
