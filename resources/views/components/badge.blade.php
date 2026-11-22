@props([
    'age' => 'P',
    'type' => null,
])
@php
    $code = strtoupper(trim((string) $age));
    $variant = match ($code) {
        'T18', 'C18', '18+' => 'badge-18',
        'T16', 'C16', '16+' => 'badge-16',
        'T13', 'C13', '13+' => 'badge-13',
        default => 'badge-p',
    };
@endphp
<span {{ $attributes->class(['age-badge', $variant]) }}>{{ $code ?: 'P' }}</span>
@if($type)
    <span class="type-pill {{ $type === 'phim' ? 'pill-film' : 'pill-event' }}">
        {{ $type === 'phim' ? 'ĐIỆN ẢNH' : 'SỰ KIỆN' }}
    </span>
@endif
