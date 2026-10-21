<?php

namespace Database\Seeders;

use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Fakultas & Prodi defaults
        $fakultas = Fakultas::firstOrCreate(
            ['nama' => 'Fakultas Teknik dan Teknologi Kemaritiman'],
            ['kode' => 'FTTK']
        );

        $prodiTI = Prodi::firstOrCreate(
            ['nama' => 'Teknik Informatika'],
            ['kode' => 'TI', 'fakultas_id' => $fakultas->id, 'kaprodi' => 'Kaprodi TI']
        );

        $prodiTE = Prodi::firstOrCreate(
            ['nama' => 'Teknik Elektro'],
            ['kode' => 'TE', 'fakultas_id' => $fakultas->id, 'kaprodi' => 'Kaprodi TE']
        );

        $users = [
            [
                'nama' => 'Super Admin',
                'email' => 'admin@siwalan.com',
                'password' => Hash::make('admin123'),
                'role' => 'Super Admin',
            ],
            [
                'nama' => 'Admin Akun',
                'email' => 'admin@apjad.com',
                'password' => Hash::make('admin123'),
                'role' => 'Admin',
            ],
            [
                'nama' => 'Revi Aedrian',
                'email' => 'randtian@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'Jurusan',
            ],
            [
                'nama' => 'Brian Darell',
                'email' => 'bdarell@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'Fakultas',
            ],
            [
                'nama' => 'Admin Prodi TI',
                'email' => 'prodi@apjad.com',
                'password' => Hash::make('password123'),
                'role' => 'Prodi',
                'prodi_id' => $prodiTI->id,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Pastikan ruangan bawaan kampus (12 ruang kelas + 2 lab) selalu tersedia
        $this->call(RuanganSeeder::class);
    }
}
