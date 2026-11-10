@php
    $url = $content->anhBiaUrl();
    $mau = [['#1f514a','#122c36'],['#483e69','#202e40'],['#ac5636','#662747'],['#0f4c75','#1b262c'],['#7b2d43','#2b1b3d'],['#2d6a4f','#1b4332']][$content->id % 6];
    $style = $url ? "background-image:url('".$url."')" : "background:linear-gradient(150deg,{$mau[0]},{$mau[1]})";
        $tuoi = $content->phim?->gioi_han_tuoi ?? $content->suKien?->gioi_han_tuoi;
    $thuocTinhStyle = 'style="'.e($style).'"';
@endphp
<div class="dv-poster {{ $class ?? '' }}" {!! $thuocTinhStyle !!} role="img" aria-label="Poster {{ $content->tieu_de }}">
    @empty($small)
        <div class="dv-badges">
            @if($tuoi)<span class="dv-badge">{{ $tuoi }}</span>@endif
            <span class="dv-badge alt">{{ $content->loai_noi_dung === 'phim' ? 'PHIM' : 'SỰ KIỆN' }}</span>
        </div>
    @endempty
    @unless($url)<span class="dv-poster-title">{{ $content->tieu_de }}</span>@endunless
</div>