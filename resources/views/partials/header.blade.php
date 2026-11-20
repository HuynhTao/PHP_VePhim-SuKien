<nav class="navbar navbar-expand-lg bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ route('home') }}">VePhim<span class="text-success">SuKien</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
            aria-controls="mainNav" aria-expanded="false" aria-label="Mở menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Khám phá</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('home', ['loai' => 'phim']) }}">Phim</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('home', ['loai' => 'su_kien']) }}">Sự kiện</a></li>
        @auth
        <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}">Vé của tôi</a></li>
          @if(auth()->user()->isAdmin())
          <li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Quản trị</a>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}">Đơn đặt vé</a></li>
    <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">Tài khoản</a></li>
  </ul>
</li>
          @endif
        @endauth
      </ul>
      <div class="d-flex align-items-center gap-2">
        @auth
          <a href="{{ route('account') }}" class="text-decoration-none">{{ auth()->user()->ho_ten }}</a>
          <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="btn btn-sm btn-outline-success">Đăng xuất</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="btn btn-sm btn-link">Đăng nhập</a>
          <a href="{{ route('register') }}" class="btn btn-sm btn-success">Đăng ký</a>
        @endauth
      </div>
    </div>
  </div>
</nav>