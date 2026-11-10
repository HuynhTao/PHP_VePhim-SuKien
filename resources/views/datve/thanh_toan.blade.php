@extends('layouts.app')
@section('title', 'Thanh toán')
@push('styles')<link rel="stylesheet" href="{{ asset('css/m4.css') }}">@endpush
@section('content')
<section class="dv-page">
<span class="eyebrow">ĐƠN {{ $don->ma_don_hang }}</span>
<h1>Xác nhận & thanh toán</h1>
<div class="dv-layout">
<div class="dv-panel">
<div class="dv-sumhead">@include('datve._poster', ['content' => $suat->noiDung, 'small' => true, 'class' => 'dv-poster-sm'])<h3 style="margin:0">{{ $suat->noiDung->tieu_de }}</h3></div>
<p class="muted">{{ $suat->phongChieu->diaDiem->ten_dia_diem }} · {{ $suat->phongChieu->ten_phong }} · {{ $suat->bat_dau_luc->format('H:i d/m/Y') }}</p>
<div class="table-wrap"><table><thead><tr><th>Ghế</th><th>Loại</th><th>Giá vé</th></tr></thead><tbody>
@foreach($don->gheDangGiu as $g)<tr><td>{{ $g->gheNgoi->hang_ghe }}{{ $g->gheNgoi->so_ghe }}</td><td>{{ $g->gheNgoi->loai_ghe === 'vip' ? 'VIP' : 'Thường' }}</td><td>{{ number_format($g->gia_ve, 0, ',', '.') }}đ</td></tr>@endforeach
</tbody></table></div>
<form method="POST" action="{{ route('datve.thanh-toan.xac-nhan', $don) }}" id="form-tt">@csrf
<h3 style="margin-top:24px">Phương thức thanh toán</h3>
<div class="dv-methods">
@foreach($phuongThuc as $key => $ten)
<label><span style="font-size:26px">{{ ['momo' => '💗', 'vnpay' => '📱', 'the_ngan_hang' => '💳'][$key] ?? '💰' }}</span><input type="radio" name="phuong_thuc" value="{{ $key }}" @checked(old('phuong_thuc') === $key) required><strong>{{ $ten }}</strong></label>
@endforeach
</div>
<p class="muted" style="font-size:12px">Đây là thanh toán mô phỏng phục vụ đồ án, không trừ tiền thật.</p>
<button class="btn btn-block">Xác nhận thanh toán {{ number_format($don->tong_tien, 0, ',', '.') }}đ</button>
</form>
</div>
<aside class="dv-panel">
<p class="muted" style="margin-bottom:4px">Thời gian giữ ghế còn lại</p>
<div
    class="dv-timer"
    id="dv-dem"
    data-het-han-luc="{{ $don->het_han_luc->toIso8601String() }}"
    data-url-chon-ghe="{{ route('datve.chon-ghe', $suat) }}"
>
    --:--
</div>
<hr>
<p class="muted" style="margin-bottom:4px">Tổng tiền</p>
<div class="dv-total">{{ number_format($don->tong_tien, 0, ',', '.') }}đ</div>
<form method="POST" action="{{ route('datve.huy', $don) }}" style="margin-top:16px" onsubmit="return confirm('Hủy đơn và nhả ghế?')">@csrf<button class="btn btn-outline btn-block">Hủy đơn</button></form>
</aside>
</div>
</section>
@endsection
@push('scripts')
<script>
(() => {
    const el = document.getElementById('dv-dem');

    const han = new Date(el.dataset.hetHanLuc).getTime();
    const chonGhe = el.dataset.urlChonGhe;

    const t = setInterval(() => {
        const con = Math.max(0, Math.floor((han - Date.now()) / 1000));

        const phut = String(Math.floor(con / 60)).padStart(2, '0');
        const giay = String(con % 60).padStart(2, '0');

        el.textContent = phut + ':' + giay;

        if (con === 0) {
            clearInterval(t);
            alert('Đã hết thời gian giữ ghế.');
            window.location = chonGhe;
        }
    }, 1000);
})();
</script>
@endpush