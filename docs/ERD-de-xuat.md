# ERD — Website dat ve phim va su kien

Ten bang, cot va quan he trong so do deu dung tieng Viet khong dau. Phim va su kien dung chung lich suat va quy trinh ban ve.

```mermaid
erDiagram
    NGUOI_DUNG ||--o{ DON_HANG : dat
    NOI_DUNG ||--o| PHIM : chi_tiet_phim
    NOI_DUNG ||--o| SU_KIEN : chi_tiet_su_kien
    NOI_DUNG ||--o{ SUAT : co_suat
    DIA_DIEM ||--o{ PHONG_CHIEU : co_phong
    PHONG_CHIEU ||--o{ GHE_NGOI : co_ghe
    PHONG_CHIEU ||--o{ SUAT : to_chuc
    SUAT ||--o{ GHE_THEO_SUAT : mo_ban
    GHE_NGOI ||--o{ GHE_THEO_SUAT : duoc_dung_trong
    DON_HANG ||--|{ VE : gom
    GHE_THEO_SUAT ||--o| VE : duoc_ban_thanh
    DON_HANG ||--o{ THANH_TOAN : co_giao_dich

    NGUOI_DUNG {
        bigint id PK
        string ho_ten
        string email UK
        string mat_khau
        string so_dien_thoai
        string vai_tro
        datetime xac_thuc_email_luc
        timestamps ngay_tao_cap_nhat
    }

    DIA_DIEM {
        bigint id PK
        string ten_dia_diem
        string dia_chi
        string tinh_thanh
        string so_dien_thoai
        string trang_thai
        timestamps ngay_tao_cap_nhat
    }

    PHONG_CHIEU {
        bigint id PK
        bigint dia_diem_id FK
        string ten_phong
        string loai_phong
        string trang_thai
        timestamps ngay_tao_cap_nhat
    }

    GHE_NGOI {
        bigint id PK
        bigint phong_chieu_id FK
        string hang_ghe
        integer so_ghe
        string loai_ghe
        boolean dang_su_dung
        timestamps ngay_tao_cap_nhat
    }

    NOI_DUNG {
        bigint id PK
        string loai_noi_dung
        string tieu_de
        text mo_ta
        string anh_bia
        string trang_thai
        timestamps ngay_tao_cap_nhat
    }

    PHIM {
        bigint noi_dung_id PK,FK
        integer thoi_luong_phut
        string gioi_han_tuoi
        string ngon_ngu
        date ngay_khoi_chieu
    }

    SU_KIEN {
        bigint noi_dung_id PK,FK
        string don_vi_to_chuc
        string the_loai
        string gioi_han_tuoi
    }

    SUAT {
        bigint id PK
        bigint noi_dung_id FK
        bigint phong_chieu_id FK
        datetime bat_dau_luc
        datetime ket_thuc_luc
        string trang_thai
        timestamps ngay_tao_cap_nhat
    }

    GHE_THEO_SUAT {
        bigint id PK
        bigint suat_id FK
        bigint ghe_ngoi_id FK
        decimal gia_ve
        string trang_thai
        datetime giu_den_luc
        timestamps ngay_tao_cap_nhat
    }

    DON_HANG {
        bigint id PK
        bigint nguoi_dung_id FK
        string ma_don_hang UK
        string trang_thai
        decimal tam_tinh
        decimal tien_giam
        decimal tong_tien
        datetime het_han_luc
        datetime dat_luc
        timestamps ngay_tao_cap_nhat
    }

    VE {
        bigint id PK
        bigint don_hang_id FK
        bigint ghe_theo_suat_id FK,UK
        string ma_ve UK
        string trang_thai
        decimal gia_ban
        datetime phat_hanh_luc
        timestamps ngay_tao_cap_nhat
    }

    THANH_TOAN {
        bigint id PK
        bigint don_hang_id FK
        string nha_cung_cap
        string ma_giao_dich
        decimal so_tien
        string trang_thai
        datetime thanh_toan_luc
        json du_lieu_phan_hoi
        timestamps ngay_tao_cap_nhat
    }
```

## Ý nghĩa các bảng

- `DIA_DIEM` là rạp hoặc địa điểm tổ chức; mỗi địa điểm có nhiều `PHONG_CHIEU`.
- `GHE_NGOI` là sơ đồ ghế cố định của một phòng. `hang_ghe` và `so_ghe` tạo mã dễ đọc như A01, A02.
- `NOI_DUNG` lưu thông tin chung. `loai_noi_dung` nhận `phim` hoặc `su_kien`; chi tiết riêng nằm trong `PHIM` hoặc `SU_KIEN`.
- `SUAT` là một lần chiếu phim hoặc một lần diễn ra sự kiện tại một phòng và thời điểm cụ thể.
- `GHE_THEO_SUAT` lưu giá và tình trạng của từng ghế ở từng suất. Trạng thái có thể là `trong`, `dang_giu`, `da_ban`, `tam_khoa`.
- `DON_HANG` lưu lần đặt của khách; `VE` là vé cụ thể được tạo từ ghế đã bán.
- `THANH_TOAN` lưu từng giao dịch. Một đơn có thể có nhiều bản ghi thanh toán nếu khách thử lại.

## Ràng buộc cần giữ

- `GHE_NGOI`: không trùng (`phong_chieu_id`, `hang_ghe`, `so_ghe`).
- `GHE_THEO_SUAT`: không trùng (`suat_id`, `ghe_ngoi_id`); giá vé không âm.
- `VE`: `ghe_theo_suat_id` là duy nhất để một ghế trong một suất không bị bán hai lần; `ma_ve` cũng là duy nhất.
- `SUAT`: `ket_thuc_luc` phải sau `bat_dau_luc`; không xếp hai suất bị trùng giờ trong cùng phòng.
- Khi khách chọn ghế, cập nhật `GHE_THEO_SUAT` trong transaction và đặt `giu_den_luc`. Thanh toán thành công thì chuyển sang `da_ban`; hết hạn thì mở ghế lại.
- `loai_noi_dung` chỉ nhận `phim` hoặc `su_kien`. Mỗi `NOI_DUNG` phải có đúng một bản ghi chi tiết tương ứng trong `PHIM` hoặc `SU_KIEN`.
- Dùng `DECIMAL(12,2)` cho tiền, không dùng `REAL`/`FLOAT`.

## Ghi chú Laravel

Tên bảng tiếng Việt không dấu vẫn dùng được, nhưng không khớp tên bảng mặc định Laravel. Trong từng model cần khai báo tên bảng, ví dụ `protected $table = 'nguoi_dung';`. Có thể dùng tên bảng số nhiều như trong sơ đồ nếu muốn; khi đó model cần khai báo đúng tên số nhiều ấy. Các khóa ngoại cũng nên đặt thủ công theo tên trong sơ đồ ở migration.

Mô hình hiện giả định mọi vé đều gắn với ghế đánh số. Nếu sự kiện có vé đứng hoặc vé theo khu vực, nên bổ sung bảng loại vé và sức chứa theo suất, thay vì tạo ghế giả.
