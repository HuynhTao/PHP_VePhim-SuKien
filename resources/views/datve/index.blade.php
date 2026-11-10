@extends('layouts.app')
@section('title', 'Chọn suất chiếu')
@push('styles')<link rel="stylesheet" href="{{ asset('css/m4.css') }}">@endpush
@section('content')
<section class="dv-page">
<div class="dv-hero"><span class="eyebrow">ĐẶT VÉ TRỰC TUYẾN</span><h1>Chọn phim, chọn ghế,<br>ra rạp thôi!</h1><p>{{ $contents->total() }} phim / sự kiện có suất chiếu ngày {{ $ngay->format('d/m/Y') }}. Chọn giờ phù hợp và giữ ghế trong vài giây.</p></div>
<div class="dv-dates">
@foreach($ngays as $d)
<a href="{{ route('datve.index', array_merge(request()->except('page'), ['ngay' => $d->format('Y-m-d')])) }}" @class(['active' => $d->isSameDay($ngay)])><small>{{ ['CN','T2','T3','T4','T5','T6','T7'][$d->dayOfWeek] }}</small><strong>{{ $d->format('d/m') }}</strong></a>
@endforeach
</div>
<form class="filter-bar" method="GET" action="{{ route('datve.index') }}">
<input type="hidden" name="ngay" value="{{ $ngay->format('Y-m-d') }}">
<label class="sr-only" for="q">Tên phim / sự kiện</label><input id="q" name="q" value="{{ request('q') }}" maxlength="100" placeholder="Tìm phim, sự kiện...">
<label class="sr-only" for="dia_diem_id">Rạp</label><select id="dia_diem_id" name="dia_diem_id"><option value="">Tất cả rạp</option>@foreach($raps as $rap)<option value="{{ $rap->id }}" @selected(request('dia_diem_id') == $rap->id)>{{ $rap->ten_dia_diem }}</option>@endforeach</select>
<button class="btn">Tìm suất</button><a class="muted" href="{{ route('datve.index') }}">Đặt lại</a>
</form>
@forelse($contents as $content)
<article class="dv-film">
@include('datve._poster', ['content' => $content])
<div class="dv-film-body">
<span class="eyebrow">{{ $content->loai_noi_dung === 'phim' ? 'PHIM' : 'SỰ KIỆN' }}</span>
<h3>{{ $content->tieu_de }}</h3>
<div class="dv-meta">
@if($content->phim)<span>🕒 {{ $content->phim->thoi_luong_phut }} phút</span><span>🌐 {{ $content->phim->ngon_ngu }}</span><span>🔞 {{ $content->phim->gioi_han_tuoi }}</span>
@else<span>🎵 {{ $content->suKien?->the_loai }}</span><span>🏢 {{ $content->suKien?->don_vi_to_chuc }}</span>@endif
</div>
<p class="dv-desc">{{ \Illuminate\Support\Str::limit($content->mo_ta, 150) }}</p>
@foreach($content->suats->groupBy(fn ($s) => $s->phongChieu->diaDiem->ten_dia_diem) as $tenRap => $suats)
<div class="dv-rap"><h4>📍 {{ $tenRap }}</h4><div class="dv-times">
@foreach($suats as $s)
@php($tyLe = $s->ghe_trong_count / max(1, $s->ghe_theo_suats_count))
<a class="dv-time {{ $s->ghe_trong_count === 0 ? 'full' : ($tyLe < 0.2 ? 'low' : '') }}" href="{{ route('datve.chon-ghe', $s) }}">{{ $s->bat_dau_luc->format('H:i') }}<small>{{ $s->phongChieu->ten_phong }}</small><em>{{ $s->ghe_trong_count === 0 ? 'Hết ghế' : 'Còn '.$s->ghe_trong_count.'/'.$s->ghe_theo_suats_count }}</em></a>
@endforeach
</div></div>
@endforeach
</div>
</article>
@empty
<div class="empty-state"><h3>Không có suất phù hợp</h3><p>Thử chọn ngày hoặc rạp khác.</p></div>
@endforelse
@include('partials.pagination', ['paginator' => $contents])
</section>
@endsection