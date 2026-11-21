@extends('layouts.app')
@section('title', 'Chọn ghế')
@push('styles')<link rel="stylesheet" href="{{ asset('css/m4.css') }}">@endpush
@section('content')
<section class="dv-page">
<a class="back-link" href="{{ route('datve.index', ['ngay' => $suat->bat_dau_luc->format('Y-m-d')]) }}">← Chọn suất khác</a>
<div class="dv-movie">
@include('datve._poster', ['content' => $suat->noiDung, 'small' => true, 'class' => 'dv-poster-md'])
<div><span class="eyebrow">{{ $suat->noiDung->loai_noi_dung === 'phim' ? 'PHIM' : 'SỰ KIỆN' }}</span><h1>{{ $suat->noiDung->tieu_de }}</h1>
<div class="dv-meta"><span>🕒 {{ $suat->bat_dau_luc->format('H:i d/m/Y') }} → {{ $suat->ket_thuc_luc->format('H:i') }}</span><span>📍 {{ $suat->phongChieu->diaDiem->ten_dia_diem }}</span><span>🎬 {{ $suat->phongChieu->ten_phong }}</span></div></div>
</div>
@if($donCho)
<div class="alert success">Bạn đang giữ ghế cho đơn <strong>{{ $donCho->ma_don_hang }}</strong>. <a href="{{ route('datve.thanh-toan', $donCho) }}">Tiếp tục thanh toán →</a></div>
@endif
<form method="POST" action="{{ route('datve.giu-ghe', $suat) }}" class="dv-layout">@csrf
<div class="dv-panel">
<div class="dv-screen">MÀN HÌNH / SÂN KHẤU</div>
<div
    class="dv-scroll"
    id="dv-map"
    data-url-trang-thai="{{ route('datve.trang-thai', $suat) }}"
    data-max="{{ \App\Services\DatVeService::TOI_DA_GHE }}"
