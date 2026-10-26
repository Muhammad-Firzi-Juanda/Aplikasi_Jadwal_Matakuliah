<?php

use App\Models\Fakultas;
use App\Models\MataKuliah;
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
        $fakultas = Fakultas::first();
        if (! $fakultas) {
            $fakultas = Fakultas::create([
                'nama' => 'Fakultas Teknik',
                'dekan' => 'Dekan Fakultas Teknik',
            ]);
        }
        $fakultasId = $fakultas->id;

        // 1. Pastikan Prodi Teknik Informatik (INF) & Teknik Elektro (EEL) terdaftar dengan kode resmi
        $prodiTI = Prodi::where('nama', 'like', '%Informatik%')->first();
        if ($prodiTI) {
            $prodiTI->update(['kode' => 'INF']);
        } else {
            $prodiTI = Prodi::create([
                'nama' => 'Teknik Informatik',
                'kode' => 'INF',
                'fakultas_id' => $fakultasId,
                'kaprodi' => 'Feri Irawan, S.Kom, M.Kom',
            ]);
        }

        $prodiTE = Prodi::where('nama', 'like', '%Elektro%')->first();
        if ($prodiTE) {
            $prodiTE->update(['kode' => 'EEL']);
        } else {
            $prodiTE = Prodi::create([
                'nama' => 'Teknik Elektro',
                'kode' => 'EEL',
                'fakultas_id' => $fakultasId,
                'kaprodi' => 'Dr. Eng. Kaprodi Elektro, M.T.',
            ]);
        }

        // 2. Setup 2 Laboratorium Khusus Informatika
        Ruangan::updateOrCreate(
            ['kode' => 'LAB INF 1'],
            [
                'nama' => 'LAB INF 1',
                'tipe' => 'Lab',
                'kapasitas' => 40,
                'gedung' => 'Teknik',
                'lantai' => '1',
                'prodi_id' => $prodiTI->id,
                'is_active' => true,
            ]
        );

        Ruangan::updateOrCreate(
            ['kode' => 'LAB INF 2'],
            [
                'nama' => 'LAB INF 2',
                'tipe' => 'Lab',
                'kapasitas' => 40,
                'gedung' => 'Teknik',
                'lantai' => '1',
                'prodi_id' => $prodiTI->id,
                'is_active' => true,
            ]
        );

        // 3. Setup 2 Laboratorium Khusus Teknik Elektro
        Ruangan::updateOrCreate(
            ['kode' => 'LAB EEL 1'],
            [
                'nama' => 'LAB EEL 1',
                'tipe' => 'Lab',
                'kapasitas' => 40,
                'gedung' => 'Teknik',
                'lantai' => '1',
                'prodi_id' => $prodiTE->id,
                'is_active' => true,
            ]
        );

        Ruangan::updateOrCreate(
            ['kode' => 'LAB EEL 2'],
            [
                'nama' => 'LAB EEL 2',
                'tipe' => 'Lab',
                'kapasitas' => 40,
                'gedung' => 'Teknik',
                'lantai' => '1',
                'prodi_id' => $prodiTE->id,
                'is_active' => true,
            ]
        );

        // Nonaktifkan / bersihkan lab generic lama (LAB1, LAB2) jika masih ada agar tidak membingungkan
        Ruangan::whereIn('kode', ['LAB1', 'LAB2'])->update(['is_active' => false]);

        // 4. Sinkronisasi level_id master mata kuliah ke prodi yang tepat
        MataKuliah::where('tipe', 'Prodi')
            ->where(function ($q) {
                $q->where('kode', 'like', 'EEL%')
                    ->orWhere('kode', 'like', 'TE%')
                    ->orWhere('pemilik', 'like', '%Elektro%');
            })
            ->update([
                'level_id' => $prodiTE->id,
                'pemilik' => 'Teknik Elektro',
            ]);

        MataKuliah::where('tipe', 'Prodi')
            ->where(function ($q) {
                $q->where('kode', 'like', 'INF%')
                    ->orWhere('kode', 'like', 'TIF%')
                    ->orWhere('pemilik', 'like', '%Informatik%');
            })
            ->update([
                'level_id' => $prodiTI->id,
                'pemilik' => $prodiTI->nama,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Ruangan::whereIn('kode', ['LAB INF 1', 'LAB INF 2', 'LAB EEL 1', 'LAB EEL 2', 'LAB-IF-1', 'LAB-IF-2', 'LAB-EL-1', 'LAB-EL-2'])->delete();
        Ruangan::whereIn('kode', ['LAB1', 'LAB2'])->update(['is_active' => true]);
    }
};
