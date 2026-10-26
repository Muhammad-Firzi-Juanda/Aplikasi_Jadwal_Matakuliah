<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $mengulangDetails = DB::table('mata_kuliah_details')
            ->where(function ($q) {
                $q->where('kelas', 'like', '%ulang%')
                    ->orWhere('kelas', 'like', '%mengulang%')
                    ->orWhere('kode_mk', 'like', '%-ulang%')
                    ->orWhere('kode_mk', 'like', '%-mengulang%')
                    ->orWhere('nama_mk', 'like', '%(mengulang)%');
            })
            ->pluck('id');

        if ($mengulangDetails->isNotEmpty()) {
            DB::table('mata_kuliah_details')
                ->whereIn('id', $mengulangDetails)
                ->update([
                    'status_tawar' => false,
                    'is_active' => false,
                    'status_verifikasi' => 'mengulang',
                ]);

            $rombelIds = DB::table('rombels')
                ->whereIn('mata_kuliah_detail_id', $mengulangDetails)
                ->pluck('id');

            if ($rombelIds->isNotEmpty()) {
                DB::table('jadwals')->whereIn('rombel_id', $rombelIds)->delete();
                DB::table('rombels')->whereIn('id', $rombelIds)->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
