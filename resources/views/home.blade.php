@extends('layouts.app')
@section('title', 'Đặt vé Phim & Sự kiện trực tuyến — MoMo Cinema Style')
@section('content')
<section class="hero">
    <div class="hero-copy">
        <span class="eyebrow">MOMO CINEMA &amp; EVENTS · ĐẶT VÉ SIÊU TỐC</span>
        <h1>Mua vé xem phim &amp; sự kiện<br><em>chạm nhẹ, vào rạp ngay!</em></h1>
        <p>Trải nghiệm đặt vé trực tuyến hiện đại: chọn rạp gần bạn, xem sơ đồ ghế trực quan theo thời gian thực và thanh toán an toàn chỉ trong vài giây.</p>
        <div class="hero-cta">
            <a class="btn" href="#kham-pha">Đặt vé ngay <span aria-hidden="true">→</span></a>
            <a class="btn btn-outline hero-btn-alt" href="{{ route('datve.index') }}">Lịch chiếu theo rạp</a>
        </div>
        <div class="hero-stats">
            <div class="stat-pill"><strong>{{ $stats['dang_chieu'] ?? 24 }}</strong><span>Phim đang chiếu</span></div>
            <div class="stat-pill"><strong>{{ $stats['sap_chieu'] ?? 11 }}</strong><span>Phim sắp chiếu</span></div>
            <div class="stat-pill"><strong>{{ $stats['rap'] ?? 4 }}</strong><span>Cụm rạp IMAX/3D</span></div>
            <div class="stat-pill"><strong>{{ $stats['suat'] ?? 36 }}+</strong><span>Suất mở bán</span></div>
        </div>
    </div>
    <div class="hero-art" aria-hidden="true">
        <div class="orbit"></div>
        <div class="momo-glow"></div>
        <div class="ticket-art">
            <div class="ticket-head">
                <span>CINEVIET × MOMO</span>
                <em class="ticket-chip">VIP PASS</em>
            </div>
            <strong>CINEMA<br>&amp; LIVE.</strong>
            <div class="ticket-meta-row">
                <div><small>PHÒNG CHIẾU</small><b>IMAX LASER</b></div>
                <div><small>GHẾ</small><b>E08 · E09</b></div>
            </div>
            <div class="ticket-rule"></div>
            <small>QUÉT MÃ QR VÀO RẠP · KHÔNG CẦN IN VÉ</small>
            <div class="barcode"></div>
        </div>
        <span class="art-label">TRẢI NGHIỆM ĐIỆN ẢNH &amp; SỰ KIỆN</span>
    </div>
</section>

<section class="quick-steps" aria-label="Quy trình đặt vé 4 bước">
    <div class="step-card">
        <span class="step-num">01</span>
        <div>
            <strong>Chọn Phim / Sự kiện</strong>
            <p>Khám phá 36 bộ phim bom tấn &amp; sự kiện trên MoMo Cinema.</p>
        </div>
    </div>
    <div class="step-card">
        <span class="step-num">02</span>
        <div>
            <strong>Chọn Rạp &amp; Suất chiếu</strong>
            <p>Lọc nhanh khung giờ đẹp tại cụm rạp gần bạn nhất.</p>
        </div>
    </div>
    <div class="step-card">
        <span class="step-num">03</span>
        <div>
            <strong>Chọn Ghế Realtime</strong>
            <p>Sơ đồ ghế trực quan, giữ ghế tự động 10 phút chống trùng.</p>
        </div>
    </div>
    <div class="step-card">
        <span class="step-num">04</span>
        <div>
            <strong>Thanh toán MoMo / QR</strong>
            <p>Nhận mã vé điện tử tức thì, quản lý hoặc hủy vé linh hoạt.</p>
        </div>
    </div>
</section>

