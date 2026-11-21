<nav class="m3-nav" aria-label="Quản lý cơ sở và lịch">
    <a href="{{ route('admin.dia-diem.index') }}" @class(['active' => request()->routeIs('admin.dia-diem.*')])>Rạp / địa điểm</a>
    <a href="{{ route('admin.phong-chieu.index') }}" @class(['active' => request()->routeIs('admin.phong-chieu.*')])>Phòng & ghế</a>
    <a href="{{ route('admin.suat.index') }}" @class(['active' => request()->routeIs('admin.suat.*')])>Suất chiếu</a>
</nav>
