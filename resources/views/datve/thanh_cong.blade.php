@extends('layouts.app')
@section('title', 'Đặt vé thành công')
@push('styles')<link rel="stylesheet" href="{{ asset('css/m4.css') }}">@endpush
@section('content')
<section class="dv-page" style="max-width:760px;margin:auto">
<div style="text-align:center"><div class="dv-ok">✓</div><h1>Đặt vé thành công!</h1>
<p class="muted">Mã đơn <strong>{{ $don->ma_don_hang }}</strong> · {{ $phuongThuc[$don->thanhToans->first()?->nha_cung_cap] ?? 'Đã thanh toán' }} · Tổng {{ number_format($don->tong_tien, 0, ',', '.') }}đ</p></div>
@foreach($don->ves as $ve)
@php($g = $ve->gheTheoSuat)
<div class="dv-ticket2">
@include('datve._poster', ['content' => $g->suat->noiDung, 'small' => true])
<div class="dv-ticket2-body">
<span class="eyebrow">VÉ ĐIỆN TỬ</span>
<h3>{{ $g->suat->noiDung->tieu_de }}</h3>
<p style="margin:0 0 4px">📍 {{ $g->suat->phongChieu->diaDiem->ten_dia_diem }} · {{ $g->suat->phongChieu->ten_phong }}</p>
<p style="margin:0 0 12px">🕒 {{ $g->suat->bat_dau_luc->format('H:i d/m/Y') }}</p>
<div class="dv-barcode"></div><small class="muted">{{ $ve->ma_ve }}</small>
</div>
<div class="dv-ticket2-stub"><small>GHẾ</small><strong class="dv-seatbig">{{ $g->gheNgoi->hang_ghe }}{{ $g->gheNgoi->so_ghe }}</strong><small>{{ number_format($ve->gia_ban, 0, ',', '.') }}đ</small></div>
</div>
@endforeach
<div class="form-actions" style="justify-content:center"><a class="btn" href="{{ route('datve.index') }}">Đặt thêm vé</a><a class="btn btn-outline" href="{{ route('home') }}">Về trang chủ</a></div>
</section>
@endsection