<section id="kham-pha" class="catalog">
    <div class="section-heading">
        <div>
            <span class="eyebrow">LỊCH CHIẾU &amp; SỰ KIỆN HOT</span>
            <h2>Phim &amp; sự kiện nổi bật</h2>
        </div>
        <div class="category-tabs" role="tablist" aria-label="Lọc nhanh loại nội dung">
            <a href="{{ route('home', array_filter(['q' => request('q')])) }}#kham-pha"
               @class(['tab-pill', 'active' => empty($muc) && !request('loai')])>
                Tất cả ({{ ($stats['phim'] ?? 0) + ($stats['su_kien'] ?? 0) }})
            </a>
            <a href="{{ route('home', array_filter(['muc' => 'dang_chieu', 'q' => request('q')])) }}#kham-pha"
               @class(['tab-pill', 'active' => ($muc ?? '') === 'dang_chieu'])>
                🎬 Phim đang chiếu ({{ $stats['dang_chieu'] ?? 0 }})
            </a>
            <a href="{{ route('home', array_filter(['muc' => 'sap_chieu', 'q' => request('q')])) }}#kham-pha"
               @class(['tab-pill', 'active' => ($muc ?? '') === 'sap_chieu'])>
                🍿 Phim sắp chiếu ({{ $stats['sap_chieu'] ?? 0 }})
            </a>
            <a href="{{ route('home', array_filter(['muc' => 'su_kien', 'q' => request('q')])) }}#kham-pha"
               @class(['tab-pill', 'active' => ($muc ?? '') === 'su_kien' || request('loai') === 'su_kien'])>
                🎵 Sự kiện &amp; Concert ({{ $stats['su_kien'] ?? 0 }})
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('home') }}" class="filter-bar">
        @if(!empty($muc))<input type="hidden" name="muc" value="{{ $muc }}">@endif
        <div class="quick-wrap">
            <label class="sr-only" for="q">Tìm theo tên</label>
            <input id="q" name="q" value="{{ request('q') }}" placeholder="🔎 Gõ tên phim, concert để tìm nhanh (AJAX)..." maxlength="100" autocomplete="off">
            <div id="quick-results" class="quick-results" hidden></div>
        </div>
        <label class="sr-only" for="loai">Loại nội dung</label>
        <select id="loai" name="loai">
            <option value="">Tất cả danh mục</option>
            <option value="phim" @selected(request('loai') === 'phim')>Phim điện ảnh</option>
            <option value="su_kien" @selected(request('loai') === 'su_kien')>Sự kiện &amp; Âm nhạc</option>
        </select>
        <button class="btn">Tìm kiếm</button>
        @if(request()->filled('q') || request()->filled('loai') || !empty($muc))
            <a class="btn btn-outline btn-small" href="{{ route('home') }}#kham-pha">Đặt lại</a>
        @else
            <span class="muted result-count">Hiển thị {{ $contents->count() }} / {{ $contents->total() }} nội dung</span>
        @endif
    </form>

    <div class="card-grid">
    @forelse($contents as $content)
        @php
            $laPhim = $content->loai_noi_dung === 'phim';
            $sapChieu = $laPhim && $content->phim && $content->phim->ngay_khoi_chieu?->isFuture();
            $age = $content->phim?->gioi_han_tuoi ?? $content->suKien?->gioi_han_tuoi ?? 'P';
            $posterUrl = $content->anhBiaUrl();
            $rating = number_format(8.6 + (($content->id * 3) % 13) / 10, 1);
        @endphp
        <article class="content-card">
            <a href="{{ route('noi-dung.show', $content) }}" class="poster-link" aria-label="Xem chi tiết {{ $content->tieu_de }}">
                <div class="poster {{ $laPhim ? 'poster-film' : 'poster-event' }} poster-variant-{{ $content->id % 6 }} {{ $posterUrl ? 'poster-img' : '' }}"
                     @if($posterUrl) style="background-image: linear-gradient(180deg, rgba(15,12,28,.15) 0%, rgba(15,12,28,.88) 92%), url('{{ e($posterUrl) }}'); background-size: cover; background-position: center;" @endif>
                    <div class="poster-top">
                        <div class="poster-badges">
                            <x-badge :age="$age" />
                            <span class="badge">{{ $laPhim ? 'ĐIỆN ẢNH' : 'SỰ KIỆN' }}</span>
                        </div>
                        <span class="rating-pill" title="Điểm đánh giá khán giả">★ {{ $rating }}</span>
                    </div>
                    <span class="poster-number">{{ str_pad((string) ($loop->iteration + ($contents->currentPage() - 1) * $contents->perPage()), 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="poster-bottom">
                        <span class="poster-caption">CINEVIET × MOMO SELECTION</span>
                        <h3>{{ $content->tieu_de }}</h3>
                    </div>
                </div>
            </a>
            <div class="card-body">
                <div class="metadata">
                    @if($laPhim && $content->phim)
                        <span>⏱ {{ $content->phim->thoi_luong_phut }} phút</span>
                        <span>·</span>
                        <span>{{ $content->phim->the_loai ?: $content->phim->ngon_ngu }}</span>
                        @if($sapChieu)
                            <span class="soon-tag">Khởi chiếu {{ $content->phim->ngay_khoi_chieu->format('d/m') }}</span>
                        @endif
                    @else
                        <span>🎵 {{ $content->suKien?->the_loai ?? 'Sự kiện' }}</span>
                        <span>·</span>
                        <span>🏢 {{ $content->suKien?->don_vi_to_chuc }}</span>
                    @endif
                </div>
                <p>{{ \Illuminate\Support\Str::limit($content->mo_ta, 120) }}</p>
                <div class="showtime-list">
                    <div class="showtime-label">
                        <span>Suất sắp chiếu ({{ $content->suats->count() }})</span>
                        <a href="{{ route('noi-dung.show', $content) }}">Chi tiết &amp; lịch chiếu →</a>
                    </div>
                    @forelse($content->suats->take(2) as $session)
                        <div class="showtime">
                            <div class="showtime-info">
                                <strong>{{ $session->bat_dau_luc->format('H:i') }} · {{ $session->bat_dau_luc->format('d/m') }}</strong>
                                <small>{{ $session->phongChieu->diaDiem->ten_dia_diem }} ({{ $session->phongChieu->ten_phong }})</small>
                            </div>
                            <a class="btn btn-small" href="{{ route('datve.chon-ghe', $session) }}">Mua vé</a>
                        </div>
                    @empty
                        <div class="showtime muted">{{ $sapChieu ? 'Phim sắp khởi chiếu.' : 'Lịch diễn đang được cập nhật.' }}</div>
                    @endforelse
                </div>
            </div>
        </article>
    @empty
        <div class="empty-state">
            <span class="eyebrow">KHÔNG TÌM THẤY KẾT QUẢ</span>
            <h3>Chưa tìm thấy nội dung phù hợp</h3>
            <p>Thử từ khóa khác hoặc xem tất cả phim và sự kiện đang mở bán.</p>
            <a href="{{ route('home') }}" class="btn btn-outline">Xem tất cả</a>
        </div>
    @endforelse
    </div>

    @include('partials.pagination', ['paginator' => $contents])
</section>

@if(!empty($raps) && $raps->count() > 0)
<section class="cinema-chain" aria-label="Hệ thống cụm rạp đối tác">
    <div class="section-heading">
        <div>
            <span class="eyebrow">ĐỐI TÁC RẠP &amp; SÂN KHẤU</span>
            <h2>Hệ thống cụm rạp nổi bật</h2>
        </div>
        <a class="btn btn-outline btn-small" href="{{ route('datve.index') }}">Chọn suất theo rạp →</a>
    </div>
    <div class="cinema-grid">
        @foreach($raps as $rap)
            <div class="cinema-card">
                <div class="cinema-icon">{{ mb_substr($rap->ten_dia_diem, 0, 1) }}</div>
                <div class="cinema-info">
                    <strong>{{ $rap->ten_dia_diem }}</strong>
                    <p>{{ $rap->dia_chi }}, {{ $rap->tinh_thanh }}</p>
                    <small>{{ $rap->phong_chieus_count }} phòng chiếu / sân khấu · {{ $rap->so_dien_thoai }}</small>
                </div>
                <a class="btn btn-small btn-outline" href="{{ route('datve.index', ['dia_diem_id' => $rap->id]) }}">Lịch chiếu</a>
            </div>
        @endforeach
    </div>
</section>
@endif

<script>
/* M2 - Tìm kiếm nhanh bằng AJAX (fetch + debounce + AbortController) */
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
            empty.textContent = 'Không tìm thấy kết quả phù hợp.';
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
            title.textContent = it.tieu_de;
            const type = document.createElement('small');
            type.textContent = it.loai;
            text.appendChild(title);
            text.appendChild(type);
            a.appendChild(thumb);
            a.appendChild(text);
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
        }, 250);
    });

    document.addEventListener('click', function (e) {
        if (e.target !== input && !box.contains(e.target)) hide();
    });
})();
</script>
@endsection
