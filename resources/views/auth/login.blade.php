@extends('layouts.app')
@section('title', 'Đăng nhập — VePhimSuKien')
@section('content')
<div class="auth-wrap">
    <section class="form-card auth-card">
        <div class="auth-brand-badge">MOMO CINEMA &amp; EVENTS</div>
        <span class="eyebrow">CHÀO MỪNG TRỞ LẠI</span>
        <h1>Đăng nhập</h1>
        <p class="muted">Một tài khoản cho mọi trải nghiệm đặt vé phim và sự kiện.</p>

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <x-input
                name="email"
                type="email"
                label="Email"
                required
                autofocus
                maxlength="255"
                autocomplete="email"
                placeholder="nhap@email.com"
            />

            <x-input
                name="password"
                type="password"
                label="Mật khẩu"
                required
                autocomplete="current-password"
                placeholder="••••••••"
            />

            <label class="checkbox">
                <input name="remember" type="checkbox" value="1">
                <span>Ghi nhớ đăng nhập trên thiết bị này</span>
            </label>

            <button class="btn btn-block">Đăng nhập ngay</button>
        </form>

        <div class="demo-accounts-box">
            <small><strong>Tài khoản demo nhanh:</strong></small>
            <div class="demo-chips">
                <span>Admin: <code>admin@example.com</code> / <code>Admin@123456</code></span>
                <span>User: <code>user@example.com</code> / <code>User@123456</code></span>
            </div>
        </div>

        <p class="form-foot">Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a></p>
    </section>
</div>
@endsection
