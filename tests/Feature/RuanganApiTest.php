<?php

namespace Tests\Feature;

use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RuanganApiTest extends TestCase
{
    use RefreshDatabase;

    private const ADMIN_EMAIL = 'admin@siwalan.com';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin(): User
    {
        return User::where('email', self::ADMIN_EMAIL)->firstOrFail();
    }

    public function test_guest_cannot_access_ruangan_api(): void
    {
        $response = $this->postJson('/api/ruangan', [
            'kode' => 'R-TEST',
            'nama' => 'Ruangan Uji',
            'tipe' => 'Ruang Kelas',
            'kapasitas' => 30,
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_create_ruangan_via_api(): void
    {
        $response = $this->actingAs($this->admin())
            ->postJson('/api/ruangan', [
                'kode' => 'R-LAB10',
                'nama' => 'Laboratorium Jaringan',
                'tipe' => 'Lab',
                'kapasitas' => 45,
                'gedung' => 'FTTK',
                'lantai' => 2,
                'is_active' => '1',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Ruangan berhasil ditambahkan.',
        ]);

        $this->assertDatabaseHas('ruangans', [
            'kode' => 'R-LAB10',
            'nama' => 'Laboratorium Jaringan',
            'is_active' => 1,
        ]);
    }

    public function test_create_ruangan_validation_failure_returns_json(): void
    {
        $response = $this->actingAs($this->admin())
            ->postJson('/api/ruangan', [
                'kode' => '',
                'nama' => '',
                'tipe' => 'InvalidType',
                'kapasitas' => 0,
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_user_can_fetch_ruangan_details_via_api(): void
    {
        $ruangan = Ruangan::create([
            'kode' => 'R-GET',
            'nama' => 'Ruang Fetch',
            'tipe' => 'Ruang Kelas',
            'kapasitas' => 35,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin())
            ->getJson("/api/ruangan/{$ruangan->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $ruangan->id,
            'kode' => 'R-GET',
            'nama' => 'Ruang Fetch',
        ]);
    }

    public function test_user_can_update_ruangan_via_api(): void
    {
        $ruangan = Ruangan::create([
            'kode' => 'R-UPDATE',
            'nama' => 'Nama Lama',
            'tipe' => 'Ruang Kelas',
            'kapasitas' => 30,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin())
            ->putJson("/api/ruangan/{$ruangan->id}", [
                'kode' => 'R-UPDATE',
                'nama' => 'Nama Baru',
                'tipe' => 'Lab',
                'kapasitas' => 50,
                'is_active' => '0',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Ruangan berhasil diperbarui.',
        ]);

        $ruangan->refresh();
        $this->assertSame('Nama Baru', $ruangan->nama);
        $this->assertSame('Lab', $ruangan->tipe);
        $this->assertFalse((bool) $ruangan->is_active);
    }

    public function test_user_can_toggle_ruangan_status_via_api(): void
    {
        $ruangan = Ruangan::create([
            'kode' => 'R-TOGGLE',
            'nama' => 'Ruang Toggle',
            'tipe' => 'Ruang Kelas',
            'kapasitas' => 30,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin())
            ->postJson("/api/ruangan/{$ruangan->id}/toggle-status");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_active' => false,
        ]);

        $ruangan->refresh();
        $this->assertFalse((bool) $ruangan->is_active);

        // Toggle back to active
        $response2 = $this->actingAs($this->admin())
            ->postJson("/api/ruangan/{$ruangan->id}/toggle-status");

        $response2->assertStatus(200);
        $response2->assertJson([
            'success' => true,
            'is_active' => true,
        ]);

        $ruangan->refresh();
        $this->assertTrue((bool) $ruangan->is_active);
    }

    public function test_user_can_delete_ruangan_via_api(): void
    {
        $ruangan = Ruangan::create([
            'kode' => 'R-DEL',
            'nama' => 'Ruang Hapus',
            'tipe' => 'Ruang Kelas',
            'kapasitas' => 30,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin())
            ->deleteJson("/api/ruangan/{$ruangan->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Ruangan berhasil dihapus.',
        ]);

        $this->assertDatabaseMissing('ruangans', [
            'id' => $ruangan->id,
        ]);
    }

    public function test_cannot_delete_default_campus_ruangan(): void
    {
        $ruangan = Ruangan::where('kode', 'R01-FTTK')->firstOrFail();

        $response = $this->actingAs($this->admin())
            ->deleteJson("/api/ruangan/{$ruangan->id}");

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);

        $this->assertDatabaseHas('ruangans', [
            'id' => $ruangan->id,
            'kode' => 'R01-FTTK',
        ]);
    }

    public function test_default_campus_ruangans_are_seeded(): void
    {
        $this->assertEquals(14, Ruangan::whereIn('kode', Ruangan::DEFAULT_KODES)->count());
        $this->assertEquals(12, Ruangan::where('tipe', 'Ruang Kelas')->whereIn('kode', Ruangan::DEFAULT_KODES)->count());
        $this->assertEquals(2, Ruangan::where('tipe', 'Lab')->whereIn('kode', Ruangan::DEFAULT_KODES)->count());
    }
}
