@extends('layouts.app')
@section('content')
<section class="hero">
    <div class="hero-copy"><span class="eyebrow">MỖI TẤM VÉ, MỘT TRẢI NGHIỆM</span>
        <h1>Hẹn bạn ở<br><em>những khoảnh khắc hay.</em></h1>
        <p>Khám phá phim và sự kiện, tìm lịch phù hợp và lên kế hoạch cho buổi hẹn tiếp theo.</p>
        <a class="btn" href="#kham-pha">Khám phá ngay <span aria-hidden="true">↗</span></a>
    </div>
    <div class="hero-art" aria-hidden="true"><div class="orbit"></div><div class="ticket-art"><span>CINEVIET</span><strong>LET'S<br>GO OUT.</strong><div class="ticket-rule"></div><small>MOVIES & EVENTS</small><div class="barcode"></div></div><span class="art-label">MỘT BUỔI TỐI THẬT KHÁC.</span></div>
</section>
<section id="kham-pha" class="catalog">
    <div class="section-heading"><div><span class="eyebrow">LÊN LỊCH HẸN</span><h2>Phim & sự kiện nổi bật</h2></div><span class="muted">{{ $contents->total() }} nội dung</span></div>
    <form method="GET" action="{{ route('home') }}" class="filter-bar">
        <label class="sr-only" for="q">Tìm theo tên</label><input id="q" name="q" value="{{ request('q') }}" placeholder="Tìm phim, sự kiện..." maxlength="100">
        <label class="sr-only" for="loai">Loại nội dung</label><select id="loai" name="loai"><option value="">Tất cả nội dung</option value="phim" @selected(request('loai') === 'phim')>Phim</option><option value="su_kien" @selected(request('loai') === 'su_kien')>Sự kiện</option></select>
        <button class="btn">Tìm kiếm</button><a class="muted" href="{{ route('home') }}">Đặt lại</a>
    </form>
    <div class="card-grid">
    @forelse($contents as $content)
        <article class="content-card">
            <div class="poster {{ $content->loai_noi_dung === 'phim' ? 'poster-film' : 'poster-event' }}">
                <span class="badge">{{ $content->loai_noi_dung === 'phim' ? 'ĐIỆN ẢNH' : 'SỰ KIỆN' }}</span>
                <span class="poster-number">0{{ $loop->iteration }}</span>
                <h3>{{ $content->tieu_de }}</h3><span class="poster-caption">CINEVIET SELECTION</span>
            </div>
            <div class="card-body">
                <div class="metadata">@if($content->phim){{ $content->phim->thoi_luong_phut }} phút · {{ $content->phim->ngon_ngu }}@else{{ $content->suKien?->the_loai ?? 'Sự kiện' }} · {{ $content->suKien?->don_vi_to_chuc }}@endif</div>
                <p>{{ $content->mo_ta }}</p>
                @forelse($content->suats->take(3) as $session)
                    <div class="showtime"><strong>{{ $session->bat_dau_luc->format('d/m · H:i') }}</strong><span>{{ $session->phongChieu->ten_phong }}</span><small>{{ $session->phongChieu->diaDiem->ten_dia_diem }}</small></div>
                @empty
                    <div class="showtime muted">Lịch diễn đang được cập nhật.</div>
                @endforelse
            </div>
        </article>
    @empty
        <div class="empty-state"><h3>Chưa tìm thấy nội dung phù hợp</h3><p>Thử từ khóa khác hoặc xem tất cả phim và sự kiện.</p><a href="{{ route('home') }}" class="btn btn-outline">Xem tất cả</a></div>
    @endforelse
    </div>
    @include('partials.pagination', ['paginator' => $contents])
</section>
@endsection

