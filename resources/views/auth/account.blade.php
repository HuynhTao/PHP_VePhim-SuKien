@extends('layouts.app')
@section('title', 'Tài khoản của tôi')
@section('content')
<section class="form-card account-card"><span class="eyebrow">TÀI KHOẢN</span><h1>Chào {{ auth()->user()->ho_ten }}!</h1>
<dl><dt>Email</dt><dd>{{ auth()->user()->email }}</dd><dt>Số điện thoại</dt><dd>{{ auth()->user()->so_dien_thoai ?? 'Chưa cung cấp' }}</dd><dt>Vai trò</dt><dd>{{ auth()->user()->isAdmin() ? 'Quản trị viên' : 'Người dùng' }}</dd></dl>
<a class="btn" href="{{ route('home') }}">Khám phá phim & sự kiện</a></section>
@endsection

