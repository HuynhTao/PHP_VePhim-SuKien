<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-col-main">
            <a class="brand footer-brand" href="{{ route('home') }}">
                <span class="brand-icon">M</span>
                <span>VePhim</span><span class="brand-accent">SuKien</span>
            </a>
            <p>Nền tảng đặt vé xem phim chiếu rạp, đêm nhạc, live concert và sự kiện giải trí trực tuyến phong cách MoMo Cinema.</p>
            <div class="footer-badges">
                <span>✓ Giữ ghế 10 phút</span>
                <span>✓ Thanh toán MoMo / VNPay</span>
                <span>✓ Vé QR tức thì</span>
            </div>
        </div>
        <div class="footer-links">
            <div>
                <strong>Khám phá</strong>
                <a href="{{ route('home', ['muc' => 'dang_chieu']) }}">Phim đang chiếu</a>
                <a href="{{ route('home', ['muc' => 'sap_chieu']) }}">Phim sắp chiếu</a>
                <a href="{{ route('home', ['loai' => 'su_kien']) }}">Sự kiện &amp; Concert</a>
                <a href="{{ route('datve.index') }}">Lịch chiếu theo rạp</a>
            </div>
            <div>
                <strong>Tài khoản</strong>
                @auth
                    <a href="{{ route('account') }}">Hồ sơ cá nhân</a>
                    <a href="{{ route('orders.index') }}">Lịch sử đặt vé</a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}">Dashboard Quản trị</a>
                    @endif
                @else
                    <a href="{{ route('login') }}">Đăng nhập</a>
                    <a href="{{ route('register') }}">Đăng ký thành viên</a>
                @endauth
            </div>
        </div>
        <div class="footer-copy">
            <p><strong>Đồ án Lập trình Mã nguồn mở PHP (Laravel 12)</strong></p>
            <small>© {{ date('Y') }} VePhimSuKien · Tích hợp hoàn chỉnh 5 Module (M1 – M5).</small>
        </div>
    </div>
</footer>
