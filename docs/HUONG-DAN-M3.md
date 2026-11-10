# Hướng dẫn M3 — Văn Thành

## 1. Cách dùng khi bạn đã chạy được bản cũ

1. Giữ một bản sao thư mục project đang chạy trước khi thay code.
2. Giải nén bản này vào một thư mục mới, ví dụ `C:\xampp\htdocs\VePhim-M3`.
3. Sao chép **file `.env` của project cũ** sang thư mục mới. Giữ nguyên `APP_KEY`, thông tin database, tên database và mật khẩu của bạn.
4. Trong `.env`, kiểm tra `DB_CONNECTION=mysql`, và thêm `APP_TIMEZONE=Asia/Ho_Chi_Minh`. Đặt `APP_URL=http://127.0.0.1:8000` nếu dùng cổng 8000.
5. Bật MySQL trong XAMPP. Tại terminal của thư mục mới chạy:

```powershell
composer install
php artisan config:clear
php artisan migrate
php artisan serve
```

Bản M3 **không thêm hoặc sửa migration**, nên không cần xóa database, không dùng `migrate:fresh`. Giữ nguyên dữ liệu và tài khoản đang dùng. Nếu đã có `.env` và khóa ứng dụng thì không cần chạy lại `key:generate`.

Đóng server của project cũ trước khi chạy server mới ở cổng 8000. Có thể dùng cổng 8001 (`php artisan serve --port=8001`) nếu cần chạy hai bản.

## 2. Cài mới nếu chưa có database/project chạy được

Mở PowerShell tại thư mục đã giải nén, nơi có `artisan` và `composer.json`:

```powershell
composer install
if (!(Test-Path .env)) { Copy-Item .env.example .env }
php artisan key:generate
```

Tạo database rỗng `webphim` trong phpMyAdmin, chọn `utf8mb4_unicode_ci`. File `.env`:

```ini
APP_ENV=local
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=Asia/Ho_Chi_Minh
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=webphim
DB_USERNAME=root
DB_PASSWORD=
```

Điền mật khẩu nếu tài khoản MySQL của bạn có mật khẩu. Nếu đã dùng tên database khác, giữ tên đó.

```powershell
php artisan config:clear
php artisan migrate --seed
php artisan serve
```

Không bắt buộc bật Apache nếu dùng `artisan serve`; cần Apache khi mở phpMyAdmin. Không cần chạy `npm install` hoặc Vite cho giao diện hiện tại.

Tài khoản mẫu cho cài mới:

| Vai trò | Email | Mật khẩu |
| --- | --- | --- |
| Admin | admin@example.com | Admin@123456 |
| User | user@example.com | User@123456 |

Seeder không đặt lại mật khẩu nếu email đã tồn tại.

## 3. Chức năng hoàn thiện

| Phần | Đường dẫn | Chức năng |
| --- | --- | --- |
| Rạp | `/quan-tri/dia-diem` | CRUD, tìm tên/địa chỉ/tỉnh thành, lọc trạng thái, phân trang, đếm phòng |
| Bản đồ | `/quan-tri/dia-diem/{id}/ban-do` | Google Maps nhúng và liên kết mở bản đồ |
| Phòng | `/quan-tri/phong-chieu` | CRUD, tìm tên, lọc rạp/trạng thái, phân trang, số ghế/số suất |
| Ghế | `/quan-tri/phong-chieu/{id}` | Sơ đồ theo hàng, thường/VIP/ngừng sử dụng, thêm/sửa/xóa từng ghế, tạo lại sơ đồ |
| Suất | `/quan-tri/suat` | CRUD, tìm nội dung, lọc rạp/phòng/ngày/trạng thái, phân trang |
| Chi tiết suất | `/quan-tri/suat/{id}` | Sơ đồ, giá vé, số ghế trống/giữ/bán/khóa |

Tất cả route M3 đều nằm trong middleware `auth`, `role:admin`. User/khách không được sửa dữ liệu bằng cách truy cập URL hay gửi request trực tiếp.

## 4. Quy tắc nghiệp vụ

### Phòng và ghế

