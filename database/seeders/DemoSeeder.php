<?php

namespace Database\Seeders;

use App\Models\{User, DiaDiem, PhongChieu, GheNgoi, NoiDung, Phim, SuKien, Suat, GheTheoSuat, DonHang, Ve, ThanhToan};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('DemoSeeder chỉ được chạy trong môi trường local/testing.');
        }

        DB::transaction(function () {
            // 1. Người dùng mẫu (Admin & các User)
            User::firstOrCreate(['email' => 'admin@example.com'], [
                'ho_ten' => 'Quản trị viên', 'mat_khau' => 'Admin@123456', 'vai_tro' => 'admin',
                'so_dien_thoai' => '0900000001', 'email_verified_at' => now(),
            ]);
            $user = User::firstOrCreate(['email' => 'user@example.com'], [
                'ho_ten' => 'Người dùng mẫu', 'mat_khau' => 'User@123456', 'vai_tro' => 'user',
                'so_dien_thoai' => '0900000002',
            ]);
            User::firstOrCreate(['email' => 'minhanh@example.com'], [
                'ho_ten' => 'Nguyễn Minh Anh', 'mat_khau' => 'User@123456', 'vai_tro' => 'user',
                'so_dien_thoai' => '0901234567',
            ]);
            User::firstOrCreate(['email' => 'hoangnam@example.com'], [
                'ho_ten' => 'Trần Hoàng Nam', 'mat_khau' => 'User@123456', 'vai_tro' => 'user',
                'so_dien_thoai' => '0902345678',
            ]);

            // 2. Hệ thống Rạp / Địa điểm (4 cụm rạp lớn)
            $venue1 = DiaDiem::firstOrCreate(['ten_dia_diem' => 'CineViet Central'], [
                'dia_chi' => '123 Nguyễn Huệ, Quận 1', 'tinh_thanh' => 'TP. Hồ Chí Minh',
                'so_dien_thoai' => '02838220001', 'trang_thai' => 'hoat_dong',
            ]);
            $venue2 = DiaDiem::firstOrCreate(['ten_dia_diem' => 'CineViet Landmark 81'], [
                'dia_chi' => '720A Điện Biên Phủ, Bình Thạnh', 'tinh_thanh' => 'TP. Hồ Chí Minh',
                'so_dien_thoai' => '02838220002', 'trang_thai' => 'hoat_dong',
            ]);
            $venue3 = DiaDiem::firstOrCreate(['ten_dia_diem' => 'CineViet Thảo Điền Mall'], [
                'dia_chi' => '12 Quốc Hương, Thảo Điền, TP. Thủ Đức', 'tinh_thanh' => 'TP. Hồ Chí Minh',
                'so_dien_thoai' => '02838220003', 'trang_thai' => 'hoat_dong',
            ]);
            $venue4 = DiaDiem::firstOrCreate(['ten_dia_diem' => 'CineViet Crescent Mall Q7'], [
                'dia_chi' => '101 Tôn Dật Tiên, Phú Mỹ Hưng, Quận 7', 'tinh_thanh' => 'TP. Hồ Chí Minh',
                'so_dien_thoai' => '02838220004', 'trang_thai' => 'hoat_dong',
            ]);

            // Tạo phòng chiếu & ghế cho từng rạp
            $createRooms = function (DiaDiem $venue, array $configs) {
                return collect($configs)->map(function ($cfg, $name) use ($venue) {
                    $room = PhongChieu::firstOrCreate(
                        ['dia_diem_id' => $venue->id, 'ten_phong' => $name],
                        ['loai_phong' => $cfg['type'], 'trang_thai' => 'hoat_dong']
                    );
                    foreach ($cfg['rows'] as $idx => $row) {
                        $isVip = $idx >= count($cfg['rows']) - $cfg['vip_rows'];
                        for ($number = 1; $number <= $cfg['cols']; $number++) {
                            GheNgoi::firstOrCreate(
                                ['phong_chieu_id' => $room->id, 'hang_ghe' => $row, 'so_ghe' => $number],
                                ['loai_ghe' => $isVip ? 'vip' : 'thuong', 'dang_su_dung' => true]
                            );
                        }
                    }
                    return $room;
                })->values();
            };

            $rooms1 = $createRooms($venue1, [
                'Phòng chiếu 01' => ['type' => '2D', 'rows' => ['A', 'B', 'C', 'D'], 'cols' => 8, 'vip_rows' => 1],
                'Phòng chiếu 02' => ['type' => '2D', 'rows' => ['A', 'B', 'C', 'D'], 'cols' => 8, 'vip_rows' => 1],
                'Khán phòng sự kiện' => ['type' => 'san_khau', 'rows' => ['A', 'B', 'C', 'D'], 'cols' => 10, 'vip_rows' => 2],
            ]);
            $rooms2 = $createRooms($venue2, [
                'IMAX Laser 01' => ['type' => 'IMAX', 'rows' => ['A', 'B', 'C', 'D', 'E'], 'cols' => 10, 'vip_rows' => 2],
                'IMAX Laser 02' => ['type' => 'IMAX', 'rows' => ['A', 'B', 'C', 'D'], 'cols' => 10, 'vip_rows' => 1],
                'Stage Hall 81' => ['type' => 'san_khau', 'rows' => ['A', 'B', 'C', 'D'], 'cols' => 10, 'vip_rows' => 1],
            ]);
            $rooms3 = $createRooms($venue3, [
                'Cinema 3D 01' => ['type' => '3D', 'rows' => ['A', 'B', 'C', 'D'], 'cols' => 8, 'vip_rows' => 1],
                'Cinema 2D 02' => ['type' => '2D', 'rows' => ['A', 'B', 'C', 'D'], 'cols' => 8, 'vip_rows' => 1],
            ]);
            $rooms4 = $createRooms($venue4, [
                'Gold Class 01' => ['type' => '2D', 'rows' => ['A', 'B', 'C', 'D'], 'cols' => 8, 'vip_rows' => 2],
                'Crescent Stage' => ['type' => 'san_khau', 'rows' => ['A', 'B', 'C', 'D'], 'cols' => 10, 'vip_rows' => 2],
            ]);

            $filmRooms = [$rooms1[0], $rooms1[1], $rooms2[0], $rooms2[1], $rooms3[0], $rooms3[1], $rooms4[0]];
            $eventRooms = [$rooms1[2], $rooms2[2], $rooms4[1]];

            // 3. Danh sách ĐÚNG 36 Bộ phim & Sự kiện lấy trực tiếp từ MoMo Cinema (https://www.momo.vn/cinema)
            // [tieu_de, loai, mo_ta, thoi_luong, gia_ve, gioi_han_tuoi, the_loai, ngon_ngu_hoac_to_chuc, file_anh_png, la_sap_chieu]
            $items = [
                // --- 24 Nội dung Đang chiếu trên MoMo Cinema ---
                [
                    'Trại Giam Hạnh Phúc', 'phim',
                    'Phúc (Tuấn Trần) vốn là một thầy giáo hiền lành nhưng phải ngồi tù sau biến cố bảo vệ em gái. Trong trại giam, lòng tốt của Phúc dần cảm hóa các bạn tù và cả người quản giáo nguyên tắc.',
                    123, 95000, 'T16', 'Hài, Chính Kịch', 'Tiếng Việt',
                    'images/trai-giam-hanh-phuc.png', false,
                ],
                [
                    'Ngày Con Còn Mẹ', 'phim',
                    'Khai thác góc nhìn chân thật về tình mẫu tử giữa bà Tím và Mây, hành trình giằng xé nhưng ấm áp nhắc nhở chúng ta học cách tha thứ và yêu thương khi mọi thứ vẫn còn kịp.',
                    107, 90000, 'T16', 'Gia Đình, Tâm Lý', 'Tiếng Việt',
                    'images/ngay-con-con-me.png', false,
                ],
                [
                    'Đấu Trường Sinh Tử: Bình Minh Ngày Chiêu Quân', 'phim',
                    'Đưa khán giả trở lại Panem 24 năm trước sự kiện Đấu Trường Sinh Tử đầu tiên, bắt đầu vào buổi sáng diễn ra Lễ Chiêu Quân lần thứ 50 — Huyết trường Tứ phân lần thứ hai.',
                    158, 120000, 'T16', 'Khoa Học Viễn Tưởng, Hành Động', 'Tiếng Anh · Phụ đề Việt',
                    'images/dau-truong-sinh-tu-binh-minh-ngay-chieu-quan.png', false,
                ],
                [
                    'Mẹ Mìn', 'phim',
                    'Mẹ Mìn — một nhân vật bí ẩn có khả năng ban phát điều ước cho mọi người, nhưng bất kỳ điều ước nào cũng đi kèm cái giá và điều kiện kinh hoàng của nó.',
                    125, 100000, 'T18', 'Kinh Dị, Bí Ẩn, Gay Cấn', 'Tiếng Việt',
                    'images/me-min.png', false,
                ],
                [
                    'Ma Mút Con Vượt Kỷ Băng Hà', 'phim',
                    'Chú ma mút con Fluffy tỉnh giấc sau giấc ngủ hàng nghìn năm giữa Nam Cực băng giá và bắt đầu hành trình vượt đại dương tìm mẹ cùng chú hải âu cổ rụt Puffin.',
                    90, 85000, 'P', 'Hoạt Hình, Phiêu Lưu, Gia Đình', 'Lồng tiếng Việt',
                    'images/ma-mut-con-vuot-ky-bang-ha.png', false,
                ],
                [
                    'Dune: Hành Tinh Cát - Phần Hai (Chiếu lại)', 'phim',
                    'Hành trình thần thoại của Paul Atreides khi đoàn kết với Chani và người Fremen trên con đường trả thù những kẻ âm mưu hủy hoại gia tộc Atreides.',
                    166, 125000, 'T16', 'Khoa Học Viễn Tưởng, Phiêu Lưu', 'IMAX · Phụ đề Việt',
                    'images/dune-hanh-tinh-cat-phan-hai.png', false,
                ],
                [
                    'Thòng Lọng Ma: Quỷ Cái Đòi Mạng', 'phim',
                    'Một nghi lễ trừ tà bị cấm được phát trực tiếp tại Indonesia vô tình đánh thức Kuntilanak — linh hồn nữ quỷ báo thù kéo theo hàng loạt vụ quỷ ám kinh hoàng.',
                    120, 95000, 'T18', 'Kinh Dị, Gay Cấn, Bí Ẩn', 'Phụ đề Việt',
                    'images/thong-long-ma-quy-cai-doi-mang.png', false,
                ],
                [
                    'Phim Điện Ảnh Thám Tử Siêu Mông: Ngôi Sao & Mặt Trăng', 'phim',
                    'Thám tử Butt hợp tác cùng Đạo tặc U đột nhập Đảo Bụi Sao nhằm lấy lại Nguyệt Quang Thạch và ngăn chặn dự án bí ẩn DARK AGE của Học Viện Đạo Tặc.',
                    77, 85000, 'P', 'Hoạt Hình, Trinh Thám, Hài', 'Lồng tiếng Việt',
                    'images/tham-tu-sieu-mong-ngoi-sao-mat-trang.png', false,
                ],
                [
                    'BTS WORLD TOUR ARIRANG IN KAOHSIUNG: LIVE VIEWING', 'su_kien',
                    'BTS WORLD TOUR ARIRANG chính thức đổ bộ màn ảnh rộng với những màn trình diễn bùng nổ tại Cao Hùng, mang đến trải nghiệm Live Viewing hoành tráng dành cho ARMY!',
                    180, 350000, 'T13', 'Âm Nhạc · Concert Live Viewing', 'HYBE & MoMo Cinema',
                    'images/bts-world-tour-arirang-kaohsiung.png', false,
                ],
                [
                    'Kiến Lé Phiêu Lưu Ké', 'phim',
                    'Giữa cơn hạn khốc liệt đe dọa cả đồng cỏ, hai anh chàng kiến vụng về Arnie và Barney bất ngờ lao vào hành trình giải cứu tổ kiến và vạch trần âm mưu trục lợi nguồn nước.',
                    77, 85000, 'P', 'Hài, Hoạt Hình, Phiêu Lưu', 'Lồng tiếng Việt',
                    'images/kien-le-phieu-luu-ke.png', false,
                ],
                [
                    'Kẻ Hồi Sinh', 'phim',
                    'Seok Hwan bất ngờ sống lại sau 72 giờ bị sát hại và phát hiện mình sở hữu năng lực hồi sinh vô hạn, nhưng món quà này sớm đưa anh vào tầm ngắm của một tổ chức bí ẩn.',
                    102, 95000, 'T16', 'Hài, Giả Tượng, Hành Động', 'Tiếng Hàn · Phụ đề Việt',
                    'images/ke-hoi-sinh.png', false,
                ],
                [
                    'Ebenezer Phép Màu Đêm Giáng Sinh', 'phim',
                    'Johnny Depp hóa thân thành Ebenezer Scrooge trong phiên bản điện ảnh giả tưởng mới lạ về phép màu đêm Giáng Sinh và những bí mật chưa từng được kể.',
                    115, 105000, 'T13', 'Hài, Giả Tượng, Gia Đình', 'Tiếng Anh · Phụ đề Việt',
                    'images/ebenezer-phep-mau-dem-giang-sinh.png', false,
                ],
                [
                    'Khi Thần Chết Đổ Chuông', 'phim',
                    'Danto trở về quê nhà Belitung sau khi ông mình bị sát hại bởi Panebok — linh hồn không đầu gieo rắc nỗi kinh hoàng mỗi khi chiếc chuông thiêng phong ấn bị đánh cắp.',
                    86, 90000, 'T18', 'Kinh Dị, Tâm Linh', 'Phụ đề Việt',
                    'images/khi-than-chet-do-chuong.png', false,
                ],
                [
                    'Kamen Rider Zeztz & Siêu Cảnh sát Vũ trụ Gavan Infinity', 'phim',
                    'Trụ sở Cảnh sát Quốc gia bị đánh bom, Zeztz và Nox phải đối đầu Kamen Rider Mugen trong khi các Gavan từ khắp đa vũ trụ hội tụ mở màn trận chiến sinh tử.',
                    95, 90000, 'P', 'Khoa Học Viễn Tưởng, Hành Động', 'Tiếng Nhật · Phụ đề Việt',
                    'images/kamen-rider-zeztz-gavan-infinity.png', false,
                ],
                [
                    'Bí Kíp Luyện Cướp', 'phim',
                    'Một băng cướp ngân hàng am hiểu mạng xã hội biến những phi vụ táo tợn thành màn trình diễn gây sốt, kéo họ vào cuộc đấu trí nghẹt thở với đặc vụ FBI kỳ cựu.',
                    122, 105000, 'T18', 'Gay Cấn, Hình Sự, Hành Động', 'Tiếng Anh · Phụ đề Việt',
                    'images/bi-kip-luyen-cuop.png', false,
                ],
                [
                    'Ramayana: Khởi Nguồn Của Một Huyền Thoại', 'phim',
                    'Sử thi hoành tráng xoay quanh chuyện tình giữa Rama và Sita, 14 năm lưu đày trong rừng sâu và cuộc đại chiến giải cứu Sita khỏi Quỷ vương Ravana tại đảo quốc Lanka.',
                    195, 120000, 'T13', 'Giả Tượng, Phiêu Lưu, Hành Động', 'IMAX · Phụ đề Việt',
                    'images/ramayana-khoi-nguon-huyen-thoai.png', false,
                ],
                [
                    'Chàng Mèo Mang Mũ', 'phim',
                    'Chú Mèo Ta hài hước nhận nhiệm vụ từ Viện Tưởng Tượng I.I.I.I. để giúp hai anh em Gabby và Sebastian tìm lại niềm vui trong hành trình kỳ ảo có một không hai.',
                    107, 90000, 'P', 'Hài, Hoạt Hình, Gia Đình', 'Lồng tiếng Việt',
                    'images/chang-meo-mang-mu.png', false,
                ],
                [
                    'Bò Sữa Bay', 'phim',
                    'Thập niên 90, một trang trại bò sữa nghiên cứu công thức sữa độc quyền giúp trẻ hóa. Nhóm công nhân quyết định đánh cắp công thức ấy để đổi đời nhưng rơi vào lưới tình.',
                    108, 95000, 'T18', 'Chính Kịch, Hài Đen', 'Tiếng Việt',
                    'images/bo-sua-bay.png', false,
                ],
                [
                    'Godzilla Trừ Không', 'phim',
                    'Nhật Bản hậu chiến vừa gượng dậy từ con số 0 thì sự xuất hiện của quái thú Godzilla khổng lồ một lần nữa kéo đất nước xuống vực thẳm tuyệt vọng.',
                    135, 115000, 'T16', 'Kinh Dị, Khoa Học Viễn Tưởng, Hành Động', 'IMAX · Phụ đề Việt',
                    'images/godzilla-tru-khong.png', false,
                ],
                [
                    'Chị Chị Em Em 3', 'phim',
                    'Giữa xã hội Bắc Bộ cũ đầy lễ giáo, tiểu thư Thị Mầu bước vào mạng nhện tình cảm rối ren với những bí mật, toan tính và lựa chọn thách thức mọi khuôn phép.',
                    122, 100000, 'T18', 'Chính Kịch, Tâm Lý', 'Tiếng Việt',
                    'images/chi-chi-em-em-3.png', false,
                ],
                [
                    'Clayface', 'phim',
                    'Hành trình sa ngã kinh hoàng của một diễn viên triển vọng tại Hollywood khi anh dần biến đổi thành quái vật đất sét bị thôi thúc bởi thù hận và tham vọng khoa học.',
                    109, 105000, 'T18', 'Kinh Dị, Khoa Học Viễn Tưởng, Gay Cấn', 'Tiếng Anh · Phụ đề Việt',
                    'images/clayface.png', false,
                ],
                [
                    'Quỷ Ăn Tạng 4: Hổ Tinh', 'phim',
                    'Yak và Trung sĩ Paphan tiến sâu vào vùng hiểm địa Khao Khwang truy tìm người mất tích và phải tử chiến với Hổ tinh ăn thịt người có khả năng biến hóa khôn lường.',
                    111, 100000, 'T18', 'Kinh Dị, Hành Động, Gay Cấn', 'Tiếng Thái · Phụ đề Việt',
                    'images/quy-an-tang-4-ho-tinh.png', false,
                ],
                [
                    'Trại Buôn Người', 'phim',
                    'Vì cứu em gái sa bẫy buôn người vùng biên giới, Ny cùng bạn thân Báu thâm nhập sào huyệt lừa đảo và phối hợp với các trinh sát nằm vùng lập kế hoạch đào thoát.',
                    135, 100000, 'T18', 'Hành Động, Gay Cấn', 'Tiếng Việt',
                    'images/trai-buon-nguoi.png', false,
                ],
                [
                    'Dune: Hành Tinh Cát (Chiếu lại)', 'phim',
                    'Chàng trai trẻ tài năng Paul Atreides phải du hành đến hành tinh sa mạc Arrakis nguy hiểm nhất vũ trụ để bảo vệ tương lai của gia tộc trước những thế lực tàn ác.',
                    155, 115000, 'T13', 'Khoa Học Viễn Tưởng, Phiêu Lưu', 'IMAX · Phụ đề Việt',
                    'images/dune-hanh-tinh-cat.png', false,
                ],

                // --- 12 Phim Sắp Chiếu trên MoMo Cinema ---
                [
                    'EM', 'phim',
                    'Dự án điện ảnh tâm lý - gia đình mới nhất của đạo diễn Trấn Thành sau chuỗi phim trăm tỷ Bố Già, Nhà Bà Nữ, MAI và Bộ Tứ Báo Thủ.',
                    134, 110000, 'T16', 'Tâm Lý, Tình Cảm, Gia Đình', 'Tiếng Việt',
                    'images/em.png', true,
                ],
                [
                    'Thám Tử Kiên 2: Lời Nguyền Hoàng Kim', 'phim',
                    'Thám tử Kiên trở lại với vụ án kỳ bí liên quan đến kho báu và lời nguyền hoàng kim cổ xưa ẩn giấu sau những cái chết bất thường nơi dinh thự quyền quý.',
                    132, 110000, 'T16', 'Trinh Thám, Cổ Trang, Bí Ẩn', 'Tiếng Việt',
                    'images/tham-tu-kien-2-loi-nguyen-hoang-kim.png', true,
                ],
                [
                    'Dune: Hành Tinh Cát - Phần Ba', 'phim',
                    'Tiếp nối câu chuyện khi Paul Atreides trở thành Hoàng đế, đối mặt với hậu quả của cuộc Thánh chiến liên thiên hà và âm mưu phản loạn định đoạt tương lai nhân loại.',
                    160, 125000, 'T13', 'Khoa Học Viễn Tưởng, Phiêu Lưu', 'IMAX · Phụ đề Việt',
                    'images/dune-hanh-tinh-cat-phan-ba.png', true,
                ],
                [
                    'Avengers: Ngày Tận Thế', 'phim',
                    'Biệt đội Avengers, Wakanda, Fantastic Four, Thunderbolts và X-Men cùng hợp lực trong cuộc chiến đa vũ trụ vĩ đại nhất chống lại hiểm họa từ Doctor Doom.',
                    150, 130000, 'T13', 'Khoa Học Viễn Tưởng, Hành Động', 'IMAX 3D · Phụ đề Việt',
                    'images/avengers-ngay-tan-the.png', true,
                ],
                [
                    'Con Mắt Thứ Ba', 'phim',
                    'Một kẻ cuồng tín sau cơn đau đầu dữ dội phát hiện con mắt dọc trên trán, mở ra sợi chỉ đỏ kết nối hàng loạt vụ sát nhân bí ẩn và những bóng ma từ quá khứ.',
                    112, 95000, 'T18', 'Kinh Dị, Tâm Linh', 'Tiếng Việt',
                    'images/con-mat-thu-ba.png', true,
                ],
                [
                    'Chuyến Đi Nhớ Đời', 'phim',
                    'Từ chuyến đi biển định mệnh, hai cha con Hoàng và An bị bão cuốn dạt vào hoang đảo, bắt đầu hành trình sinh tồn dở khóc dở cười để tìm đường về nhà và tìm lại nhau.',
                    108, 90000, 'P', 'Gia Đình, Hài, Phiêu Lưu', 'Tiếng Việt',
                    'images/chuyen-di-nho-doi.png', true,
                ],
                [
                    'Vùng Trời Lửa', 'phim',
                    'Bộ phim điện ảnh lịch sử - không quân tái hiện giai thoại hào hùng về đời sống phi công quân sự Việt Nam và sự kiện chấn động tháng 4/1975.',
                    128, 105000, 'T13', 'Lịch Sử, Hành Động, Chiến Tranh', 'Tiếng Việt',
                    'images/vung-troi-lua.png', true,
                ],
                [
                    'Bắt Cá Ba Tay', 'phim',
                    'Cánh cửa mở ra, trước mắt nàng là ba chàng trai: một người lịch lãm trưởng thành, một người dịu dàng ấm áp và một chàng trai trẻ cá tính. Ai sẽ là chân ái?',
                    106, 90000, 'T13', 'Hài, Lãng Mạn', 'Tiếng Việt',
                    'images/bat-ca-ba-tay.png', true,
                ],
                [
                    'Gặp Gỡ Thông Gia: Dâu Mới Trình Làng', 'phim',
                    'Henry đưa bạn gái — một chuyên gia đàm phán con tin của FBI — về ra mắt gia đình Focker, kéo theo hàng loạt tình huống dở khóc dở cười khi kế hoạch cầu hôn bị lộ.',
                    105, 95000, 'T13', 'Hài, Lãng Mạn, Gia Đình', 'Tiếng Anh · Phụ đề Việt',
                    'images/gap-go-thong-gia-dau-moi-trinh-lang.png', true,
                ],
                [
                    'Phim Angry Birds 3', 'phim',
                    'Dịp lễ này, chú chim đỏ Red phải đối đầu với thử thách nhọc nhằn nhất cuộc đời: vừa học cách làm một người cha mẫu mực, vừa giải cứu thế giới khỏi kẻ thù mới!',
                    96, 85000, 'P', 'Hài, Hoạt Hình, Gia Đình', 'Lồng tiếng Việt',
                    'images/phim-angry-birds-3.png', true,
                ],
                [
                    'Người Mẹ Xấu', 'phim',
                    'Một cuộc chạy trốn trong mưa, một người mẹ không hoàn hảo và một người con không thể quay đầu trong bộ phim hành động - tâm lý kịch tính của đạo diễn Hoàng Tuấn Cường.',
                    116, 95000, 'T16', 'Hành Động, Tâm Lý, Gia Đình', 'Tiếng Việt',
                    'images/nguoi-me-xau.png', true,
                ],
                [
                    'Jumanji: Thế Giới Đảo Chiều', 'phim',
                    'Trò chơi Jumanji bất ngờ thoát khỏi chiếc máy chơi game cũ, giải phóng đàn thú rừng rậm và những hiểm họa khôn lường xâm chiếm trực tiếp thế giới thực!',
                    124, 110000, 'T13', 'Hài, Giả Tượng, Phiêu Lưu', 'Phụ đề / Lồng tiếng',
                    'images/jumanji-the-gioi-dao-chieu.png', true,
                ],
            ];

            $hasGenreColumn = Schema::hasColumn('phim', 'the_loai');

            $timeSlots = [
                [9, 0],
                [12, 30],
                [16, 0],
                [19, 30],
                [22, 45],
            ];

            foreach ($items as $i => [$title, $type, $description, $duration, $price, $age, $genre, $langOrOrg, $posterPath, $isUpcoming]) {
                $content = NoiDung::updateOrCreate(
                    ['tieu_de' => $title, 'loai_noi_dung' => $type],
                    [
                        'mo_ta' => $description,
                        'anh_bia' => $posterPath,
                        'trang_thai' => 'cong_bo',
                    ]
                );

                if ($type === 'phim') {
                    $phimData = [
                        'thoi_luong_phut' => $duration,
                        'gioi_han_tuoi' => $age,
                        'ngon_ngu' => $langOrOrg,
                        'ngay_khoi_chieu' => $isUpcoming ? today()->addDays(($i % 10) + 3) : today()->subDays($i % 7),
                    ];
                    if ($hasGenreColumn) {
                        $phimData['the_loai'] = $genre;
                    }
                    Phim::updateOrCreate(['noi_dung_id' => $content->id], $phimData);
                } else {
                    SuKien::updateOrCreate(
                        ['noi_dung_id' => $content->id],
                        [
                            'don_vi_to_chuc' => $langOrOrg ?? 'MoMo Cinema',
                            'the_loai' => $genre,
                            'gioi_han_tuoi' => $age,
                        ]
                    );
                }

                // Tạo suất chiếu mở bán không trùng phòng + giờ bắt đầu
                $roomPool = $type === 'phim' ? $filmRooms : $eventRooms;
                $room = $roomPool[$i % count($roomPool)];
                $round = intdiv($i, count($roomPool));
                $slot = $timeSlots[$round % count($timeSlots)];
                $dayOffset = $isUpcoming ? (($i % 10) + 4) : (intdiv($round, count($timeSlots)) + 1);

                $start = now()->addDays($dayOffset)->setTime($slot[0], $slot[1]);
                $session = Suat::where('noi_dung_id', $content->id)
                    ->where('phong_chieu_id', $room->id)
                    ->first();

                if (! $session) {
                    while (Suat::where('phong_chieu_id', $room->id)->where('bat_dau_luc', $start)->exists()) {
                        $start = $start->copy()->addDay();
                    }
                    $session = Suat::create([
                        'noi_dung_id' => $content->id,
                        'phong_chieu_id' => $room->id,
                        'bat_dau_luc' => $start,
                        'ket_thuc_luc' => $start->copy()->addMinutes($duration),
                        'trang_thai' => 'mo_ban',
                    ]);
                }

                $room->loadMissing('gheNgois');
                foreach ($room->gheNgois as $seat) {
                    GheTheoSuat::firstOrCreate(
                        ['suat_id' => $session->id, 'ghe_ngoi_id' => $seat->id],
                        [
                            'gia_ve' => $price + ($seat->loai_ghe === 'vip' ? 30000 : 0),
                            'trang_thai' => 'trong',
                        ]
                    );
                }

                // Đơn hàng mẫu đầu tiên cho user@example.com
                if ($i === 0 && ! DonHang::where('ma_don_hang', 'DEMO-0001')->exists()) {
                    $seat = $session->gheTheoSuats()->where('trang_thai', 'trong')->whereDoesntHave('ve')->lockForUpdate()->first();
                    if (! $seat) {
                        continue;
                    }
                    $order = DonHang::create([
                        'nguoi_dung_id' => $user->id,
                        'ma_don_hang' => 'DEMO-0001',
                        'trang_thai' => 'da_thanh_toan',
                        'tam_tinh' => $seat->gia_ve,
                        'tien_giam' => 0,
                        'tong_tien' => $seat->gia_ve,
                        'dat_luc' => now(),
                    ]);
                    $seat->update(['trang_thai' => 'da_ban']);
                    Ve::create([
                        'don_hang_id' => $order->id,
                        'ghe_theo_suat_id' => $seat->id,
                        'ma_ve' => 'DEMO-VE-0001',
                        'trang_thai' => 'hieu_luc',
                        'gia_ban' => $seat->gia_ve,
                        'phat_hanh_luc' => now(),
                    ]);
                    ThanhToan::create([
                        'don_hang_id' => $order->id,
                        'nha_cung_cap' => 'momo',
                        'ma_giao_dich' => 'DEMO-TT-0001',
                        'so_tien' => $seat->gia_ve,
                        'trang_thai' => 'thanh_cong',
                        'thanh_toan_luc' => now(),
                        'du_lieu_phan_hoi' => ['du_lieu_mau' => true],
                    ]);
                }
            }
        });
    }
}
