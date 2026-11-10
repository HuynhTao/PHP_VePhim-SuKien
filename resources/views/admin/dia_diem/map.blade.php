@extends('layouts.app')
@section('title', 'Bản đồ rạp')
@section('content')
<section class="admin-page">
@include('admin.m3-nav')
<h1>{{ $rap->ten_dia_diem }}</h1>
<p>{{ $rap->dia_chi }}, {{ $rap->tinh_thanh }}</p>
@php($address = $rap->ten_dia_diem . ', ' . $rap->dia_chi . ', ' . $rap->tinh_thanh)
<iframe class="m3-map" title="Vị trí {{ $rap->ten_dia_diem }} trên Google Maps" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://maps.google.com/maps?q={{ rawurlencode($address) }}&output=embed"></iframe>
<p class="m3-note">Bản đồ cần Internet. Địa chỉ mẫu có thể không trùng vị trí rạp thực tế.</p>
<div class="m3-actions"><a class="btn" href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode($address) }}" target="_blank" rel="noopener noreferrer">Mở Google Maps</a><a href="{{ route('admin.dia-diem.index') }}">Về danh sách</a><a href="{{ route('admin.phong-chieu.index', ['dia_diem_id' => $rap->id]) }}">Xem phòng tại rạp</a></div>
</section>
@endsection