- Tên phòng duy nhất trong từng rạp; các rạp khác nhau được dùng cùng tên phòng.
- Tạo phòng nhập 1–26 hàng, 1–50 ghế/hàng. Tổng ghế tối đa 1.300; hàng A–Z, số ghế 1–50. Các hàng VIP nằm cuối phòng, có thể chọn 0 hàng VIP.
- Sức chứa tính từ các ghế thực tế, không tạo cột mới vào schema M1.
- Trước khi có suất: được thêm, sửa, xóa ghế hoặc sinh lại toàn bộ sơ đồ. Ghế ngừng sử dụng không được đưa vào suất mới.
- Sau khi có bất kỳ suất nào: khóa thay đổi sơ đồ. Muốn sơ đồ khác, tạo phòng mới; không tự xóa ghế đã có lịch sử đặt vé.
- Không xóa rạp có phòng. Không xóa phòng có suất hoặc ghế theo suất. Phòng chưa có suất được xóa cùng các ghế chưa sử dụng.
- Phòng đã có suất không được chuyển rạp. Có thể đổi tên, loại phòng hoặc trạng thái bảo trì. Chuyển bảo trì không tự hủy suất/vé đã có.

### Suất và lịch

- Chọn `noi_dung` đã `cong_bo`; dùng dữ liệu mẫu M1 mà không cần đợi M2.
- Giờ kết thúc phải sau giờ bắt đầu. Với phim, phải đủ `thoi_luong_phut`, và không trước `ngay_khoi_chieu`. Sự kiện nhập giờ kết thúc trực tiếp.
- Chỉ mở bán tại rạp/phòng đang hoạt động và có ít nhất một ghế đang sử dụng.
- Hai khoảng thời gian giao nhau khi `bat_dau_cu < ket_thuc_moi` và `ket_thuc_cu > bat_dau_moi`. Kiểm tra được suất qua nửa đêm, suất bao trùm và khi sửa (loại trừ chính suất đang sửa).
- Cho phép suất sau bắt đầu đúng lúc suất trước kết thúc. Chưa cộng thời gian vệ sinh phòng; nếu nhóm cần, bổ sung quy tắc chung trước khi tích hợp.
- Suất `dong` vẫn chiếm lịch; suất `huy` không chiếm khoảng giờ. Ràng buộc unique của M1 vẫn cấm hai bản ghi cùng phòng và cùng giờ bắt đầu, kể cả bản ghi đã hủy. Khi đó chỉnh/xóa suất cũ chưa có vé, không sửa schema để né ràng buộc.
- Khóa hàng phòng bằng `lockForUpdate()` trong transaction trước khi kiểm tra lịch và ghi suất; hai thao tác tạo cùng phòng được xử lý lần lượt trên MySQL/MariaDB (InnoDB).
- Tạo suất đồng thời tạo `ghe_theo_suat`, giá thường/VIP theo form. Giá được lưu DECIMAL, tối đa 2 chữ số thập phân. Suất hủy tạo ghế `tam_khoa`.
- Suất chưa giữ/bán/phát hành vé có thể sửa (tái tạo danh sách ghế và giá) hoặc xóa cùng ghế theo suất.
- Có ghế `dang_giu`, `da_ban` hoặc bất kỳ bản ghi vé nào: chặn sửa/xóa toàn bộ suất. Hủy và hoàn vé cần luồng M4/M5; không tự xử lý trong M3.

### Phối hợp M4

M3 chuẩn bị inventory `ghe_theo_suat` ngay lúc lập suất. M4 dùng `suat_id`, `ghe_ngoi_id`, `gia_ve`, `trang_thai`, `giu_den_luc` theo schema M1; không sinh trùng inventory.

Khi giữ/bán ghế, M4 phải đọc lại các hàng ghế theo suất bằng `lockForUpdate()` trong transaction, kiểm tra đúng phòng, trạng thái suất `mo_ban`, phòng/rạp hoạt động, giờ bắt đầu và trạng thái giữ/bán. Không dùng một bản ghi cũ đã đọc ngoài transaction để ghi vé. Nếu thao tác cần khóa nhiều ghế, khóa theo id tăng dần; nếu cần khóa cả phòng/suất, thống nhất thứ tự phòng → suất → ghế để giảm deadlock.

M3 không thay thế luồng giữ ghế, đặt vé, thanh toán hoặc tự nhả ghế hết hạn của M4. Trang chi tiết suất là màn hình quản trị, tải lại để xem dữ liệu mới, không phải AJAX chọn ghế của khách.

### Google Maps

Nhúng bằng địa chỉ rạp, không dùng khóa API trong project. Cần Internet để tải iframe. Có nút mở Google Maps nếu iframe không tải. Tên/địa chỉ demo không bảo đảm là rạp thật; nhập địa chỉ thật khi demo vị trí.

