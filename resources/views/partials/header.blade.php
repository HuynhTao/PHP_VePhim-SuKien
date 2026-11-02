<header class="site-header">
    <div class="container header-inner">
        <a href="{{ route('home') }}" class="brand"><span class="brand-icon">V</span><span>VePhim</span><span>SuKien</span></a>
        <nav aria-label="Điều hướng chính">
            <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home') && !request('loai')])>Khám phá</a>
            <a href="{{ route('home', ['loai' => 'phim']) }}" @class(['active' => request('loai') === 'phim'])>Phim</a>
            <a href="{{ route('home', ['loai' => 'su_kien']) }}" @class(['active' => request('loai') === 'su_kien'])>Sự kiện</a>
           @auth
    @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.users.index') }}"
           @class(['active' => request()->routeIs('admin.users.*')])>
            Quản lý tài khoản
        </a>

        <a href="{{ route('admin.dia-diem.index') }}"
           @class(['active' => request()->routeIs('admin.dia-diem.*')])>
            Quản lý rạp
        </a>
        <a href="{{ route('admin.phong-chieu.index') }}" @class(['active' => request()->routeIs('admin.phong-chieu.*') || request()->routeIs('admin.ghe.*')])>Phòng & ghế</a>
        <a href="{{ route('admin.suat.index') }}" @class(['active' => request()->routeIs('admin.suat.*')])>Suất chiếu</a>
    @endif
@endauth
        </nav>
        <div class="account-nav">
            @auth
                <a href="{{ route('account') }}" class="account-name">{{ auth()->user()->ho_ten }}</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-small btn-outline">Đăng xuất</button></form>
            @else
                <a href="{{ route('login') }}">Đăng nhập</a>
                <a href="{{ route('register') }}" class="btn btn-small">Đăng ký</a>
            @endauth
        </div>
    </div>
</header>