>
@foreach($ghes as $hang => $rows)
<div class="dv-row" style="--n: {{ $rows->count() }}"><span class="dv-rowname">{{ $hang }}</span>
@foreach($rows as $g)
@php($ten = $g->gheNgoi->hang_ghe.$g->gheNgoi->so_ghe)
@php($cuaToi = $g->trang_thai === 'dang_giu' && $g->donHangGiu?->nguoi_dung_id === auth()->id())
<label class="dv-seat {{ $g->trang_thai }} {{ $g->gheNgoi->loai_ghe === 'vip' ? 'vip' : '' }} {{ $cuaToi ? 'cua-toi' : '' }}" title="{{ $ten }} · {{ $g->gheNgoi->loai_ghe === 'vip' ? 'VIP' : 'Thường' }} · {{ number_format($g->gia_ve, 0, ',', '.') }}đ">
<input type="checkbox" name="ghe_ids[]" value="{{ $g->id }}" data-gia="{{ (float) $g->gia_ve }}" data-ten="{{ $ten }}" @disabled($g->trang_thai !== 'trong')>
<span>{{ $ten }}</span></label>
@if($loop->iteration === (int) ceil($rows->count() / 2))<span class="dv-aisle"></span>@endif
@endforeach
</div>
@endforeach
</div>
<div class="dv-legend"><span><b class="lg trong"></b>Trống</span><span><b class="lg vip"></b>VIP</span><span><b class="lg chon"></b>Đang chọn</span><span><b class="lg giu"></b>Đang được giữ</span><span><b class="lg ban"></b>Đã bán</span></div>
<p class="m3-note muted" style="font-size:12px">Trạng thái ghế tự cập nhật mỗi 3 giây · lần cập nhật cuối: <span id="dv-luc">--:--:--</span></p>
<div class="dv-toast" id="dv-toast" role="alert"></div>
</div>
<aside class="dv-panel dv-summary">
@php($dsGhe = $ghes->flatten(1))
@php($giaThuong = $dsGhe->first(fn ($g) => $g->gheNgoi->loai_ghe === 'thuong')?->gia_ve)
@php($giaVip = $dsGhe->first(fn ($g) => $g->gheNgoi->loai_ghe === 'vip')?->gia_ve)
<div class="dv-sumhead">
@include('datve._poster', ['content' => $suat->noiDung, 'small' => true, 'class' => 'dv-poster-sm'])
<div><h3>{{ $suat->noiDung->tieu_de }}</h3><p class="muted" style="margin:0;font-size:13px">{{ $suat->bat_dau_luc->format('H:i · d/m/Y') }}<br>{{ $suat->phongChieu->diaDiem->ten_dia_diem }} · {{ $suat->phongChieu->ten_phong }}</p></div>
</div>
<div class="dv-prices">
@if($giaThuong)<span><i class="lg trong"></i>Thường <strong>{{ number_format($giaThuong, 0, ',', '.') }}đ</strong></span>@endif
@if($giaVip)<span><i class="lg vip"></i>VIP <strong>{{ number_format($giaVip, 0, ',', '.') }}đ</strong></span>@endif
</div>
<p class="muted" style="margin:0">Ghế đã chọn: <strong id="dv-sl">0</strong></p>
<div id="dv-ds" class="dv-chips"><span class="muted">Chưa chọn ghế</span></div>
<div class="dv-totalrow"><span class="muted">Tổng tiền</span><div class="dv-total" id="dv-tong">0đ</div></div>
<p class="muted" style="font-size:12px">Ghế được giữ {{ \App\Services\DatVeService::PHUT_GIU_GHE }} phút sau khi bấm tiếp tục. Tối đa {{ \App\Services\DatVeService::TOI_DA_GHE }} ghế/đơn.</p>
<button class="btn btn-block dv-go" id="dv-btn" disabled>Tiếp tục thanh toán →</button>
</aside>
</form>
</section>
@endsection
@push('scripts')
<script>
(() => {
  const map = document.getElementById('dv-map');
  const URL_TRANG_THAI = map.dataset.urlTrangThai;
  const MAX = Number(map.dataset.max);
  const toast = document.getElementById('dv-toast');
  const dinhDang = n => new Intl.NumberFormat('vi-VN').format(n) + 'đ';
  const TRANG_THAI = ['dang_giu', 'da_ban', 'tam_khoa', 'cua-toi'];

  function tinhTong() {
    const chon = [...map.querySelectorAll('input:checked')];
        const ds = document.getElementById('dv-ds');
        ds.innerHTML = '';
        if (!chon.length) { ds.innerHTML = '<span class="muted">Chưa chọn ghế</span>'; }
        chon.forEach(i => { const s = document.createElement('span'); s.className = 'dv-chip'; s.textContent = i.dataset.ten; ds.appendChild(s); });
        document.getElementById('dv-sl').textContent = chon.length;
        document.getElementById('dv-tong').textContent = dinhDang(chon.reduce((s, i) => s + parseFloat(i.dataset.gia), 0));
        document.getElementById('dv-btn').disabled = chon.length === 0;
  }

  map.addEventListener('change', e => {
    if (!e.target.matches('input')) return;
    if (map.querySelectorAll('input:checked').length > MAX) {
      e.target.checked = false;
      alert('Mỗi đơn chỉ được đặt tối đa ' + MAX + ' ghế.');
    }
    e.target.closest('label').classList.toggle('chon', e.target.checked);
    tinhTong();
  });

  async function capNhat() {
    if (document.hidden) return;
    try {
      const r = await fetch(URL_TRANG_THAI, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
      if (!r.ok) return;
      const d = await r.json();
      const biMat = [];
      d.ghes.forEach(g => {
        const input = map.querySelector('input[value="' + g.id + '"]');
        if (!input) return;
        const label = input.closest('label');
        TRANG_THAI.forEach(c => label.classList.remove(c));
        if (g.trang_thai === 'trong') { input.disabled = false; return; }
        if (input.checked) { input.checked = false; label.classList.remove('chon'); biMat.push(input.dataset.ten); }
        input.disabled = true;
        label.classList.add(g.trang_thai);
        if (g.cua_toi) label.classList.add('cua-toi');
      });
      document.getElementById('dv-luc').textContent = d.luc;
      if (biMat.length) {
        toast.textContent = 'Ghế ' + biMat.join(', ') + ' vừa được người khác chọn. Vui lòng chọn ghế khác.';
        toast.style.display = 'block';
        setTimeout(() => toast.style.display = 'none', 6000);
        tinhTong();
      }
    } catch (e) { /* mất mạng hoặc hết phiên: bỏ qua lần này */ }
  }
  setInterval(capNhat, 3000);
  capNhat();
})();
</script>
@endpush