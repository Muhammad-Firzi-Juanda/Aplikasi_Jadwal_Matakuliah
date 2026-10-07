<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private const ADMIN_EMAIL = 'admin@siwalan.com';

    private const ADMIN_PASSWORD = 'admin123';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin(): User
    {
        return User::where('email', self::ADMIN_EMAIL)->firstOrFail();
    }

    private function adminPembantu(): User
    {
        return User::where('email', 'admin@apjad.com')->firstOrFail();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('APJAD');
        $response->assertSee('Universitas Maritim');
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => self::ADMIN_EMAIL,
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('show_error', true);
        $this->assertGuest();
    }

    public function test_login_succeeds_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => self::ADMIN_EMAIL,
            'password' => self::ADMIN_PASSWORD,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_ajax_succeeds_with_json_popup_response(): void
    {
        $response = $this->postJson('/login', [
            'email' => self::ADMIN_EMAIL,
            'password' => self::ADMIN_PASSWORD,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Login Berhasil',
            'redirect' => route('dashboard'),
        ]);
        $this->assertAuthenticated();
    }

    public function test_login_ajax_fails_with_json_response(): void
    {
        $response = $this->postJson('/login', [
            'email' => self::ADMIN_EMAIL,
            'password' => 'wrongpass',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Email atau Password Anda Salah',
        ]);
        $this->assertGuest();
    }

    public function test_manajemen_akun_displays_non_admin_users(): void
    {
        $response = $this->actingAs($this->admin())->get('/manajemen-akun');

        $response->assertStatus(200);
        $response->assertSee('Manajemen User');
        $response->assertSee('Revi Aedrian');
        $response->assertSee('Brian Darell');
        // Super Admin tidak muncul di tabel manajemen akun
        $response->assertDontSee('<td>Super Admin</td>', false);
    }

    public function test_dashboard_shows_sidebar_links_and_stats(): void
    {
        $response = $this->actingAs($this->admin())->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee(route('manajemen-akun'), false);
        $response->assertSee(route('jadwal-kuliah.index'), false);
        $response->assertSee(route('penjadwalan.index'), false);
    }

    public function test_create_user_successfully(): void
    {
        $response = $this->actingAs($this->admin())->post('/users', [
            'nama' => 'Dosen Baru',
            'email' => 'dosenbaru@gmail.com',
            'role' => 'Jurusan',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('flash_success', 'Akun berhasil dibuat!');

        $this->assertDatabaseHas('users', [
            'email' => 'dosenbaru@gmail.com',
            'nama' => 'Dosen Baru',
            'role' => 'Jurusan',
        ]);
    }

    public function test_create_user_with_role_prodi(): void
    {
        $response = $this->actingAs($this->admin())->post('/users', [
            'nama' => 'Kaprodi Baru',
            'email' => 'kaprodi@gmail.com',
            'role' => 'Prodi',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('flash_success', 'Akun berhasil dibuat!');

        $this->assertDatabaseHas('users', [
            'email' => 'kaprodi@gmail.com',
            'nama' => 'Kaprodi Baru',
            'role' => 'Prodi',
        ]);
    }

    public function test_super_admin_can_create_admin_account(): void
    {
        $response = $this->actingAs($this->admin())->post('/users', [
            'nama' => 'Admin Baru',
            'email' => 'adminbaru@gmail.com',
            'role' => UserRole::ADMIN,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('flash_success', 'Akun berhasil dibuat!');
        $this->assertDatabaseHas('users', [
            'email' => 'adminbaru@gmail.com',
            'role' => UserRole::ADMIN,
        ]);
    }

    public function test_cannot_create_super_admin_from_crud(): void
    {
        $response = $this->actingAs($this->admin())->post('/users', [
            'nama' => 'Hacker Super',
            'email' => 'hack@gmail.com',
            'role' => 'Super Admin',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'hack@gmail.com']);
    }

    public function test_update_user_successfully(): void
    {
        $target = User::where('email', 'randtian@gmail.com')->firstOrFail();

        $response = $this->actingAs($this->admin())->put("/users/{$target->id}", [
            'nama' => 'Revi Aedrian Updated',
            'email' => 'randtian@gmail.com',
            'role' => 'Fakultas',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('flash_success', 'Data akun berhasil diperbarui!');

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'nama' => 'Revi Aedrian Updated',
            'role' => 'Fakultas',
        ]);
    }

    public function test_prevent_self_deletion(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->delete("/users/{$admin->id}");

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('flash_error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_delete_other_user(): void
    {
        $target = User::where('email', 'bdarell@gmail.com')->firstOrFail();

        $response = $this->actingAs($this->admin())->delete("/users/{$target->id}");

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('flash_success', 'User berhasil dihapus!');
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_edit_profile_super_admin(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post('/profile', [
            'nama' => 'Admin Utama',
            'email' => 'adminutama@gmail.com',
            'password_lama' => self::ADMIN_PASSWORD,
            'password_baru' => 'newadminpassword123',
            'password_baru_confirmation' => 'newadminpassword123',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('flash_success', 'Profile berhasil diperbarui!');

        $admin->refresh();
        $this->assertEquals('Admin Utama', $admin->nama);
        $this->assertEquals('adminutama@gmail.com', $admin->email);
        $this->assertTrue(Hash::check('newadminpassword123', $admin->password));
    }

    public function test_change_password(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post('/change-password', [
            'password_lama' => self::ADMIN_PASSWORD,
            'password_baru' => 'password456',
            'password_baru_confirmation' => 'password456',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('flash_success', 'Password berhasil diubah!');

        $this->assertTrue(Hash::check('password456', $admin->fresh()->password));
    }

    public function test_logout(): void
    {
        $response = $this->actingAs($this->admin())->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_admin_role_redirects_to_manajemen_akun_on_login(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@apjad.com',
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('manajemen-akun'));
        $this->assertAuthenticated();
    }

    public function test_admin_can_access_manajemen_akun(): void
    {
        $adminAkun = $this->adminPembantu();
        $response = $this->actingAs($adminAkun)->get('/manajemen-akun');

        $response->assertStatus(200);
        $response->assertSee('Manajemen User');
        $response->assertSee('APJAD');
        // Admin tidak melihat link Dashboard, Jadwal Kuliah, Penjadwalan di sidebar
        $response->assertDontSee(route('dashboard'), false);
        $response->assertDontSee(route('jadwal-kuliah.index'), false);
        $response->assertDontSee(route('penjadwalan.index'), false);
    }

    public function test_admin_can_crud_account(): void
    {
        $adminAkun = $this->adminPembantu();

        // 1. Create account (e.g. Fakultas/Jurusan/Prodi)
        $response = $this->actingAs($adminAkun)->post('/users', [
            'nama' => 'Staff Baru',
            'email' => 'staffbaru@gmail.com',
            'role' => 'Jurusan',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHas('flash_success');
        $this->assertDatabaseHas('users', ['email' => 'staffbaru@gmail.com']);

        $createdUser = User::where('email', 'staffbaru@gmail.com')->firstOrFail();

        // 2. Update account
        $response = $this->actingAs($adminAkun)->put("/users/{$createdUser->id}", [
            'nama' => 'Staff Diperbarui',
            'email' => 'staffbaru@gmail.com',
            'role' => 'Prodi',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHas('flash_success');
        $this->assertDatabaseHas('users', [
            'id' => $createdUser->id,
            'nama' => 'Staff Diperbarui',
            'role' => 'Prodi',
        ]);

        // 3. Delete account
        $response = $this->actingAs($adminAkun)->delete("/users/{$createdUser->id}");
        $response->assertSessionHas('flash_success');
        $this->assertDatabaseMissing('users', ['id' => $createdUser->id]);
    }

    public function test_admin_cannot_access_other_features(): void
    {
        $adminAkun = $this->adminPembantu();

        // Admin visiting root / is redirected to manajemen-akun
        $this->actingAs($adminAkun)->get('/')->assertRedirect(route('manajemen-akun'));

        // Admin cannot access Super Admin dashboard (forbidden 403)
        $this->actingAs($adminAkun)->get('/dashboard')->assertForbidden();

        // Admin cannot access master data or scheduling (forbidden 403)
        $this->actingAs($adminAkun)->get('/jadwal-kuliah')->assertForbidden();
        $this->actingAs($adminAkun)->get('/penjadwalan')->assertForbidden();
        $this->actingAs($adminAkun)->post('/fakultas', ['nama' => 'Fakultas Test'])->assertForbidden();
        $this->actingAs($adminAkun)->post('/mata-kuliah', ['nama' => 'MK Test'])->assertForbidden();
        $this->actingAs($adminAkun)->post('/prodi', ['nama' => 'Prodi Test'])->assertForbidden();
    }

    public function test_admin_cannot_create_another_admin(): void
    {
        $adminAkun = $this->adminPembantu();

        $response = $this->actingAs($adminAkun)->post('/users', [
            'nama' => 'Admin Ilegal',
            'email' => 'adminilegal@gmail.com',
            'role' => UserRole::ADMIN,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHas('flash_error', 'Hanya Super Admin yang dapat membuat akun Admin!');
        $this->assertDatabaseMissing('users', ['email' => 'adminilegal@gmail.com']);
    }

    public function test_non_super_admin_cannot_access_admin_pages(): void
    {
        $user = User::where('email', 'randtian@gmail.com')->firstOrFail();

        $this->actingAs($user)->get('/jadwal-kuliah')->assertForbidden();
        $this->actingAs($user)->get('/penjadwalan')->assertForbidden();
    }

    public function test_admin_cannot_edit_own_account_in_users_table(): void
    {
        $adminAkun = $this->adminPembantu();

        $response = $this->actingAs($adminAkun)->put("/users/{$adminAkun->id}", [
            'nama' => 'Admin Berubah',
            'email' => $adminAkun->email,
            'role' => UserRole::ADMIN,
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHas('flash_error', 'Admin tidak dapat mengubah akun sendiri melalui tabel. Silakan gunakan menu Edit Profile.');
        $this->assertDatabaseMissing('users', ['nama' => 'Admin Berubah']);
    }

    public function test_admin_can_update_own_account_via_edit_profile(): void
    {
        $adminAkun = $this->adminPembantu();

        $response = $this->actingAs($adminAkun)->post('/profile', [
            'nama' => 'Admin Pembantu Updated',
            'email' => 'admin_baru@apjad.com',
            'password_lama' => 'admin123',
            'password_baru' => 'newpassword123',
            'password_baru_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHas('flash_success', 'Profile berhasil diperbarui!');
        $this->assertDatabaseHas('users', [
            'id' => $adminAkun->id,
            'nama' => 'Admin Pembantu Updated',
            'email' => 'admin_baru@apjad.com',
        ]);
        $this->assertTrue(Hash::check('newpassword123', $adminAkun->fresh()->password));
    }

    public function test_admin_does_not_see_own_account_in_table(): void
    {
        $adminAkun = $this->adminPembantu();

        // Saat Admin membuka manajemen akun, akun Admin sendiri tidak muncul di tabel
        $response = $this->actingAs($adminAkun)->get('/manajemen-akun');
        $response->assertStatus(200);
        $response->assertSee('Revi Aedrian');
        $response->assertDontSee("<td>{$adminAkun->email}</td>", false);

        // Super Admin tetap melihat akun Admin di tabel
        $superAdminResponse = $this->actingAs($this->admin())->get('/manajemen-akun');
        $superAdminResponse->assertStatus(200);
        $superAdminResponse->assertSee("<td>{$adminAkun->email}</td>", false);
    }
}
