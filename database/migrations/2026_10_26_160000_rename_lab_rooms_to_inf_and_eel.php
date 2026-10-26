<?php

use App\Models\Prodi;
use App\Models\Ruangan;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $prodiTI = Prodi::where('nama', 'like', '%Informatik%')->first();
        $prodiTE = Prodi::where('nama', 'like', '%Elektro%')->first();

        // 1. Update atau buat LAB INF 1
        $labIf1 = Ruangan::whereIn('kode', ['LAB-IF-1', 'LAB INF 1'])->first();
        if ($labIf1) {
            $labIf1->update([
                'kode' => 'LAB INF 1',
                'nama' => 'LAB INF 1',
                'tipe' => 'Lab',
                'prodi_id' => $prodiTI?->id,
                'is_active' => true,
            ]);
        } else {
            Ruangan::create([
                'kode' => 'LAB INF 1',
                'nama' => 'LAB INF 1',
                'tipe' => 'Lab',
                'kapasitas' => 40,
                'gedung' => 'Teknik',
                'lantai' => '1',
                'prodi_id' => $prodiTI?->id,
                'is_active' => true,
            ]);
        }

        // 2. Update atau buat LAB INF 2
        $labIf2 = Ruangan::whereIn('kode', ['LAB-IF-2', 'LAB INF 2'])->first();
        if ($labIf2) {
            $labIf2->update([
                'kode' => 'LAB INF 2',
                'nama' => 'LAB INF 2',
                'tipe' => 'Lab',
                'prodi_id' => $prodiTI?->id,
                'is_active' => true,
            ]);
        } else {
            Ruangan::create([
                'kode' => 'LAB INF 2',
                'nama' => 'LAB INF 2',
                'tipe' => 'Lab',
                'kapasitas' => 40,
                'gedung' => 'Teknik',
                'lantai' => '1',
                'prodi_id' => $prodiTI?->id,
                'is_active' => true,
            ]);
        }

        // 3. Update atau buat LAB EEL 1
        $labEl1 = Ruangan::whereIn('kode', ['LAB-EL-1', 'LAB EEL 1'])->first();
        if ($labEl1) {
            $labEl1->update([
                'kode' => 'LAB EEL 1',
                'nama' => 'LAB EEL 1',
                'tipe' => 'Lab',
                'prodi_id' => $prodiTE?->id,
                'is_active' => true,
            ]);
        } else {
            Ruangan::create([
                'kode' => 'LAB EEL 1',
                'nama' => 'LAB EEL 1',
                'tipe' => 'Lab',
                'kapasitas' => 40,
                'gedung' => 'Teknik',
                'lantai' => '1',
                'prodi_id' => $prodiTE?->id,
                'is_active' => true,
            ]);
        }

        // 4. Update atau buat LAB EEL 2
        $labEl2 = Ruangan::whereIn('kode', ['LAB-EL-2', 'LAB EEL 2'])->first();
        if ($labEl2) {
            $labEl2->update([
                'kode' => 'LAB EEL 2',
                'nama' => 'LAB EEL 2',
                'tipe' => 'Lab',
                'prodi_id' => $prodiTE?->id,
                'is_active' => true,
            ]);
        } else {
            Ruangan::create([
                'kode' => 'LAB EEL 2',
                'nama' => 'LAB EEL 2',
                'tipe' => 'Lab',
                'kapasitas' => 40,
                'gedung' => 'Teknik',
                'lantai' => '1',
                'prodi_id' => $prodiTE?->id,
                'is_active' => true,
            ]);
        }

        // Bersihkan duplikat atau kode lama jika ada yang tertinggal
        Ruangan::whereIn('kode', ['LAB-IF-1', 'LAB-IF-2', 'LAB-EL-1', 'LAB-EL-2'])->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Ruangan::whereIn('kode', ['LAB INF 1', 'LAB INF 2', 'LAB EEL 1', 'LAB EEL 2'])->delete();
    }
};