## 5. Kịch bản chạy thử / demo

1. Admin → Quản lý rạp → thêm “Rạp Văn Thành”, nhập địa chỉ thật → mở Bản đồ.
2. Phòng & ghế → thêm phòng cho rạp đó, 5 hàng × 10 ghế, 1 hàng VIP → kiểm tra 50 ghế, hàng E là VIP.
3. Trước khi có suất, sửa A1 thành VIP; ngừng sử dụng A2. Thử thêm A1 lần nữa: báo trùng ghế.
4. Tạo suất cho phim mẫu, ngày sau ngày khởi chiếu, 18:00–20:00; giá thường 90.000, VIP 120.000 → chi tiết có 49 ghế, không có A2.
5. Tạo suất cùng phòng 19:00–21:00 → bị chặn trùng lịch. Thử 20:00–22:00 → được. Cùng giờ ở phòng khác → được.
6. Dùng tìm kiếm/lọc/phân trang; thử sửa suất vừa tạo (chưa có vé) → được.
7. Mở suất mẫu đã có một vé demo → thử sửa/xóa → bị chặn. Thử xóa phòng có suất → bị chặn.
8. Đăng xuất hoặc dùng tài khoản User mở URL quản trị → bị chuyển đăng nhập hoặc báo 403.

## 6. Kiểm thử tự động

```powershell
php artisan test
```

Mặc định dùng SQLite `:memory:` riêng cho kiểm thử, không dùng database đang chạy. PHP cần `pdo_sqlite` để chạy bộ test mặc định.

Muốn chạy bằng MySQL/MariaDB: tạo **database kiểm thử riêng** `ve_phim_m3_test`, kiểm tra thông tin kết nối trong `phpunit.mysql.xml`, rồi:

```powershell
php vendor/bin/phpunit -c phpunit.mysql.xml
```

Chỉ trỏ cấu hình test vào database kiểm thử rỗng; `RefreshDatabase` tạo lại bảng trong database được cấu hình.

Kết quả xác thực trong môi trường phát triển: xem `KET-QUA-KIEM-THU-M3.md`. Máy kiểm thử PHP 8.3/Linux; cần kiểm tra lần cuối trên PHP 8.5/XAMPP Windows của bạn.

## 7. Các file thay đổi / bổ sung

- `app/Http/Controllers/Admin/DiaDiemController.php`: thêm trang bản đồ.
- Thêm `PhongChieuController.php`, `GheNgoiController.php`, `SuatController.php` trong cùng thư mục.
- Thêm `app/Services/QuanLyGhe.php`, `QuanLySuat.php`: sinh ghế, transaction, chặn trùng lịch và bảo vệ lịch sử.
- Thêm Blade trong `resources/views/admin/phong_chieu`, `admin/suat`, bản đồ `admin/dia_diem/map.blade.php`, menu `admin/m3-nav.blade.php`.
- Sửa danh sách rạp để có liên kết xem phòng/bản đồ; sửa header thêm Phòng & ghế, Suất chiếu.
- `routes/web.php`: route Admin M3.
- `public/css/m3.css`, `layouts/app.blade.php`: CSS bổ sung, giữ CSS M1.
- `lang/vi/validation.php`: bổ sung thông báo cho số, giờ, trạng thái, ghế.
- `config/app.php`: múi giờ mặc định `Asia/Ho_Chi_Minh`, có thể cấu hình bằng `APP_TIMEZONE`.
- `.env.example`: mẫu MySQL; `README.md`, tài liệu M3, `phpunit.mysql.xml`, `tests/Feature/M3ManagementTest.php`.
- Không đổi migration/model gốc, không bổ sung bảng, không sửa chức năng M1/M2/M4/M5.

## 8. Git và ghép nhóm

Bản RAR/ZIP không thay thế lịch sử Git. Khi làm với repo nhóm, clone repo của M1, tạo nhánh `feature/m3-suatchieu`, rồi chép các file M3 trên vào nhánh đó. Cài thư viện và kiểm thử trước khi commit. Dùng `git diff` để kiểm tra phần sửa chung tại route/header/layout/validation.

Khi các thành viên hoàn tất: tạo PR vào `develop`, xử lý xung đột cùng người sở hữu module, chạy test và demo luồng đặt vé. Sau khi nhóm xác nhận bản nộp, merge `develop` vào `main` và gắn tag phiên bản cuối. Chưa thực hiện merge/tag bản nộp trong gói này vì chưa có repo và code các module còn lại.
