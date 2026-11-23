<header class="site-header">
    <div class="container header-inner">
        <a href="{{ route('home') }}" class="brand">
            <span class="brand-icon">M</span>
            <span>VePhim</span><span class="brand-accent">SuKien</span>
        </a>
        <nav aria-label="Điều hướng chính">
            <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home') && !request('loai') && !request('muc')])>Khám phá</a>
            <a href="{{ route('home', ['loai' => 'phim']) }}" @class(['active' => request('loai') === 'phim'])>Phim chiếu rạp</a>
            <a href="{{ route('home', ['loai' => 'su_kien']) }}" @class(['active' => request('loai') === 'su_kien'])>Sự kiện</a>
            <a href="{{ route('datve.index') }}" @class(['active' => request()->routeIs('datve.*')])>Lịch chiếu &amp; Đặt vé</a>
            @auth
                <a href="{{ route('orders.index') }}" @class(['active' => request()->routeIs('orders.*')])>Vé của tôi</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
                    <a href="{{ route('admin.orders.index') }}" @class(['active' => request()->routeIs('admin.orders.*')])>Đơn đặt vé</a>
                    <a href="{{ route('admin.noidung.index') }}" @class(['active' => request()->routeIs('admin.noidung.*')])>Phim &amp; sự kiện</a>
                    <a href="{{ route('admin.dia-diem.index') }}" @class(['active' => request()->routeIs('admin.dia-diem.*') || request()->routeIs('admin.phong-chieu.*') || request()->routeIs('admin.ghe.*') || request()->routeIs('admin.suat.*')])>Rạp &amp; Suất chiếu</a>
                    <a href="{{ route('admin.users.index') }}" @class(['active' => request()->routeIs('admin.users.*')])>Tài khoản</a>
                @endif
            @endauth
        </nav>
        <div class="account-nav">
            @auth
                <a href="{{ route('account') }}" @class(['account-pill', 'active' => request()->routeIs('account')])>
                    <span class="account-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->ho_ten, 0, 1)) }}</span>
                    <span class="account-name">{{ auth()->user()->ho_ten }}</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-small btn-outline">Đăng xuất</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-login-link">Đăng nhập</a>
                <a href="{{ route('register') }}" class="btn btn-small">Đăng ký</a>
            @endauth
        </div>
    </div>
</header>
