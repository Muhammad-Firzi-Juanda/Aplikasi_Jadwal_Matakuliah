<?php

use Database\Seeders\RuanganSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        (new RuanganSeeder)->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data ruangan bawaan kampus sengaja dipertahankan agar tidak terhapus.
    }
};
