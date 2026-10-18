@extends('layouts.app')
@section('title', $user->exists ? 'Sửa tài khoản' : 'Thêm tài khoản')
@section('content')
<section class="form-card account-card">
<a class="back-link" href="{{ route('admin.users.index') }}">← Danh sách tài khoản</a><h1>{{ $user->exists ? 'Sửa tài khoản' : 'Thêm tài khoản' }}</h1>
<form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">@csrf @if($user->exists) @method('PUT') @endif
<label for="ho_ten">Họ và tên</label><input id="ho_ten" name="ho_ten" value="{{ old('ho_ten', $user->ho_ten) }}" required maxlength="100">
<label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="255">
<label for="so_dien_thoai">Số điện thoại</label><input id="so_dien_thoai" name="so_dien_thoai" type="tel" value="{{ old('so_dien_thoai', $user->so_dien_thoai) }}" maxlength="20">
<label for="vai_tro">Vai trò</label><select id="vai_tro" name="vai_tro"><option value="user" @selected(old('vai_tro', $user->vai_tro) === 'user')>User — Người dùng</option><option value="admin" @selected(old('vai_tro', $user->vai_tro) === 'admin')>Admin — Quản trị viên</option></select>
<label for="password">Mật khẩu {{ $user->exists ? '(để trống nếu không đổi)' : '' }}</label><input id="password" name="password" type="password" autocomplete="new-password" minlength="8" @required(!$user->exists)>
<label for="password_confirmation">Nhập lại mật khẩu</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" @required(!$user->exists)>
<div class="form-actions"><button class="btn">Lưu tài khoản</button><a href="{{ route('admin.users.index') }}" class="btn btn-outline">Hủy</a></div>
</form></section>
@endsection

