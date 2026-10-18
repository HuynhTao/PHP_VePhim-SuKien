@extends('layouts.app')
@section('title', 'Đăng ký')
@section('content')
<div class="auth-wrap"><section class="form-card">
    <span class="eyebrow">BẮT ĐẦU HÀNH TRÌNH</span><h1>Tạo tài khoản</h1><p class="muted">Sẵn sàng cho những trải nghiệm tiếp theo.</p>
    <form method="POST" action="{{ route('register.store') }}">@csrf
        <label for="ho_ten">Họ và tên</label><input id="ho_ten" name="ho_ten" autocomplete="name" value="{{ old('ho_ten') }}" required maxlength="100">
        <label for="email">Email</label><input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required maxlength="255">
        <label for="so_dien_thoai">Số điện thoại <small>(không bắt buộc)</small></label><input id="so_dien_thoai" name="so_dien_thoai" type="tel" autocomplete="tel" value="{{ old('so_dien_thoai') }}" maxlength="20">
        <label for="password">Mật khẩu</label><input id="password" name="password" type="password" autocomplete="new-password" required minlength="8" aria-describedby="password-help"><small id="password-help" class="muted">Tối thiểu 8 ký tự.</small>
        <label for="password_confirmation">Nhập lại mật khẩu</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="8">
        <button class="btn btn-block form-submit">Đăng ký</button>
    </form>
    <p class="form-foot">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></p>
</section></div>
@endsection

