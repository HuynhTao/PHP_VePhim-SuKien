<header class="site-header">
    <div class="container header-inner">
        <a href="{{ route('home') }}" class="brand"><span class="brand-icon">?</span><span>VePhim</span><span>SuKien</span></a>
        <nav aria-label="Điều hướng chính">
            <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home') && !request('loai')])>Khám phá</a>
            <a href="{{ route('home', ['loai' => 'phim']) }}" @class(['active' => request('loai') === 'phim'])>Phim</a>
            <a href="{{ route('home', ['loai' => 'su_kien']) }}" @class(['active' => request('loai') === 'su_kien'])>Sự kiện</a>
            @auth
                @if(auth()->user()->isAdmin())<a href="{{ route('admin.users.index') }}" @class(['active' => request()->routeIs('admin.*')])>Quản trị</a>@endif
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

