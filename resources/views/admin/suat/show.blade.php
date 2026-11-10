@extends('layouts.app')
@section('title', 'Chi tiết suất')
@section('content')
<section class="admin-page">
@include('admin.m3-nav')
<div class="section-heading"><div><span class="eyebrow">SUẤT #{{ $suat->id }}</span><h1>{{ $suat->noiDung->tieu_de }}</h1><p class="muted">{{ $suat->phongChieu->diaDiem->ten_dia_diem }} · {{ $suat->phongChieu->ten_phong }}</p></div><a class="btn btn-outline" href="{{ route('admin.suat.edit', $suat) }}">Sửa suất</a></div>
<div class="m3-details"><strong>{{ $suat->bat_dau_luc->format('d/m/Y H:i') }} → {{ $suat->ket_thuc_luc->format('d/m/Y H:i') }}</strong><span>Giờ Việt Nam</span><span class="m3-status">{{ ['mo_ban' => 'Mở bán', 'dong' => 'Đóng', 'huy' => 'Hủy'][$suat->trang_thai] }}</span></div>
@php($labels = ['trong' => 'Trống', 'dang_giu' => 'Đang giữ', 'da_ban' => 'Đã bán', 'tam_khoa' => 'Tạm khóa'])
<div class="m3-details">@foreach($labels as $key => $label)<span>{{ $label }}: <strong>{{ $suat->gheTheoSuats->where('trang_thai', $key)->count() }}</strong></span>@endforeach</div>
<div class="m3-panel"><div class="screen">MÀN HÌNH / SÂN KHẤU</div><div class="seat-scroll">
@foreach($suat->gheTheoSuats->sortBy(fn ($row) => $row->gheNgoi->hang_ghe . str_pad($row->gheNgoi->so_ghe, 3, '0', STR_PAD_LEFT))->groupBy('gheNgoi.hang_ghe') as $hang => $rows)
<div class="seat-row"><strong class="seat">{{ $hang }}</strong>@foreach($rows as $row)<span @class(['seat', 'vip' => $row->gheNgoi->loai_ghe === 'vip', $row->trang_thai]) title="{{ $labels[$row->trang_thai] }} · {{ number_format($row->gia_ve, 0, ',', '.') }}đ">{{ $row->gheNgoi->hang_ghe }}{{ $row->gheNgoi->so_ghe }}</span>@endforeach</div>
@endforeach
</div><p class="m3-note">Xanh: trống thường · Vàng: trống VIP · Xanh lam: đang giữ · Đỏ: đã bán · Xám: tạm khóa. Di chuột vào ghế để xem giá. Tải lại trang để cập nhật trạng thái.</p></div>
<details class="m3-panel"><summary><strong>Bảng giá và trạng thái từng ghế</strong></summary><div class="table-wrap"><table><thead><tr><th>Ghế</th><th>Loại</th><th>Giá vé</th><th>Trạng thái</th></tr></thead><tbody>@foreach($suat->gheTheoSuats as $row)<tr><td>{{ $row->gheNgoi->hang_ghe }}{{ $row->gheNgoi->so_ghe }}</td><td>{{ $row->gheNgoi->loai_ghe === 'vip' ? 'VIP' : 'Thường' }}</td><td>{{ number_format($row->gia_ve, 0, ',', '.') }}đ</td><td>{{ $labels[$row->trang_thai] }}</td></tr>@endforeach</tbody></table></div></details>
</section>
@endsection
