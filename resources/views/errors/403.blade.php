@extends('layouts.app')
@section('title', 'Không có quyền truy cập')
@section('content')
<section class="empty-state"><span class="eyebrow">403</span><h1>Bạn không có quyền truy cập</h1><p>Trang này chỉ dành cho tài khoản được cấp quyền phù hợp.</p><a href="{{ route('home') }}" class="btn">Về trang chủ</a></section>
@endsection

