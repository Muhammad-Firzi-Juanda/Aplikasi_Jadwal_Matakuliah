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
        if (Schema::hasTable('mata_kuliah')) {
            Schema::table('mata_kuliah', function (Blueprint $table) {
                if (! Schema::hasColumn('mata_kuliah', 'kategori')) {
                    $table->string('kategori', 50)->nullable()->after('tipe');
                }
                if (! Schema::hasColumn('mata_kuliah', 'pemilik')) {
                    $table->string('pemilik', 100)->nullable()->after('kategori');
                }
                if (! Schema::hasColumn('mata_kuliah', 'kurikulum')) {
                    $table->string('kurikulum', 50)->nullable()->after('pemilik');
                }
            });
        }

        if (Schema::hasTable('mata_kuliah_details')) {
            Schema::table('mata_kuliah_details', function (Blueprint $table) {
                if (! Schema::hasColumn('mata_kuliah_details', 'kategori')) {
                    $table->string('kategori', 50)->nullable()->after('tipe');
                }
                if (! Schema::hasColumn('mata_kuliah_details', 'master_mk_id')) {
                    $table->foreignId('master_mk_id')->nullable()->after('id')->constrained('mata_kuliah')->nullOnDelete();
                }
                if (! Schema::hasColumn('mata_kuliah_details', 'status_verifikasi')) {
                    $table->string('status_verifikasi', 30)->default('dikenali')->after('is_active');
                }
                if (! Schema::hasColumn('mata_kuliah_details', 'status_tawar')) {
                    $table->boolean('status_tawar')->default(true)->after('status_verifikasi');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mata_kuliah_details')) {
            Schema::table('mata_kuliah_details', function (Blueprint $table) {
                if (Schema::hasColumn('mata_kuliah_details', 'master_mk_id')) {
                    $table->dropForeign(['master_mk_id']);
                    $table->dropColumn('master_mk_id');
                }
                if (Schema::hasColumn('mata_kuliah_details', 'kategori')) {
                    $table->dropColumn('kategori');
                }
                if (Schema::hasColumn('mata_kuliah_details', 'status_verifikasi')) {
                    $table->dropColumn('status_verifikasi');
                }
                if (Schema::hasColumn('mata_kuliah_details', 'status_tawar')) {
                    $table->dropColumn('status_tawar');
                }
            });
        }

        if (Schema::hasTable('mata_kuliah')) {
            Schema::table('mata_kuliah', function (Blueprint $table) {
                if (Schema::hasColumn('mata_kuliah', 'kategori')) {
                    $table->dropColumn('kategori');
                }
                if (Schema::hasColumn('mata_kuliah', 'pemilik')) {
                    $table->dropColumn('pemilik');
                }
                if (Schema::hasColumn('mata_kuliah', 'kurikulum')) {
                    $table->dropColumn('kurikulum');
                }
            });
        }
    }
};
