@extends('layouts.app')

@section('title', $item->exists ? 'Sửa phim/sự kiện' : 'Thêm phim/sự kiện')

@section('content')
@php
    $isEdit = $item->exists;
    $loai = old('loai_noi_dung', $item->loai_noi_dung ?: 'phim');
    $tuoi = old('gioi_han_tuoi', $item->phim?->gioi_han_tuoi ?? $item->suKien?->gioi_han_tuoi ?? 'P');
@endphp
<section class="form-card account-card form-wide">
    <a class="back-link" href="{{ route('admin.noidung.index') }}">← Danh sách phim &amp; sự kiện</a>
    <span class="eyebrow">QUẢN LÝ NỘI DUNG · MODULE 2</span>
    <h1>{{ $isEdit ? 'Sửa phim/sự kiện' : 'Thêm phim/sự kiện' }}</h1>

    <form method="POST" enctype="multipart/form-data"
          action="{{ $isEdit ? route('admin.noidung.update', $item) : route('admin.noidung.store') }}">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <label for="loai_noi_dung">Loại nội dung</label>
        @if($isEdit)
            <input type="hidden" name="loai_noi_dung" value="{{ $loai }}">
            <input value="{{ $loai === 'phim' ? 'Phim' : 'Sự kiện' }}" disabled>
        @else
            <select id="loai_noi_dung" name="loai_noi_dung" required>
                <option value="phim" @selected($loai === 'phim')>Phim</option>
                <option value="su_kien" @selected($loai === 'su_kien')>Sự kiện</option>
            </select>
        @endif

        <label for="tieu_de">Tiêu đề</label>
        <input id="tieu_de" name="tieu_de" value="{{ old('tieu_de', $item->tieu_de) }}" maxlength="255" required>

        <label for="mo_ta">Mô tả</label>
        <textarea id="mo_ta" name="mo_ta" maxlength="5000">{{ old('mo_ta', $item->mo_ta) }}</textarea>

        <label for="anh_bia">Poster (JPG/PNG/WEBP, tối đa 2 MB)</label>
        <input id="anh_bia" name="anh_bia" type="file" accept="image/jpeg,image/png,image/webp">
        <img id="poster-preview" class="poster-preview" alt="Xem trước poster"
             @if($item->anh_bia_url) src="{{ $item->anh_bia_url }}" @else hidden @endif>
        @if($isEdit)<p class="hint">Không chọn file mới thì giữ poster hiện tại.</p>@endif

        <div class="form-grid">
            <div>
                <label for="trang_thai">Trạng thái</label>
                <select id="trang_thai" name="trang_thai" required>
                    @foreach(['nhap' => 'Nháp', 'cong_bo' => 'Công bố', 'an' => 'Ẩn'] as $v => $t)
                        <option value="{{ $v }}" @selected(old('trang_thai', $item->trang_thai) === $v)>{{ $t }}</option>
                    @endforeach
                </select>
                <p class="hint">Chỉ nội dung "Công bố" mới hiện ở trang chủ và chọn được khi tạo suất chiếu.</p>
            </div>
            <div>
                <label for="gioi_han_tuoi">Giới hạn tuổi</label>
                <select id="gioi_han_tuoi" name="gioi_han_tuoi" required>
                    @foreach(['P' => 'P - Mọi lứa tuổi', 'T13' => 'T13', 'T16' => 'T16', 'T18' => 'T18'] as $v => $t)
                        <option value="{{ $v }}" @selected($tuoi === $v)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Nhóm trường dành cho PHIM --}}
        <div id="phim-fields">
            <div class="form-grid">
                <div>
                    <label for="thoi_luong_phut">Thời lượng (phút)</label>
                    <input id="thoi_luong_phut" name="thoi_luong_phut" type="number" min="1" max="600"
                           value="{{ old('thoi_luong_phut', $item->phim?->thoi_luong_phut) }}" required>
                </div>
                <div>
                    <label for="ngay_khoi_chieu">Ngày khởi chiếu</label>
                    <input id="ngay_khoi_chieu" name="ngay_khoi_chieu" type="date"
                           value="{{ old('ngay_khoi_chieu', $item->phim?->ngay_khoi_chieu?->format('Y-m-d')) }}" required>
                </div>
            </div>
            <label for="ngon_ngu">Ngôn ngữ</label>
            <input id="ngon_ngu" name="ngon_ngu" maxlength="100"
                   value="{{ old('ngon_ngu', $item->phim?->ngon_ngu) }}" required>
            <label for="the_loai_phim">Thể loại (hành động, tình cảm...)</label>
            <input id="the_loai_phim" name="the_loai" maxlength="100"
                   value="{{ old('the_loai', $item->phim?->the_loai) }}">
        </div>

        {{-- Nhóm trường dành cho SỰ KIỆN --}}
        <div id="sukien-fields">
            <label for="don_vi_to_chuc">Đơn vị tổ chức</label>
            <input id="don_vi_to_chuc" name="don_vi_to_chuc" maxlength="255"
                   value="{{ old('don_vi_to_chuc', $item->suKien?->don_vi_to_chuc) }}" required>
            <label for="the_loai">Thể loại</label>
            <input id="the_loai" name="the_loai" maxlength="100"
                   value="{{ old('the_loai', $item->suKien?->the_loai) }}" required>
        </div>

        <div class="form-actions">
            <button class="btn" type="submit">Lưu nội dung</button>
            <a class="btn btn-outline" href="{{ route('admin.noidung.index') }}">Hủy</a>
        </div>
    </form>
</section>

<script>
(function () {
    const typeEl = document.getElementById('loai_noi_dung');
    const groups = { phim: document.getElementById('phim-fields'), su_kien: document.getElementById('sukien-fields') };

    function sync() {
        const type = typeEl ? typeEl.value : @json($loai);
        Object.keys(groups).forEach(function (key) {
            const on = key === type;
            groups[key].hidden = !on;
            groups[key].querySelectorAll('input,select,textarea').forEach(function (el) { el.disabled = !on; });
        });
    }
    if (typeEl) typeEl.addEventListener('change', sync);
    sync();

    const file = document.getElementById('anh_bia');
    const preview = document.getElementById('poster-preview');
    file.addEventListener('change', function () {
        const f = file.files[0];
        if (!f) return;
        preview.src = URL.createObjectURL(f);
        preview.hidden = false;
    });
})();
</script>
@endsection
