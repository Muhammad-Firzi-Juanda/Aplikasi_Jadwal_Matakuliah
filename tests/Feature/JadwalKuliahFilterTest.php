<?php

namespace Tests\Feature;

use App\Models\Fakultas;
use App\Models\Jadwal;
use App\Models\MataKuliah;
use App\Models\MataKuliahDetail;
use App\Models\Prodi;
use App\Models\Rombel;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalKuliahFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_super_admin_can_view_jadwal_kuliah_page_with_prodi_separated(): void
    {
        $admin = User::where('role', 'Super Admin')->firstOrFail();

        $fakultas = Fakultas::first();
        $prodiInf = Prodi::firstOrCreate(
            ['nama' => 'Teknik Informatik'],
            ['kode' => 'INF', 'fakultas_id' => $fakultas->id, 'kaprodi' => 'Kaprodi TI']
        );

        // Buat mata kuliah Informatika dan Elektro
        MataKuliah::create([
            'kode' => 'INF901',
            'nama' => 'Algoritma Lanjut',
            'sks' => 3,
            'semester' => 3,
            'tipe' => 'Prodi',
            'level_id' => $prodiInf->id,
            'pemilik' => 'Teknik Informatik',
        ]);

        MataKuliah::create([
            'kode' => 'EEL902',
            'nama' => 'Elektronika Lanjut',
            'sks' => 2,
            'semester' => 4,
            'tipe' => 'Prodi',
            'level_id' => $prodiInf->id, // simulasi data lama yang level_id nya 1
            'pemilik' => 'Teknik Elektro',
        ]);

        $response = $this->actingAs($admin)->get(route('jadwal-kuliah.index'));

        $response->assertStatus(200);
        $response->assertSee('INF901');
        $response->assertSee('EEL902');
        $response->assertSee('Teknik Elektro (EEL)');
        // Pastikan EEL902 terpisah dan tidak menggunakan kunci prodi Informatika ($prodiInf->id)
        $this->assertStringContainsString('data-kode-mk="eel902"', $response->getContent());
        $this->assertStringNotContainsString('data-prodi-key="'.$prodiInf->id.'" data-prodi-id="'.$prodiInf->id.'" data-prodi-nama="teknik informatika" data-kode-mk="eel902"', $response->getContent());
    }

    public function test_separated_lab_rooms_and_teori_praktik_badges(): void
    {
        $admin = User::where('role', 'Super Admin')->firstOrFail();

        // Verifikasi database memiliki 2 Lab Informatika dan 2 Lab Elektro
        $labIf1 = Ruangan::where('kode', 'LAB INF 1')->first();
        $labIf2 = Ruangan::where('kode', 'LAB INF 2')->first();
        $labEl1 = Ruangan::where('kode', 'LAB EEL 1')->first();
        $labEl2 = Ruangan::where('kode', 'LAB EEL 2')->first();

        $this->assertNotNull($labIf1, 'LAB INF 1 harus terdaftar');
        $this->assertNotNull($labIf2, 'LAB INF 2 harus terdaftar');
        $this->assertNotNull($labEl1, 'LAB EEL 1 harus terdaftar');
        $this->assertNotNull($labEl2, 'LAB EEL 2 harus terdaftar');

        $this->assertNotEquals($labIf1->prodi_id, $labEl1->prodi_id, 'Lab Informatika dan Lab Elektro harus memiliki prodi_id yang berbeda');

        // Verifikasi halaman penjadwalan memuat kedua lab dan format kurung (EEL)
        $response = $this->actingAs($admin)->get(route('penjadwalan.index'));
        $response->assertStatus(200);
        $response->assertSee('LAB INF 1');
        $response->assertSee('LAB EEL 1');
        $response->assertSee('Teknik Elektro (EEL)');
    }

    public function test_user_can_update_kuota_single(): void
    {
        $admin = User::where('role', 'Super Admin')->firstOrFail();

        $mkDetail1 = MataKuliahDetail::create([
            'kode_mk' => 'INF101',
            'nama_mk' => 'Dasar Pemrograman',
            'sks' => 3,
            'semester' => 1,
            'kelas' => 'A',
            'tipe' => 'Wajib',
            'prodi' => 'Teknik Informatika',
            'kapasitas_per_kelas' => 30,
            'jumlah_kelas' => 1,
        ]);

        // Single Quick Update Kuota
        $response = $this->actingAs($admin)->post(route('penjadwalan.update-kuota', $mkDetail1), [
            'kapasitas_per_kelas' => 45,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals(45, $mkDetail1->fresh()->kapasitas_per_kelas);
    }

    public function test_kelas_mengulang_is_excluded_from_active_scheduling_and_views(): void
    {
        $admin = User::where('role', 'Super Admin')->firstOrFail();

        $mkNormal = MataKuliahDetail::create([
            'kode_mk' => 'INF301',
            'nama_mk' => 'Jaringan Komputer',
            'sks' => 3,
            'semester' => 3,
            'kelas' => 'A',
            'tipe' => 'Wajib',
            'prodi' => 'Teknik Informatika',
            'jumlah_kelas' => 1,
            'kapasitas_per_kelas' => 40,
            'is_active' => true,
            'status_tawar' => true,
        ]);

        $mkUlang = MataKuliahDetail::create([
            'kode_mk' => 'INF301-ULANG',
            'nama_mk' => 'Jaringan Komputer',
            'sks' => 3,
            'semester' => 3,
            'kelas' => 'ULANG',
            'tipe' => 'Wajib',
            'prodi' => 'Teknik Informatika',
            'jumlah_kelas' => 1,
            'kapasitas_per_kelas' => 40,
            'is_active' => true,
            'status_tawar' => true,
        ]);

        // Pastikan scopeBukanMengulang mengecualikan kelas ULANG
        $nonRepeatCodes = MataKuliahDetail::bukanMengulang()->pluck('kode_mk')->toArray();
        $this->assertContains('INF301', $nonRepeatCodes);
        $this->assertNotContains('INF301-ULANG', $nonRepeatCodes);

        // Kunjungi halaman penjadwalan, kelas ULANG tidak boleh tampil di daftar penawaran aktif
        $response = $this->actingAs($admin)->get(route('penjadwalan.index'));
        $response->assertStatus(200);
        $response->assertSee('INF301');
        $response->assertDontSee('INF301-ULANG');
    }

    public function test_deleting_prodi_cascade_deletes_its_courses_and_schedules(): void
    {
        $admin = User::where('role', 'Super Admin')->firstOrFail();
        $fakultas = Fakultas::first();

        // 1. Buat Prodi Elektro
        $prodiEel = Prodi::create([
            'nama' => 'Teknik Elektro Baru',
            'kode' => 'EEL99',
            'fakultas_id' => $fakultas->id,
            'kaprodi' => 'Kaprodi EE',
        ]);

        // 2. Buat master mata kuliah, detail, rombel, dan jadwal
        $masterMk = MataKuliah::create([
            'kode' => 'EEL9901',
            'nama' => 'Sistem Tenaga Listrik',
            'sks' => 3,
            'semester' => 4,
            'tipe' => 'Prodi',
            'level_id' => $prodiEel->id,
            'pemilik' => 'Teknik Elektro Baru',
        ]);

        $mkDetail = MataKuliahDetail::create([
            'master_mk_id' => $masterMk->id,
            'kode_mk' => 'EEL9901-A',
            'nama_mk' => 'Sistem Tenaga Listrik',
            'sks' => 3,
            'semester' => 4,
            'kelas' => 'A',
            'tipe' => 'Wajib',
            'prodi' => 'Teknik Elektro Baru',
            'jumlah_kelas' => 1,
            'kapasitas_per_kelas' => 40,
            'is_active' => true,
            'status_tawar' => true,
        ]);

        $rombel = Rombel::create([
            'mata_kuliah_detail_id' => $mkDetail->id,
            'kode_rombel' => 'R-EEL9901-A',
            'nomor_rombel' => 1,
            'kelas' => 'A',
            'semester' => 4,
            'sks' => 3,
            'kapasitas' => 40,
            'hari' => 'Senin',
            'jam_mulai' => '07:30',
            'jam_selesai' => '10:00',
            'is_active' => true,
        ]);

        $jadwal = Jadwal::create([
            'rombel_id' => $rombel->id,
            'hari' => 'Senin',
            'jam_mulai' => '07:30',
            'jam_selesai' => '10:00',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('prodi', ['id' => $prodiEel->id]);
        $this->assertDatabaseHas('mata_kuliah', ['id' => $masterMk->id]);
        $this->assertDatabaseHas('mata_kuliah_details', ['id' => $mkDetail->id]);
        $this->assertDatabaseHas('rombels', ['id' => $rombel->id]);
        $this->assertDatabaseHas('jadwals', ['id' => $jadwal->id]);

        // 3. Hapus Prodi via endpoint destroy
        $response = $this->actingAs($admin)->delete(route('prodi.destroy', $prodiEel), [
            'prodi_id' => $prodiEel->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('flash_success');

        // 4. Verifikasi bahwa semua relasi ikut terhapus secara kaskade
        $this->assertDatabaseMissing('prodi', ['id' => $prodiEel->id]);
        $this->assertDatabaseMissing('mata_kuliah', ['id' => $masterMk->id]);
        $this->assertDatabaseMissing('mata_kuliah_details', ['id' => $mkDetail->id]);
        $this->assertDatabaseMissing('rombels', ['id' => $rombel->id]);
        $this->assertDatabaseMissing('jadwals', ['id' => $jadwal->id]);
    }

    public function test_deleting_mata_kuliah_cascade_deletes_its_details_and_schedules(): void
    {
        $admin = User::where('role', 'Super Admin')->firstOrFail();
        $fakultas = Fakultas::first();
        $prodi = Prodi::first();

        $masterMk = MataKuliah::create([
            'kode' => 'INF8888',
            'nama' => 'Cloud Computing',
            'sks' => 3,
            'semester' => 5,
            'tipe' => 'Prodi',
            'level_id' => $prodi->id,
            'pemilik' => $prodi->nama,
        ]);

        $mkDetail = MataKuliahDetail::create([
            'master_mk_id' => $masterMk->id,
            'kode_mk' => 'INF8888-A',
            'nama_mk' => 'Cloud Computing',
            'sks' => 3,
            'semester' => 5,
            'kelas' => 'A',
            'tipe' => 'Wajib',
            'prodi' => $prodi->nama,
            'jumlah_kelas' => 1,
            'kapasitas_per_kelas' => 40,
            'is_active' => true,
            'status_tawar' => true,
        ]);

        $rombel = Rombel::create([
            'mata_kuliah_detail_id' => $mkDetail->id,
            'kode_rombel' => 'R-INF8888-A',
            'nomor_rombel' => 1,
            'kelas' => 'A',
            'semester' => 5,
            'sks' => 3,
            'kapasitas' => 40,
            'hari' => 'Selasa',
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:30',
            'is_active' => true,
        ]);

        $jadwal = Jadwal::create([
            'rombel_id' => $rombel->id,
            'hari' => 'Selasa',
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:30',
            'is_active' => true,
        ]);

        // Hapus Mata Kuliah via endpoint destroy
        $response = $this->actingAs($admin)->delete(route('mata-kuliah.destroy', $masterMk), [
            'mata_kuliah_id' => $masterMk->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('flash_success');

        // Verifikasi terhapus kaskade
        $this->assertDatabaseMissing('mata_kuliah', ['id' => $masterMk->id]);
        $this->assertDatabaseMissing('mata_kuliah_details', ['id' => $mkDetail->id]);
        $this->assertDatabaseMissing('rombels', ['id' => $rombel->id]);
        $this->assertDatabaseMissing('jadwals', ['id' => $jadwal->id]);
    }

    public function test_prodi_user_only_sees_own_prodi_and_no_schedules_when_empty(): void
    {
        $fakultas = Fakultas::first();
        $prodiTI = Prodi::where('nama', 'Teknik Informatika')->first() ?? Prodi::create([
            'nama' => 'Teknik Informatika',
            'kode' => 'TI',
            'fakultas_id' => $fakultas->id,
        ]);
        $prodiTE = Prodi::where('nama', 'Teknik Elektro')->first() ?? Prodi::create([
            'nama' => 'Teknik Elektro',
            'kode' => 'TE',
            'fakultas_id' => $fakultas->id,
        ]);

        $userTI = User::where('role', 'Prodi')->where('prodi_id', $prodiTI->id)->first() ?? User::create([
            'nama' => 'Admin TI',
            'email' => 'ti@test.com',
            'password' => bcrypt('password'),
            'role' => 'Prodi',
            'prodi_id' => $prodiTI->id,
        ]);

        // Buat jadwal hanya untuk Elektro
        $mkElektro = MataKuliahDetail::create([
            'kode_mk' => 'EEL777',
            'nama_mk' => 'Sinyal dan Sistem',
            'sks' => 3,
            'semester' => 3,
            'tipe' => 'Wajib',
            'prodi' => 'Teknik Elektro',
            'jumlah_kelas' => 1,
            'kapasitas_per_kelas' => 30,
            'is_active' => true,
            'status_tawar' => true,
        ]);

        $rombelEl = Rombel::create([
            'mata_kuliah_detail_id' => $mkElektro->id,
            'kode_rombel' => 'R-EEL777-A',
            'nomor_rombel' => 1,
            'kelas' => 'A',
            'semester' => 3,
            'sks' => 3,
            'kapasitas' => 30,
            'hari' => 'Rabu',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:30',
            'is_active' => true,
        ]);

        Jadwal::create([
            'rombel_id' => $rombelEl->id,
            'hari' => 'Rabu',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:30',
            'is_active' => true,
        ]);

        // Akses jadwal-kuliah sebagai Admin Prodi TI (yang belum punya MK)
        $response = $this->actingAs($userTI)->get(route('jadwal-kuliah.index'));
        $response->assertStatus(200);

        // Jadwal EEL777 milik Elektro tidak boleh muncul di tampilan TI
        $response->assertDontSee('EEL777');
        $response->assertDontSee('Sinyal dan Sistem');
    }
}
