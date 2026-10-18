@extends('layouts.app')
@section('title', 'Không tìm thấy trang')
@section('content')
<section class="empty-state"><span class="eyebrow">404</span><h1>Không tìm thấy trang</h1><p>Đường dẫn không tồn tại hoặc nội dung đã được xóa.</p><a href="{{ route('home') }}" class="btn">Về trang chủ</a></section>
@endsection

