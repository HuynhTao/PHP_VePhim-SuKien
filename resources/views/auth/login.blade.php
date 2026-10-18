@extends('layouts.app')
@section('title', 'Đăng nhập')
@section('content')
<div class="auth-wrap"><section class="form-card">
    <span class="eyebrow">CHÀO MỪNG TRỞ LẠI</span><h1>Đăng nhập</h1><p class="muted">Một tài khoản cho mọi trải nghiệm.</p>
    <form method="POST" action="{{ route('login.store') }}">@csrf
        <label for="email">Email</label><input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required autofocus maxlength="255">
        <label for="password">Mật khẩu</label><input id="password" name="password" type="password" autocomplete="current-password" required>
        <label class="checkbox"><input name="remember" type="checkbox" value="1">Ghi nhớ đăng nhập</label>
        <button class="btn btn-block">Đăng nhập</button>
    </form>
    <p class="form-foot">Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a></p>
</section></div>
@endsection

