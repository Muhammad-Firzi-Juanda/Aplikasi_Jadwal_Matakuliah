<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use App\Models\Dosen;
use App\Models\MataKuliahDetail;
use App\Models\Jadwal;
use Illuminate\View\View;

class FakultasDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_prodi'       => Prodi::count(),
            'total_dosen'       => Dosen::count(),
            'total_mata_kuliah' => MataKuliahDetail::count(),
            'total_jadwal'      => Jadwal::where('is_active', true)->count(),
        ];

        $prodis   = Prodi::with('fakultas')->orderBy('nama')->get();
        $jadwals  = Jadwal::with(['rombel.mataKuliah', 'dosen', 'ruangan'])
                        ->where('is_active', true)
                        ->orderBy('hari')
                        ->orderBy('jam_mulai')
                        ->get()
                        ->groupBy('hari');

        return view('fakultas.dashboard', compact('stats', 'prodis', 'jadwals'));
    }
}
