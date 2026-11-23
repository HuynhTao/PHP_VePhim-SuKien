@extends('layouts.app')
@section('title', 'Hồ sơ cá nhân — Tài khoản của tôi')
@section('content')
@php
    $user = $user ?? auth()->user();
@endphp
<section class="profile-page">
    <div class="profile-banner">
        <div class="profile-avatar">{{ mb_strtoupper(mb_substr($user->ho_ten, 0, 1)) }}</div>
        <div class="profile-summary">
            <span class="eyebrow">TÀI KHOẢN CỦA TÔI</span>
            <h1>Chào {{ $user->ho_ten }}!</h1>
            <div class="profile-meta">
                <span class="role-badge {{ $user->isAdmin() ? 'role-admin' : '' }}">
                    {{ $user->isAdmin() ? 'Quản trị viên (Admin)' : 'Thành viên (User)' }}
                </span>
                <span>📧 {{ $user->email }}</span>
                <span>📱 {{ $user->so_dien_thoai ?? 'Chưa cung cấp' }}</span>
                @if(isset($user->don_hangs_count))
                    <span>🎟️ {{ $user->don_hangs_count }} đơn đặt vé</span>
                @endif
            </div>
        </div>
        <div class="profile-quick-actions">
            <a class="btn btn-small" href="{{ route('datve.index') }}">Đặt vé mới</a>
            <a class="btn btn-small btn-outline" href="{{ route('orders.index') }}">Lịch sử vé</a>
        </div>
    </div>

    <div class="profile-grid">
        <section class="form-card profile-card">
            <span class="eyebrow">THÔNG TIN CÁ NHÂN</span>
            <h2>Cập nhật hồ sơ</h2>
            <p class="muted">Thông tin này dùng để hiển thị trên vé điện tử và liên hệ khi cần.</p>

            <form method="POST" action="{{ route('account.update') }}">
                @csrf
                @method('PUT')

                <x-input
                    name="ho_ten"
                    label="Họ và tên"
                    :value="$user->ho_ten"
                    required
                    maxlength="100"
                    autocomplete="name"
                />

                <x-input
                    name="email"
                    type="email"
                    label="Địa chỉ Email"
                    :value="$user->email"
                    required
                    maxlength="255"
                    autocomplete="email"
                />

                <x-input
                    name="so_dien_thoai"
                    type="tel"
                    label="Số điện thoại"
                    :value="$user->so_dien_thoai ?? ''"
                    maxlength="20"
                    autocomplete="tel"
                    hint="Định dạng 8–20 ký tự số, dùng để tra cứu vé nhanh."
                />

                <dl class="profile-readonly">
                    <dt>Vai trò hệ thống</dt>
                    <dd>{{ $user->isAdmin() ? 'Quản trị viên' : 'Người dùng' }}</dd>
                    <dt>Ngày tham gia</dt>
                    <dd>{{ $user->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </dl>

                <div class="form-actions">
                    <button type="submit" class="btn">Lưu thay đổi</button>
                </div>
            </form>
        </section>

        <section class="form-card profile-card">
            <span class="eyebrow">BẢO MẬT TÀI KHOẢN</span>
            <h2>Đổi mật khẩu</h2>
            <p class="muted">Sử dụng mật khẩu tối thiểu 8 ký tự để bảo vệ tài khoản của bạn.</p>

            <form method="POST" action="{{ route('account.password') }}">
                @csrf
                @method('PUT')

                <x-input
                    name="current_password"
                    type="password"
                    label="Mật khẩu hiện tại"
                    required
                    autocomplete="current-password"
                />

                <x-input
                    name="password"
                    type="password"
                    label="Mật khẩu mới"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    hint="Tối thiểu 8 ký tự."
                />

                <x-input
                    name="password_confirmation"
                    type="password"
                    label="Nhập lại mật khẩu mới"
                    required
                    minlength="8"
                    autocomplete="new-password"
                />

                <div class="form-actions">
                    <button type="submit" class="btn btn-outline">Cập nhật mật khẩu</button>
                </div>
            </form>
        </section>
    </div>
</section>
@endsection
