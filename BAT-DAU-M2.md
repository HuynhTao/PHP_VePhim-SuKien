# Bắt đầu nhanh (bản đã gộp phần M2)

1. Bật MySQL trong XAMPP, tạo database rỗng `webphim` (utf8mb4_unicode_ci) trong phpMyAdmin.
2. Mở PowerShell trong thư mục này (có file `artisan`) rồi chạy lần lượt:

   composer install
   php artisan key:generate
   php artisan migrate --seed
   php artisan db:seed --class=M2PhimSeeder
   php artisan storage:link
   php artisan serve

3. Mở http://127.0.0.1:8000
   Admin: admin@example.com / Admin@123456
   User : user@example.com / User@123456

Nếu MySQL có mật khẩu, điền vào DB_PASSWORD trong file .env.
Phần M2 nằm ở menu admin "Phim & sự kiện" và trang chủ.
