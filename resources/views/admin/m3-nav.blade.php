<nav class="m3-nav" aria-label="Quản lý hệ thống">
    <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>📊 Dashboard</a>
    <a href="{{ route('admin.orders.index') }}" @class(['active' => request()->routeIs('admin.orders.*')])>🎟️ Đơn đặt vé</a>
    <a href="{{ route('admin.noidung.index') }}" @class(['active' => request()->routeIs('admin.noidung.*')])>🎬 Phim &amp; sự kiện</a>
    <a href="{{ route('admin.dia-diem.index') }}" @class(['active' => request()->routeIs('admin.dia-diem.*')])>🏢 Rạp / địa điểm</a>
    <a href="{{ route('admin.phong-chieu.index') }}" @class(['active' => request()->routeIs('admin.phong-chieu.*') || request()->routeIs('admin.ghe.*')])>💺 Phòng &amp; ghế</a>
    <a href="{{ route('admin.suat.index') }}" @class(['active' => request()->routeIs('admin.suat.*')])>🕒 Suất chiếu</a>
    <a href="{{ route('admin.users.index') }}" @class(['active' => request()->routeIs('admin.users.*')])>👤 Tài khoản</a>
</nav>
