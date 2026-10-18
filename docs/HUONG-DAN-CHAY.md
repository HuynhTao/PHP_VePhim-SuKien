# CineViet — Phần nền tảng Laravel

## Đã triển khai

- Laravel 12, PHP 8.2+, giao diện Blade và CSS thuần, không cần chạy Vite để xem giao diện hiện tại.
- Migration và model quan hệ cho 12 bảng nghiệp vụ: `nguoi_dung`, `dia_diem`, `phong_chieu`, `ghe_ngoi`, `noi_dung`, `phim`, `su_kien`, `suat`, `ghe_theo_suat`, `don_hang`, `ve`, `thanh_toan`.
- Giữ các bảng hạ tầng mặc định của Laravel: sessions đăng nhập, cache, jobs, password_reset_tokens... Bảng `sessions` của Laravel khác với `suat` chiếu/biểu diễn.
- Đăng ký, đăng nhập, ghi nhớ đăng nhập và đăng xuất bằng session; mật khẩu được băm, form có CSRF; giới hạn tần suất đăng ký/đăng nhập.
- Middleware `role:admin` và `role:admin,user` kiểm tra quyền ở server. Tài khoản tự đăng ký luôn có vai trò User.
- Admin tạo, xem, sửa, xóa tài khoản; tìm kiếm, lọc vai trò, phân trang. Không cho tự xóa/tự gỡ quyền Admin; không xóa người dùng đã có đơn hàng.
- Header/footer chung, trang chủ có tìm kiếm/lọc phim và sự kiện, lịch suất, giao diện responsive và trạng thái không có kết quả.

## Chạy trên máy hiện tại

Mở terminal trong `D:\src\php\doan`:

```powershell
& 'C:\xampp\php\php.exe' artisan migrate --seed
& 'C:\xampp\php\php.exe' artisan serve --host=127.0.0.1 --port=8000
```

Mở http://127.0.0.1:8000. SQLite đã được cấu hình trong `.env`. Seeder chỉ cho phép môi trường `local` hoặc `testing`, có thể chạy lại mà không nhân đôi dữ liệu demo. Seeder không đổi lại mật khẩu tài khoản đã tồn tại.

| Vai trò | Email | Mật khẩu mẫu |
|---|---|---|
| Admin | admin@example.com | Admin@123456 |
| User | user@example.com | User@123456 |

Các tài khoản và nội dung trên phục vụ chạy đồ án ở máy local. Tài khoản User mẫu có một đơn hàng, một vé và một giao dịch demo để minh họa quan hệ. Không có thanh toán thật.

## Khi thành viên nhóm lấy dự án lần đầu

```powershell
composer install
if (!(Test-Path .env)) { Copy-Item .env.example .env }
php artisan key:generate
if (!(Test-Path database/database.sqlite)) { New-Item -ItemType File -Path database/database.sqlite }
php artisan migrate --seed
php artisan serve
```

Chỉ tạo `.env` và `database.sqlite` nếu chưa có file tương ứng. Nếu PHP chưa được thêm vào PATH, thay `php` bằng đường dẫn tới PHP trên máy. Không dùng `migrate:fresh` trên cơ sở dữ liệu cần giữ.

Muốn chuyển sang MySQL: tạo cơ sở dữ liệu rỗng, thiết lập `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` trong `.env`, rồi chạy `php artisan config:clear` và `php artisan migrate --seed`. Thay đổi cấu hình không tự chuyển dữ liệu SQLite cũ sang MySQL. Bản hiện tại đã kiểm thử trên SQLite; chưa kiểm thử bằng một máy chủ MySQL.

## Đường dẫn chính

| Đường dẫn | Nội dung |
|---|---|
| `/` | Trang chủ |
| `/dang-ky` | Đăng ký |
| `/dang-nhap` | Đăng nhập |
| `/dang-xuat` (POST) | Đăng xuất |
| `/tai-khoan` | Thông tin tài khoản đang đăng nhập |
| `/quan-tri/nguoi-dung` | CRUD tài khoản, chỉ Admin |

## Cấu trúc liên quan

- `database/migrations`: migration bổ sung đổi `users` sang `nguoi_dung` và tạo bảng nghiệp vụ; không sửa migration đã chạy để làm mất dữ liệu.
- `database/seeders/DemoSeeder.php`: dữ liệu mẫu cho toàn bộ bảng nghiệp vụ, được gọi từ `DatabaseSeeder`.
- `app/Models`: khai báo tên bảng tiếng Việt, thuộc tính và quan hệ.
- `app/Http/Controllers/AuthController.php`: xác thực.
- `app/Http/Controllers/Admin/UserController.php`: quản lý tài khoản.
- `app/Http/Middleware/EnsureRole.php`: kiểm tra vai trò.
- `resources/views/layouts`, `partials`: layout, header/footer, phân trang.
- `public/css/app.css`: CSS dùng trực tiếp, không phụ thuộc thư viện bên ngoài.
- `lang/vi/validation.php`: thông báo validation tiếng Việt.

Laravel vẫn dùng `id`, `created_at`, `updated_at`, `remember_token`, `email_verified_at` để thuận tiện tích hợp. Tài khoản dùng `ho_ten`, `mat_khau`; model User đã ánh xạ cột mật khẩu cho Auth. Trong form mật khẩu vẫn gửi trường `password` để dùng xác thực, xác nhận và cơ chế ẩn dữ liệu mật khẩu của Laravel.

## Kiểm thử

```powershell
php artisan test
```

Bộ kiểm thử dùng SQLite `:memory:`, không tác động dữ liệu chạy thử. Bao gồm đăng ký/chống chèn quyền Admin, đăng nhập/đăng xuất, hạn chế truy cập, CRUD, mật khẩu, tìm kiếm, dữ liệu mẫu chạy lặp và ràng buộc chống trùng ghế/vé.

## Phần dành cho nhiệm vụ tiếp theo

Đã có schema và dữ liệu để phát triển đặt vé, nhưng chưa có màn hình chọn ghế, dịch vụ giữ ghế/nhả ghế, tích hợp cổng thanh toán, hoàn vé hay CRUD nội dung/rạp. Khi xây phần đó cần kiểm tra ghế thuộc đúng phòng của suất, tránh trùng khoảng thời gian suất, giữ/bán ghế trong transaction, đồng bộ trạng thái đơn–ghế–vé–thanh toán và xác nhận mỗi nội dung có đúng loại chi tiết. Unique ở bảng vé đang giả định một ghế chỉ phát hành một vé trong mỗi suất; luồng hoàn vé rồi bán lại cần thiết kế thêm lịch sử trước khi triển khai.
