<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Khám phá phim & sự kiện') ·VePhim/SuKien</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
@stack('styles')
</head>
<body>
<a class="skip-link" href="#main">Đến nội dung chính</a>
@include('partials.header')
<main id="main" class="container">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="status">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>Vui lòng kiểm tra lại:</strong>
        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
@yield('content')
</main>
@include('partials.footer')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>

