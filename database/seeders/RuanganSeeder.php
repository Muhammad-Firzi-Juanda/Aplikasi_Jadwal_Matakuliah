<?php

namespace Database\Seeders;

use App\Models\Ruangan;
use Illuminate\Database\Seeder;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        // 12 Ruang Kelas dari R01-FTTK sampai R12-FTTK (dengan leading zero)
        for ($i = 1; $i <= 12; $i++) {
            $kode = 'R'.str_pad($i, 2, '0', STR_PAD_LEFT).'-FTTK';
            Ruangan::firstOrCreate(
                ['kode' => $kode],
                [
                    'nama' => "Ruang Kelas {$i}",
                    'tipe' => 'Ruang Kelas',
                    'kapasitas' => 50,
                    'gedung' => 'FTTK',
                    'lantai' => ($i <= 6) ? 1 : 2,
                    'is_active' => true,
                ]
            );
        }

        // 2 Lab (dengan leading zero)
        Ruangan::firstOrCreate(
            ['kode' => 'LAB-01-FTTK'],
            [
                'nama' => 'Laboratorium Komputer 1',
                'tipe' => 'Lab',
                'kapasitas' => 40,
                'gedung' => 'FTTK',
                'lantai' => 1,
                'is_active' => true,
            ]
        );

        Ruangan::firstOrCreate(
            ['kode' => 'LAB-02-FTTK'],
            [
                'nama' => 'Laboratorium Komputer 2',
                'tipe' => 'Lab',
                'kapasitas' => 40,
                'gedung' => 'FTTK',
                'lantai' => 2,
                'is_active' => true,
            ]
        );

        if ($this->command) {
            $this->command->info('Ruangan seeder berhasil dijalankan: 14 ruangan bawaan kampus (12 ruang kelas + 2 lab) dipastikan ada.');
        }
    }
}
