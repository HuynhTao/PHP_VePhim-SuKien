@extends('layouts.app')

@section('title', $noiDung->tieu_de)

@section('content')
@php
    $laPhim = $noiDung->loai_noi_dung === 'phim';
    $chiTiet = $laPhim ? $noiDung->phim : $noiDung->suKien;
    $anh = $noiDung->anhBiaUrl();
@endphp
<section class="detail">
    <div class="detail-poster {{ $laPhim ? 'poster-film' : 'poster-event' }}"
         @if($anh) style="background-image:url('{{ e($anh) }}')" @endif
         role="img" aria-label="Poster {{ $noiDung->tieu_de }}"></div>

    <div>
        <a class="back-link" href="{{ route('home') }}">← Về trang chủ</a>
        <span class="eyebrow">{{ $laPhim ? 'PHIM ĐIỆN ẢNH' : 'SỰ KIỆN & CONCERT' }}</span>
        <h1>{{ $noiDung->tieu_de }}</h1>

        @if($noiDung->trang_thai !== 'cong_bo')
            <div class="alert error">Nội dung này chưa công bố, chỉ quản trị viên xem được.</div>
        @endif

        <dl>
            @if($laPhim)
                <dt>Thể loại</dt><dd>{{ $chiTiet?->the_loai ?: '—' }}</dd>
                <dt>Thời lượng</dt><dd>{{ $chiTiet?->thoi_luong_phut }} phút</dd>
                <dt>Ngôn ngữ</dt><dd>{{ $chiTiet?->ngon_ngu }}</dd>
                <dt>Khởi chiếu</dt><dd>{{ $chiTiet?->ngay_khoi_chieu?->format('d/m/Y') }}</dd>
            @else
                <dt>Thể loại</dt><dd>{{ $chiTiet?->the_loai }}</dd>
                <dt>Đơn vị tổ chức</dt><dd>{{ $chiTiet?->don_vi_to_chuc }}</dd>
            @endif
            <dt>Giới hạn tuổi</dt><dd><x-badge :age="$chiTiet?->gioi_han_tuoi ?? 'P'" /></dd>
        </dl>

        <p>{!! nl2br(e($noiDung->mo_ta)) !!}</p>

        <h2 style="margin-top:34px">Lịch suất chiếu đang mở bán</h2>
        @forelse($suats as $ngay => $danhSach)
            <div class="day-group">
                <h3>📅 {{ \Carbon\Carbon::parse($ngay)->format('d/m/Y') }}</h3>
                <div class="slot-list">
                    @foreach($danhSach as $suat)
                        <a class="slot" href="{{ route('datve.chon-ghe', $suat) }}">
                            <strong>{{ $suat->bat_dau_luc->format('H:i') }}</strong>
                            <span>{{ $suat->phongChieu->ten_phong }} · {{ $suat->phongChieu->diaDiem->ten_dia_diem }}</span>
                            <small>Còn {{ $suat->ghe_trong_count }} ghế trống</small>
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="empty-state">
                <p>Chưa có suất chiếu nào đang mở bán cho nội dung này.</p>
                <a class="btn btn-outline btn-small" href="{{ route('datve.index') }}">Xem lịch chiếu tất cả phim</a>
            </div>
        @endforelse
    </div>
</section>
@endsection
