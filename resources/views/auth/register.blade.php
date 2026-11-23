@extends('layouts.app')
@section('title', 'Đăng ký tài khoản — VePhimSuKien')
@section('content')
<div class="auth-wrap">
    <section class="form-card auth-card">
        <div class="auth-brand-badge">MOMO CINEMA &amp; EVENTS</div>
        <span class="eyebrow">BẮT ĐẦU HÀNH TRÌNH</span>
        <h1>Tạo tài khoản</h1>
        <p class="muted">Sẵn sàng đặt vé xem phim và sự kiện chỉ trong vài giây.</p>

        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            <x-input
                name="ho_ten"
                label="Họ và tên"
                required
                maxlength="100"
                autocomplete="name"
                placeholder="Nguyễn Văn A"
            />

            <x-input
                name="email"
                type="email"
                label="Email"
                required
                maxlength="255"
                autocomplete="email"
                placeholder="nhap@email.com"
            />

            <x-input
                name="so_dien_thoai"
                type="tel"
                label="Số điện thoại (không bắt buộc)"
                maxlength="20"
                autocomplete="tel"
                placeholder="0901234567"
            />

            <x-input
                name="password"
                type="password"
                label="Mật khẩu"
                required
                minlength="8"
                autocomplete="new-password"
                hint="Tối thiểu 8 ký tự."
            />

            <x-input
                name="password_confirmation"
                type="password"
                label="Nhập lại mật khẩu"
                required
                minlength="8"
                autocomplete="new-password"
            />

            <button class="btn btn-block form-submit">Đăng ký tài khoản</button>
        </form>

        <p class="form-foot">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></p>
    </section>
</div>
@endsection
