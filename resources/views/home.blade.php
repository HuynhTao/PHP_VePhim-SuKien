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

    @php
        $tabs = ['' => 'Tất cả', 'dang_chieu' => 'Phim đang chiếu', 'sap_chieu' => 'Phim sắp chiếu', 'su_kien' => 'Sự kiện'];
    @endphp
    <nav class="m2-tabs" aria-label="Nhóm nội dung">
        @foreach($tabs as $key => $label)
            <a href="{{ route('home', array_filter(['q' => request('q'), 'muc' => $key])) }}#kham-pha" @class(['active' => (string) ($muc ?? '') === (string) $key])>{{ $label }}</a>
        @endforeach
    </nav>

    <form method="GET" action="{{ route('home') }}" class="filter-bar">
        @if(!empty($muc))<input type="hidden" name="muc" value="{{ $muc }}">@endif
        @if(request('loai'))<input type="hidden" name="loai" value="{{ request('loai') }}">@endif
        <div class="quick-wrap">
            <label class="sr-only" for="q">Tìm theo tên</label>
            <input id="q" name="q" value="{{ request('q') }}" placeholder="Tìm phim, sự kiện..." maxlength="100" autocomplete="off">
            <div id="quick-results" class="quick-results" hidden></div>
        </div>
        <button class="btn">Tìm kiếm</button><a class="muted" href="{{ route('home') }}#kham-pha">Đặt lại</a>
    </form>

    <div class="card-grid">
    @forelse($contents as $content)
        @php
            $laPhim = $content->loai_noi_dung === 'phim';
            $sapChieu = $laPhim && $content->phim && $content->phim->ngay_khoi_chieu->isFuture();
            $anh = $content->anh_bia_url;
        @endphp
        <article class="content-card">
            <a href="{{ route('noi-dung.show', $content) }}" aria-label="Xem {{ $content->tieu_de }}">
                <div class="poster {{ $laPhim ? 'poster-film' : 'poster-event' }} {{ $anh ? 'poster-img' : '' }}"
                     @if($anh) style="background-image:linear-gradient(180deg,rgba(0,0,0,.05),rgba(0,0,0,.75)),url('{{ $anh }}')" @endif>
                    <span class="badge">{{ $laPhim ? 'ĐIỆN ẢNH' : 'SỰ KIỆN' }}</span>
                    <span class="poster-number">0{{ $loop->iteration }}</span>
                    <h3 style="color:#fff">{{ $content->tieu_de }}</h3><span class="poster-caption" style="color:#fff">CINEVIET SELECTION</span>
                </div>
            </a>
            <div class="card-body">
                <div class="metadata">
                    @if($laPhim && $content->phim){{ $content->phim->thoi_luong_phut }} phút · {{ $content->phim->ngon_ngu }}@if($content->phim->the_loai) · {{ $content->phim->the_loai }}@endif @if($sapChieu)<span class="soon-tag">Khởi chiếu {{ $content->phim->ngay_khoi_chieu->format('d/m') }}</span>@endif
                    @else{{ $content->suKien?->the_loai ?? 'Sự kiện' }} · {{ $content->suKien?->don_vi_to_chuc }}@endif
                </div>
                <p>{{ \Illuminate\Support\Str::limit($content->mo_ta, 110) }}</p>
                @forelse($content->suats->take(3) as $session)
                    <div class="showtime"><strong>{{ $session->bat_dau_luc->format('d/m · H:i') }}</strong><span>{{ $session->phongChieu->ten_phong }}</span><small>{{ $session->phongChieu->diaDiem->ten_dia_diem }}</small></div>
                @empty
                    <div class="showtime muted">{{ $sapChieu ? 'Sắp khởi chiếu.' : 'Lịch diễn đang được cập nhật.' }}</div>
                @endforelse
                <a class="card-more" href="{{ route('noi-dung.show', $content) }}">Xem chi tiết & suất chiếu →</a>
            </div>
        </article>
    @empty
        <div class="empty-state"><h3>Chưa tìm thấy nội dung phù hợp</h3><p>Thử từ khóa khác hoặc xem tất cả phim và sự kiện.</p><a href="{{ route('home') }}" class="btn btn-outline">Xem tất cả</a></div>
    @endforelse
    </div>
    @include('partials.pagination', ['paginator' => $contents])
</section>

<script>
/* M2 - Tìm kiếm nhanh bằng AJAX (fetch) */
(function () {
    const input = document.getElementById('q');
    const box = document.getElementById('quick-results');
    if (!input || !box) return;
    const url = @json(route('noi-dung.search'));
    let timer = null, controller = null;

    function hide() { box.hidden = true; box.innerHTML = ''; }

    function render(items) {
        box.innerHTML = '';
        if (!items.length) {
            const empty = document.createElement('div');
            empty.className = 'quick-empty';
            empty.textContent = 'Không tìm thấy kết quả.';
            box.appendChild(empty);
        }
        items.forEach(function (it) {
            const a = document.createElement('a');
            a.className = 'quick-item';
            a.href = it.url;
            const thumb = document.createElement(it.anh ? 'img' : 'div');
            thumb.className = 'quick-thumb';
            if (it.anh) { thumb.src = it.anh; thumb.alt = ''; }
            const text = document.createElement('div');
            const title = document.createElement('strong');
            title.textContent = it.tieu_de;           // textContent: tránh XSS
            const type = document.createElement('small');
            type.textContent = it.loai;
            text.appendChild(title); text.appendChild(type);
            a.appendChild(thumb); a.appendChild(text);
            box.appendChild(a);
        });
        box.hidden = false;
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        const q = input.value.trim();
        if (q.length < 2) { hide(); return; }
        timer = setTimeout(async function () {
            if (controller) controller.abort();
            controller = new AbortController();
            try {
                const res = await fetch(url + '?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json' },
                    signal: controller.signal
                });
                if (!res.ok) { hide(); return; }
                render(await res.json());
            } catch (e) {
                if (e.name !== 'AbortError') hide();
            }
        }, 250);                                      // debounce 250ms
    });

    document.addEventListener('click', function (e) {
        if (e.target !== input && !box.contains(e.target)) hide();
    });
})();
</script>
@endsection
