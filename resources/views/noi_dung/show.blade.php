@extends('layouts.app')

@section('title', $noiDung->tieu_de)

@section('content')
@php
    $laPhim = $noiDung->loai_noi_dung === 'phim';
    $chiTiet = $laPhim ? $noiDung->phim : $noiDung->suKien;
    $anh = $noiDung->anh_bia_url;
    // TODO: hỏi M4 tên route trang chọn ghế rồi đổi 'dat-ve.chon-ghe' cho đúng (chỉ sửa 1 chỗ này)
    $linkDatVe = fn ($suat) => \Illuminate\Support\Facades\Route::has('dat-ve.chon-ghe')
        ? route('dat-ve.chon-ghe', $suat) : '#';
@endphp
<section class="detail">
    <div class="detail-poster {{ $laPhim ? 'poster-film' : 'poster-event' }}"
         @if($anh) style="background-image:url('{{ $anh }}')" @endif
         role="img" aria-label="Poster {{ $noiDung->tieu_de }}"></div>

    <div>
        <a class="back-link" href="{{ route('home') }}">← Về trang chủ</a>
        <span class="eyebrow">{{ $laPhim ? 'PHIM' : 'SỰ KIỆN' }}</span>
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
            <dt>Giới hạn tuổi</dt><dd>{{ $chiTiet?->gioi_han_tuoi }}</dd>
        </dl>

        <p>{!! nl2br(e($noiDung->mo_ta)) !!}</p>

        <h2 style="margin-top:34px">Suất chiếu</h2>
        @forelse($suats as $ngay => $danhSach)
            <div class="day-group">
                <h3>{{ \Carbon\Carbon::parse($ngay)->translatedFormat('l, d/m/Y') }}</h3>
                <div class="slot-list">
                    @foreach($danhSach as $suat)
                        <a class="slot" href="{{ $linkDatVe($suat) }}">
                            <strong>{{ $suat->bat_dau_luc->format('H:i') }}</strong>
                            <span>{{ $suat->phongChieu->ten_phong }} · {{ $suat->phongChieu->diaDiem->ten_dia_diem }}</span>
                            <small>{{ $suat->ghe_trong_count }} ghế trống</small>
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="empty-state"><p>Chưa có suất chiếu nào đang mở bán.</p></div>
        @endforelse
    </div>
</section>
@endsection
