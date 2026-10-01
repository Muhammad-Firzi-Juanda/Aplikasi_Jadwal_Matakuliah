<?php

namespace App\Http\Controllers;

use App\Models\Rombel;
use App\Models\Jadwal;
use App\Models\MataKuliahDetail;
use Illuminate\View\View;

class ProdiDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_rombel'      => Rombel::count(),
            'total_mata_kuliah' => MataKuliahDetail::count(),
            'total_jadwal'      => Jadwal::where('is_active', true)->count(),
            'semester_aktif'    => MataKuliahDetail::distinct('semester')->count('semester'),
        ];

        $jadwals = Jadwal::with(['rombel.mataKuliah', 'dosen', 'ruangan'])
                        ->where('is_active', true)
                        ->orderBy('hari')
                        ->orderBy('jam_mulai')
                        ->get();

        $hariList   = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $perHari    = $jadwals->groupBy('hari');
        $semesterList = MataKuliahDetail::distinct()->pluck('semester')->sort()->values();

        return view('prodi.dashboard', compact('stats', 'jadwals', 'perHari', 'hariList', 'semesterList'));
    }
}
