<?php

namespace Tests\Feature;

use App\Models\{NoiDung, User, Ve, GheTheoSuat};
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseAndHomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_populates_all_tables_and_is_repeatable(): void
    {
        $this->seed();
        $tables = ['nguoi_dung', 'dia_diem', 'phong_chieu', 'ghe_ngoi', 'noi_dung', 'phim', 'su_kien', 'suat', 'ghe_theo_suat', 'don_hang', 've', 'thanh_toan'];
        $counts = [];
        foreach ($tables as $table) {
            $counts[$table] = DB::table($table)->count();
            $this->assertGreaterThan(0, $counts[$table], $table);
        }
        $this->seed();
        foreach ($tables as $table) {
            $this->assertDatabaseCount($table, $counts[$table]);
        }
        $ticket = Ve::with('gheTheoSuat.suat.noiDung', 'donHang.thanhToans')->firstOrFail();
        $this->assertSame('da_ban', $ticket->gheTheoSuat->trang_thai);
        $this->assertSame('phim', $ticket->gheTheoSuat->suat->noiDung->loai_noi_dung);
        $this->assertSame('thanh_cong', $ticket->donHang->thanhToans->first()->trang_thai);
    }

    public function test_home_filters_published_content_and_has_empty_state(): void
    {
        $this->seed();
        NoiDung::create(['tieu_de' => 'Nội dung chưa công bố', 'loai_noi_dung' => 'phim', 'trang_thai' => 'nhap']);
        $this->get(route('home'))->assertOk()->assertSee('Chuyến đi mùa hạ')->assertDontSee('Nội dung chưa công bố');
        $this->get(route('home', ['loai' => 'su_kien']))->assertOk()->assertSee('Đêm nhạc dưới ánh sao')->assertDontSee('Chuyến đi mùa hạ');
        $this->get(route('home', ['q' => 'khong-khop']))->assertOk()->assertSee('Chưa tìm thấy nội dung phù hợp');
        $this->get(route('register'))->assertOk();
        $this->get(route('login'))->assertOk();
    }

    public function test_user_with_orders_cannot_be_deleted_and_admin_search_works(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@example.com')->firstOrFail();
        $user = User::where('email', 'user@example.com')->firstOrFail();
        $this->actingAs($admin)->delete(route('admin.users.destroy', $user))->assertSessionHasErrors('user');
        $this->assertModelExists($user);
        $this->get(route('admin.users.index', ['q' => 'user@example.com', 'vai_tro' => 'user']))
            ->assertOk()->assertSee('user@example.com')->assertDontSee('admin@example.com');
    }

    public function test_database_rejects_duplicate_seat_in_a_session(): void
    {
        $this->seed();
        $seat = GheTheoSuat::firstOrFail();
        $this->expectException(QueryException::class);
        GheTheoSuat::create(['suat_id' => $seat->suat_id, 'ghe_ngoi_id' => $seat->ghe_ngoi_id, 'gia_ve' => 100000]);
    }

    public function test_database_rejects_two_tickets_for_the_same_seat(): void
    {
        $this->seed();
        $ticket = Ve::firstOrFail();
        $this->expectException(QueryException::class);
        Ve::create(['don_hang_id' => $ticket->don_hang_id, 'ghe_theo_suat_id' => $ticket->ghe_theo_suat_id,
            'ma_ve' => 'ANOTHER-CODE', 'gia_ban' => 90000, 'phat_hanh_luc' => now()]);
    }
}
