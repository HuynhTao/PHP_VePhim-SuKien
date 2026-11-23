@extends('layouts.app')

@section('title', 'Quản lý phim & sự kiện')

@section('content')
<section class="admin-page">
    @include('admin.m3-nav')
    <div class="section-heading">
        <div>
            <span class="eyebrow">QUẢN TRỊ HỆ THỐNG · MODULE 2</span>
            <h1>Quản lý phim &amp; sự kiện</h1>
            <p class="muted">{{ $items->total() }} nội dung phù hợp</p>
        </div>
        <div class="cell-actions">
            <a class="btn" href="{{ route('admin.noidung.create', ['loai' => 'phim']) }}">+ Thêm phim</a>
            <a class="btn btn-outline" href="{{ route('admin.noidung.create', ['loai' => 'su_kien']) }}">+ Thêm sự kiện</a>
        </div>
    </div>

    <form class="filter-bar" method="GET" action="{{ route('admin.noidung.index') }}">
        <label class="sr-only" for="q">Tìm theo tên</label>
        <input id="q" name="q" value="{{ request('q') }}" placeholder="🔎 Tên phim / sự kiện" maxlength="100">

        <label class="sr-only" for="loai">Loại</label>
        <select id="loai" name="loai">
            <option value="">Tất cả loại</option>
            <option value="phim" @selected(request('loai') === 'phim')>Phim</option>
            <option value="su_kien" @selected(request('loai') === 'su_kien')>Sự kiện</option>
        </select>

        <label class="sr-only" for="the_loai">Thể loại</label>
        <input id="the_loai" name="the_loai" value="{{ request('the_loai') }}" placeholder="Thể loại" maxlength="100" style="max-width:200px">

        <label class="sr-only" for="trang_thai">Trạng thái</label>
        <select id="trang_thai" name="trang_thai">
            <option value="">Tất cả trạng thái</option>
            <option value="nhap" @selected(request('trang_thai') === 'nhap')>Nháp</option>
            <option value="cong_bo" @selected(request('trang_thai') === 'cong_bo')>Công bố</option>
            <option value="an" @selected(request('trang_thai') === 'an')>Ẩn</option>
        </select>

        <button class="btn" type="submit">Tìm kiếm</button>
        <a class="btn btn-outline btn-small" href="{{ route('admin.noidung.index') }}">Đặt lại</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Poster</th><th>Tiêu đề</th><th>Loại</th><th>Thông tin</th>
                    <th>Trạng thái</th><th>Số suất</th><th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                @php $laPhim = $item->loai_noi_dung === 'phim'; @endphp
                <tr>
                    <td>
                        @if($item->anh_bia_url)
                            <img class="thumb" src="{{ $item->anh_bia_url }}" alt="Poster {{ $item->tieu_de }}">
                        @else
                            <span class="thumb" aria-hidden="true"></span>
                        @endif
                    </td>
                    <td><strong>{{ $item->tieu_de }}</strong></td>
                    <td>{{ $laPhim ? 'Phim' : 'Sự kiện' }}</td>
                    <td>
                        @if($laPhim)
                            {{ $item->phim?->the_loai ? $item->phim->the_loai.' · ' : '' }}{{ $item->phim?->thoi_luong_phut }} phút · khởi chiếu {{ $item->phim?->ngay_khoi_chieu?->format('d/m/Y') }}
                        @else
                            {{ $item->suKien?->the_loai }} · {{ $item->suKien?->don_vi_to_chuc }}
                        @endif
                    </td>
                    <td><span class="pill pill-{{ $item->trang_thai }}">{{ ['nhap' => 'Nháp', 'cong_bo' => 'Công bố', 'an' => 'Ẩn'][$item->trang_thai] }}</span></td>
                    <td>{{ $item->suats_count }}</td>
                    <td>
                        <div class="cell-actions">
                            <a class="btn btn-small btn-outline" href="{{ route('noi-dung.show', $item) }}">Xem</a>
                            <a class="btn btn-small" href="{{ route('admin.noidung.edit', $item) }}">Sửa</a>
                            <form method="POST" action="{{ route('admin.noidung.destroy', $item) }}"
                                  onsubmit="return confirm('Bạn có chắc muốn xóa nội dung này?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-small btn-outline" type="submit">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty-state">Không tìm thấy phim/sự kiện nào.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @include('partials.pagination', ['paginator' => $items])
</section>
@endsection
