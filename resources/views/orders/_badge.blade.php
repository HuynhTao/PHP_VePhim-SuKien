@php
  $map = [
    'cho_thanh_toan' => ['warning', 'Chờ thanh toán'],
    'da_thanh_toan'  => ['success', 'Đã thanh toán'],
    'da_huy'         => ['secondary', 'Đã hủy'],
    'het_han'        => ['dark', 'Hết hạn'],
  ];
  [$mau, $nhan] = $map[$trangThai] ?? ['secondary', $trangThai];
@endphp
<span class="m5-badge m5-badge-{{ $mau }}">{{ $nhan }}</span>
