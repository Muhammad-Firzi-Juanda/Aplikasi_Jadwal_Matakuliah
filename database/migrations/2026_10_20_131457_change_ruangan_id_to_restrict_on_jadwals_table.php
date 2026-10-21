<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            // Drop foreign key lama
            $table->dropForeign(['ruangan_id']);

            // Add foreign key baru dengan restrict (tidak cascade delete)
            $table->foreign('ruangan_id')
                ->references('id')
                ->on('ruangans')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            // Drop foreign key restrict
            $table->dropForeign(['ruangan_id']);

            // Restore foreign key cascade
            $table->foreign('ruangan_id')
                ->references('id')
                ->on('ruangans')
                ->onDelete('cascade');
        });
    }
};
