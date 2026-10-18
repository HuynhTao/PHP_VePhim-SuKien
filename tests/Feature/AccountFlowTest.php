<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_hashes_password_and_ignores_admin_role_injection(): void
    {
        $this->post(route('register.store'), [
            'ho_ten' => 'Khách mới', 'email' => 'NEW@example.com',
            'password' => 'SecurePass123', 'password_confirmation' => 'SecurePass123', 'vai_tro' => 'admin',
        ])->assertRedirect(route('home'));
        $user = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertSame('user', $user->vai_tro);
        $this->assertTrue(Hash::check('SecurePass123', $user->mat_khau));
        $this->assertAuthenticatedAs($user);
        $this->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_registration_rejects_duplicate_email_and_password_mismatch(): void
    {
        User::factory()->create(['email' => 'used@example.com']);
        $this->post(route('register.store'), [
            'ho_ten' => 'Khách mới', 'email' => 'used@example.com',
            'password' => 'SecurePass123', 'password_confirmation' => 'different',
        ])->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
        $this->assertDatabaseCount('nguoi_dung', 1);
    }

    public function test_login_uses_vietnamese_password_column_and_logout_clears_authentication(): void
    {
        $user = User::factory()->create(['email' => 'login@example.com', 'mat_khau' => 'LoginPass123']);
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->post(route('login.store'), ['email' => 'LOGIN@example.com', 'password' => 'LoginPass123', 'remember' => 1])->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
        $this->get(route('account'))->assertOk()->assertSee($user->ho_ten);
        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_admin_login_redirects_to_admin_area(): void
    {
        $admin = User::factory()->create(['vai_tro' => 'admin']);
        $this->post(route('login.store'), ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.users.index'));
    }

    public function test_guest_is_redirected_and_user_cannot_access_any_admin_action(): void
    {
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));
        $user = User::factory()->create();
        $this->actingAs($user);
        foreach (['index', 'create', 'show', 'edit'] as $action) {
            $this->get(route('admin.users.'.$action, in_array($action, ['show', 'edit']) ? $user : []))->assertForbidden();
        }
        $this->post(route('admin.users.store'), [])->assertForbidden();
        $this->put(route('admin.users.update', $user), [])->assertForbidden();
        $this->delete(route('admin.users.destroy', $user))->assertForbidden();
    }

    public function test_admin_can_create_read_update_and_delete_an_unused_account(): void
    {
        $admin = User::factory()->create(['vai_tro' => 'admin']);
        $this->actingAs($admin);
        $this->get(route('admin.users.create'))->assertOk();
        $this->post(route('admin.users.store'), [
            'ho_ten' => 'Tài khoản mới', 'email' => 'created@example.com', 'vai_tro' => 'user',
            'password' => 'CreatedPass123', 'password_confirmation' => 'CreatedPass123',
        ])->assertSessionHasNoErrors();
        $user = User::where('email', 'created@example.com')->firstOrFail();
        $this->get(route('admin.users.show', $user))->assertOk()->assertSee('Tài khoản mới');
        $this->get(route('admin.users.edit', $user))->assertOk();
        $this->put(route('admin.users.update', $user), [
            'ho_ten' => 'Tên đã đổi', 'email' => $user->email, 'vai_tro' => 'admin', 'password' => '',
        ])->assertRedirect(route('admin.users.show', $user));
        $this->assertSame('admin', $user->fresh()->vai_tro);
        $this->assertTrue(Hash::check('CreatedPass123', $user->fresh()->mat_khau));
        $this->put(route('admin.users.update', $user), [
            'ho_ten' => 'Tên đã đổi', 'email' => $user->email, 'vai_tro' => 'user',
            'password' => 'ChangedPass123', 'password_confirmation' => 'ChangedPass123',
        ])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('ChangedPass123', $user->fresh()->mat_khau));
        $this->delete(route('admin.users.destroy', $user))->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('nguoi_dung', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_or_demote_self(): void
    {
        $admin = User::factory()->create(['vai_tro' => 'admin']);
        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin))->assertSessionHasErrors('user');
        $this->put(route('admin.users.update', $admin), [
            'ho_ten' => $admin->ho_ten, 'email' => $admin->email, 'vai_tro' => 'user',
        ])->assertSessionHasErrors('vai_tro');
        $this->assertSame('admin', $admin->fresh()->vai_tro);
    }

    public function test_admin_cannot_assign_duplicate_email_or_invalid_role(): void
    {
        $admin = User::factory()->create(['vai_tro' => 'admin']);
        $user = User::factory()->create();
        $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'ho_ten' => $user->ho_ten, 'email' => $admin->email, 'vai_tro' => 'superadmin',
        ])->assertSessionHasErrors(['email', 'vai_tro']);
    }

    public function test_login_attempts_are_rate_limited(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->post(route('login.store'), ['email' => 'absent@example.com', 'password' => 'wrong']);
        }
        $this->post(route('login.store'), ['email' => 'absent@example.com', 'password' => 'wrong'])->assertStatus(429);
    }
}
