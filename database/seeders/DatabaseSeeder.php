<?php

namespace Database\Seeders;

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
        $users = [
            [
                'nama' => 'Antony',
                'email' => 'antony@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'Super Admin',
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
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
