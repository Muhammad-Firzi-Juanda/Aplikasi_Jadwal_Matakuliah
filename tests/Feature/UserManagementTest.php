<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed default users
        $this->seed();
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
        $response->assertSee('Jadwal Ku');
        $response->assertSee('Universitas Maritim');
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'antony@gmail.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('show_error', true);
        $this->assertGuest();
    }

    public function test_login_succeeds_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'antony@gmail.com',
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_ajax_succeeds_with_json_popup_response(): void
    {
        $response = $this->postJson('/login', [
            'email' => 'antony@gmail.com',
            'password' => 'admin123',
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
            'email' => 'antony@gmail.com',
            'password' => 'wrongpass',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Email atau Password Anda Salah',
        ]);
        $this->assertGuest();
    }

    public function test_dashboard_displays_users_for_authenticated_user(): void
    {
        $user = User::where('email', 'antony@gmail.com')->first();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Manajemen User');
        $response->assertSee('Revi Aedrian');
        $response->assertSee('Brian Darell');
        // Super Admin is in header badge, but not in user management table
        $response->assertSee('Super Admin,');
        $response->assertDontSee('<td>Super Admin</td>', false);
    }

    public function test_create_user_successfully(): void
    {
        $admin = User::where('email', 'antony@gmail.com')->first();

        $response = $this->actingAs($admin)->post('/users', [
            'nama' => 'Dosen Baru',
            'email' => 'dosenbaru@gmail.com',
            'role' => 'Jurusan',
            'password' => 'password123',
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
        $admin = User::where('email', 'antony@gmail.com')->first();

        $response = $this->actingAs($admin)->post('/users', [
            'nama' => 'Kaprodi Baru',
            'email' => 'kaprodi@gmail.com',
            'role' => 'Prodi',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('flash_success', 'Akun berhasil dibuat!');

        $this->assertDatabaseHas('users', [
            'email' => 'kaprodi@gmail.com',
            'nama' => 'Kaprodi Baru',
            'role' => 'Prodi',
        ]);
    }

    public function test_update_user_successfully(): void
    {
        $admin = User::where('email', 'antony@gmail.com')->first();
        $target = User::where('email', 'randtian@gmail.com')->first();

        $response = $this->actingAs($admin)->put("/users/{$target->id}", [
            'nama' => 'Revi Aedrian Updated',
            'email' => 'randtian@gmail.com',
            'role' => 'Fakultas',
            'password' => '',
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
        $admin = User::where('email', 'antony@gmail.com')->first();

        $response = $this->actingAs($admin)->delete("/users/{$admin->id}");

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('flash_error', 'Akun Super Admin tidak dapat dihapus!');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_delete_other_user(): void
    {
        $admin = User::where('email', 'antony@gmail.com')->first();
        $target = User::where('email', 'bdarell@gmail.com')->first();

        $response = $this->actingAs($admin)->delete("/users/{$target->id}");

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('flash_success', 'User berhasil dihapus!');
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_edit_profile_super_admin(): void
    {
        $admin = User::where('email', 'antony@gmail.com')->first();

        // Update name and email and password
        $response = $this->actingAs($admin)->post('/profile', [
            'nama' => 'Admin Utama',
            'email' => 'adminutama@gmail.com',
            'password_lama' => 'admin123',
            'password_baru' => 'newadminpassword123',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('flash_success', 'Profile berhasil diperbarui!');

        $admin->refresh();
        $this->assertEquals('Admin Utama', $admin->nama);
        $this->assertEquals('adminutama@gmail.com', $admin->email);
        $this->assertTrue(Hash::check('newadminpassword123', $admin->password));
    }

    public function test_cannot_create_super_admin_from_crud(): void
    {
        $admin = User::where('email', 'antony@gmail.com')->first();

        $response = $this->actingAs($admin)->post('/users', [
            'nama' => 'Hacker Super',
            'email' => 'hack@gmail.com',
            'role' => 'Super Admin',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'hack@gmail.com']);
    }

    public function test_logout(): void
    {
        $admin = User::where('email', 'antony@gmail.com')->first();

        $response = $this->actingAs($admin)->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
