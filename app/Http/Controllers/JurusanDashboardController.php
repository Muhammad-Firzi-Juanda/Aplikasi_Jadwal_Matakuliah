<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\MataKuliahDetail;
use App\Models\Rombel;
use Illuminate\View\View;

class JurusanDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_dosen' => Dosen::count(),
            'total_mata_kuliah' => MataKuliahDetail::count(),
            'total_rombel' => Rombel::count(),
            'total_jadwal' => Jadwal::where('is_active', true)->count(),
        ];

        $dosens = Dosen::orderBy('nama')->get();
        $mataKuliahs = MataKuliahDetail::with(['dosenKetua', 'dosenAnggota'])
            ->orderBy('semester')
            ->orderBy('kode_mk')
            ->get()
            ->groupBy('semester');

        return view('jurusan.dashboard', compact('stats', 'dosens', 'mataKuliahs'));
    }
}
