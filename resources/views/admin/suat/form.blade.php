@extends('layouts.app')
@section('title', $suat->exists ? 'Sửa suất' : 'Thêm suất')
@section('content')
<section class="form-card account-card m3-form">
<a class="back-link" href="{{ route('admin.suat.index') }}">← Danh sách suất</a>
<h1>{{ $suat->exists ? 'Sửa suất' : 'Thêm suất chiếu / biểu diễn' }}</h1>
<p class="m3-note">Nhập giờ Việt Nam. Hai suất có thể tiếp giáp nhau; suất đóng vẫn chiếm lịch, suất hủy không chiếm khoảng giờ.</p>
@if($noidungs->isEmpty())<p>Chưa có nội dung công bố. Chạy dữ liệu mẫu hoặc nhờ M2 tạo nội dung.</p>@endif
<form method="POST" action="{{ $suat->exists ? route('admin.suat.update', $suat) : route('admin.suat.store') }}">@csrf @if($suat->exists) @method('PUT') @endif
<div class="m3-grid">
<div class="wide"><label for="noi_dung_id">Phim / sự kiện đã công bố</label><select id="noi_dung_id" name="noi_dung_id" required><option value="">Chọn nội dung</option>@foreach($noidungs as $item)<option value="{{ $item->id }}" @selected(old('noi_dung_id', $suat->noi_dung_id) == $item->id)>{{ $item->tieu_de }} — {{ $item->loai_noi_dung === 'phim' ? 'Phim · '.($item->phim?->thoi_luong_phut ?? '?').' phút' : 'Sự kiện' }}</option>@endforeach</select></div>
<div class="wide"><label for="phong_chieu_id">Rạp / phòng</label><select id="phong_chieu_id" name="phong_chieu_id" required><option value="">Chọn phòng</option>@foreach($phongs as $phong)<option value="{{ $phong->id }}" @selected(old('phong_chieu_id', $suat->phong_chieu_id) == $phong->id)>{{ $phong->diaDiem->ten_dia_diem }} — {{ $phong->ten_phong }} · {{ $phong->ghe_ngois_count }} ghế{{ $phong->trang_thai !== 'hoat_dong' || $phong->diaDiem->trang_thai !== 'hoat_dong' ? ' (ngừng hoạt động)' : '' }}</option>@endforeach</select></div>
<div><label for="bat_dau_luc">Bắt đầu</label><input id="bat_dau_luc" name="bat_dau_luc" type="datetime-local" value="{{ old('bat_dau_luc', $suat->bat_dau_luc?->format('Y-m-d\TH:i')) }}" required></div>
<div><label for="ket_thuc_luc">Kết thúc</label><input id="ket_thuc_luc" name="ket_thuc_luc" type="datetime-local" value="{{ old('ket_thuc_luc', $suat->ket_thuc_luc?->format('Y-m-d\TH:i')) }}" required></div>
<div><label for="gia_thuong">Giá vé thường (VNĐ)</label><input id="gia_thuong" name="gia_thuong" type="number" min="0" max="9999999999.99" step="0.01" value="{{ old('gia_thuong', $prices['thuong']) }}" required></div>
<div><label for="gia_vip">Giá vé VIP (VNĐ)</label><input id="gia_vip" name="gia_vip" type="number" min="0" max="9999999999.99" step="0.01" value="{{ old('gia_vip', $prices['vip']) }}" required></div>
<div><label for="trang_thai">Trạng thái</label><select id="trang_thai" name="trang_thai">@foreach(['mo_ban' => 'Mở bán', 'dong' => 'Đóng', 'huy' => 'Hủy'] as $key => $label)<option value="{{ $key }}" @selected(old('trang_thai', $suat->trang_thai) === $key)>{{ $label }}</option>@endforeach</select></div>
</div>
<p class="m3-note">Tạo suất sẽ tạo ghế theo suất từ các ghế đang sử dụng của phòng. Suất đã giữ/bán ghế hoặc phát hành vé sẽ bị chặn sửa/xóa để bảo toàn dữ liệu đặt vé.</p>
<div class="form-actions"><button class="btn">Lưu suất</button><a href="{{ route('admin.suat.index') }}">Hủy</a></div>
</form></section>
@endsection
