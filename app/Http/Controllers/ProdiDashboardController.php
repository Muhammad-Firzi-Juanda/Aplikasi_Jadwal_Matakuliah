<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\MataKuliahDetail;
use App\Models\Rombel;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProdiDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $prodi = $user->prodi;
        $prodiName = $prodi?->nama ?? 'Teknik Informatika';

        $scopeMk = function ($q) use ($prodiName) {
            $q->where('prodi', 'like', "%{$prodiName}%")
                ->orWhereIn('kategori', ['MK Universitas', 'MK Fakultas']);
        };

        $stats = [
            'total_dosen' => Dosen::where('prodi', 'like', "%{$prodiName}%")->where('is_active', true)->count(),
            'total_mata_kuliah' => MataKuliahDetail::where($scopeMk)->where('is_active', true)->count(),
            'total_rombel' => Rombel::whereHas('mataKuliah', $scopeMk)->count(),
            'total_jadwal' => Jadwal::whereHas('rombel.mataKuliah', $scopeMk)->where('is_active', true)->count(),
        ];

        $dosens = Dosen::where('prodi', 'like', "%{$prodiName}%")->where('is_active', true)->orderBy('nama')->get();

        $rombels = Rombel::with(['mataKuliah', 'dosenPengampu'])
            ->whereHas('mataKuliah', $scopeMk)
            ->orderBy('kode_rombel')
            ->get();

        $mataKuliahs = MataKuliahDetail::with(['dosenKetua', 'dosenAnggota'])
            ->where($scopeMk)
            ->where('is_active', true)
            ->orderBy('semester')
            ->orderBy('kode_mk')
            ->get()
            ->groupBy('semester');

        $jadwals = Jadwal::with(['rombel.mataKuliah', 'dosen', 'ruangan'])
            ->whereHas('rombel.mataKuliah', $scopeMk)
            ->where('is_active', true)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        // Data untuk modal tambah jadwal
        $ruangans = Ruangan::where('is_active', true)->orderBy('kode')->get();
        $dosenProdi = Dosen::where('prodi', $prodiName)->where('is_active', true)->orderBy('nama')->get();
        $rombelProdi = Rombel::with('mataKuliah')
            ->whereHas('mataKuliah', function ($q) use ($prodiName) {
                $q->where('prodi', $prodiName);
            })
            ->orderBy('kode_rombel')
            ->get();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $perHari = $jadwals->groupBy('hari');
        $semesterList = MataKuliahDetail::where('prodi', $prodiName)
            ->where('is_active', true)
            ->distinct()
            ->pluck('semester')
            ->sort()
            ->values();

        return view('prodi.dashboard', compact(
            'stats', 'dosens', 'rombels', 'mataKuliahs',
            'jadwals', 'perHari', 'hariList', 'semesterList',
            'prodiName', 'ruangans', 'dosenProdi', 'rombelProdi'
        ));
    }
}
