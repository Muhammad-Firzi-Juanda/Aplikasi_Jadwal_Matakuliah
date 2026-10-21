<?php

namespace Tests\Feature;

use App\Models\MataKuliahDetail;
use App\Models\Ruangan;
use App\Models\User;
use App\Services\GeneticSchedulerService;
use App\Services\SchedulerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenjadwalanRoleAndImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_prodi_user_can_access_penjadwalan(): void
    {
        $prodiUser = User::where('role', 'Prodi')->firstOrFail();

        $response = $this->actingAs($prodiUser)->get(route('penjadwalan.index'));

        $response->assertOk();
        $response->assertSee('Prodi: Teknik Informatika');
    }

    public function test_jurusan_user_can_access_penjadwalan_in_monitoring_mode(): void
    {
        $jurusanUser = User::where('role', 'Jurusan')->firstOrFail();

        $response = $this->actingAs($jurusanUser)->get(route('penjadwalan.index'));

        $response->assertOk();
        $response->assertSee('Mode Monitoring Jurusan');

        // Jurusan user cannot generate or store
        $storeResponse = $this->actingAs($jurusanUser)->post(route('penjadwalan.store'), [
            'kode_mk' => 'TEST123',
            'nama_mk' => 'Test Course',
            'sks' => 3,
            'semester' => 2,
            'tipe' => 'Wajib',
            'prodi' => 'Teknik Informatika',
            'jumlah_kelas' => 1,
            'kapasitas_per_kelas' => 40,
        ]);

        $storeResponse->assertSessionHas('flash_error');
    }

    public function test_store_creates_both_master_mk_and_detail_mk(): void
    {
        $admin = User::where('role', 'Super Admin')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('penjadwalan.store'), [
            'kode_mk' => 'INF201-A',
            'nama_mk' => 'Struktur Data',
            'sks' => 3,
            'semester' => 2,
            'tipe' => 'Wajib',
            'prodi' => 'Teknik Informatika',
            'jumlah_kelas' => 1,
            'kapasitas_per_kelas' => 40,
        ]);

        $response->assertRedirect(route('penjadwalan.index'));
        $this->assertDatabaseHas('mata_kuliah_details', ['kode_mk' => 'INF201-A']);
        $this->assertDatabaseHas('mata_kuliah', ['kode' => 'INF201']);
    }

    public function test_scheduler_modular_slots_strictly_avoid_lunch_break(): void
    {
        $scheduler = app(SchedulerService::class);
        $geneticScheduler = app(GeneticSchedulerService::class);

        foreach ([2, 3, 4] as $sks) {
            $slotsGreedy = $scheduler->getModularSlotMulaiForSks($sks);
            $slotsGenetic = $geneticScheduler->getModularSlotMulaiForSks($sks);

            $this->assertNotEmpty($slotsGreedy);
            $this->assertNotEmpty($slotsGenetic);

            $durasi = $sks * 50;
            foreach ([$slotsGreedy, $slotsGenetic] as $slots) {
                foreach ($slots as $mulaiStr) {
                    [$j, $m] = array_map('intval', explode(':', $mulaiStr));
                    $startMenit = $j * 60 + $m;
                    $endMenit = $startMenit + $durasi;

                    // Jam istirahat siang: 12:30 (750m) s/d 13:00 (780m)
                    $crossesLunch = ($startMenit < 780 && $endMenit > 750) && ! ($endMenit <= 750 || $startMenit >= 780);
                    $this->assertFalse($crossesLunch, "Slot {$mulaiStr} durasi {$durasi}m memotong jam istirahat 12:30-13:00");
                }
            }
        }
    }

    public function test_hasil_generate_tidak_hilang_saat_refresh_halaman(): void
    {
        $admin = User::where('role', 'Super Admin')->first() ?? User::where('role', 'Fakultas')->firstOrFail();

        MataKuliahDetail::create([
            'kode_mk' => 'INF999-A',
            'nama_mk' => 'Pengujian Perangkat Lunak',
            'sks' => 3,
            'semester' => 2,
            'tipe' => 'Wajib',
            'prodi' => 'Teknik Informatika',
            'jumlah_kelas' => 1,
            'kapasitas_per_kelas' => 40,
            'is_active' => 1,
            'status_tawar' => 1,
        ]);

        // Submit generate
        $response = $this->actingAs($admin)->post(route('penjadwalan.generate'), [
            'semester' => [2],
            'prodi' => 'Teknik Informatika',
            'algoritma' => 'greedy',
        ]);

        $response->assertRedirect(route('penjadwalan.index'));
        $response->assertSessionHas('tab_aktif', 'tab-jadwal');
        $response->assertSessionHas('periode_aktif', 'genap');
        $response->assertSessionHas('prodi_aktif', 'Teknik Informatika');
        $response->assertSessionHas('log_generate');

        // Simulasi request berikutnya dengan session tersimpan (seperti browser saat refresh)
        $sessionData = session()->all();
        $refreshVisit = $this->actingAs($admin)->withSession($sessionData)->get(route('penjadwalan.index'));
        $refreshVisit->assertOk();
        $refreshVisit->assertViewHas('prodiAktif', 'Teknik Informatika');
        $refreshVisit->assertViewHas('logGenerate');
        $this->assertNotEmpty($refreshVisit->viewData('logGenerate'));
    }

    public function test_jurusan_user_cannot_mutate_ruangan(): void
    {
        $jurusanUser = User::where('role', 'Jurusan')->firstOrFail();

        // Coba tambah ruangan via API
        $response = $this->actingAs($jurusanUser)->postJson('/api/ruangan', [
            'kode' => 'R-ILLEGAL',
            'nama' => 'Ruangan Ilegal',
            'tipe' => 'Ruang Kelas',
            'kapasitas' => 30,
        ]);
        $response->assertStatus(403);

        // Coba toggle status ruangan via API
        $ruangan = Ruangan::first();
        if ($ruangan) {
            $toggleResponse = $this->actingAs($jurusanUser)->postJson("/api/ruangan/{$ruangan->id}/toggle-status");
            $toggleResponse->assertStatus(403);
        }
    }

    public function test_jurusan_user_cannot_import_or_delete_all(): void
    {
        $jurusanUser = User::where('role', 'Jurusan')->firstOrFail();

        // Coba preview import
        $previewResponse = $this->actingAs($jurusanUser)->postJson(route('penjadwalan.import.preview'));
        $previewResponse->assertStatus(403);

        // Coba destroy all
        $destroyResponse = $this->actingAs($jurusanUser)->delete(route('penjadwalan.destroy-all'), [
            'scope' => 'all',
        ]);
        $destroyResponse->assertSessionHas('flash_error');
    }

    public function test_prodi_user_cannot_alter_other_prodi_courses(): void
    {
        $prodiTI = User::where('role', 'Prodi')->firstOrFail();

        // Buat MK prodi Elektro
        $mkElektro = MataKuliahDetail::create([
            'kode_mk' => 'ELK101-A',
            'nama_mk' => 'Rangkaian Listrik',
            'sks' => 3,
            'semester' => 2,
            'tipe' => 'Wajib',
            'prodi' => 'Teknik Elektro',
            'jumlah_kelas' => 1,
            'kapasitas_per_kelas' => 40,
            'is_active' => 1,
            'status_tawar' => 1,
        ]);

        // Coba hapus oleh user prodi TI -> ditolak
        $deleteResponse = $this->actingAs($prodiTI)->delete(route('penjadwalan.destroy', $mkElektro->id));
        $deleteResponse->assertSessionHas('flash_error');
        $this->assertDatabaseHas('mata_kuliah_details', ['id' => $mkElektro->id]);
    }
}
