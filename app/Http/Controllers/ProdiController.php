<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\MataKuliahDetail;
use App\Models\Prodi;
use App\Models\Rombel;
use App\Models\Ruangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProdiController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:prodi,nama',
            'kode' => 'nullable|string|max:20|unique:prodi,kode',
            'fakultas_id' => 'required|exists:fakultas,id',
            'kaprodi' => 'nullable|string|max:100',
        ], [
            'nama.required' => 'Nama prodi wajib diisi!',
            'nama.unique' => 'Nama prodi sudah ada!',
            'kode.unique' => 'Kode prodi sudah ada!',
            'fakultas_id.required' => 'Pilih fakultas terlebih dahulu!',
            'fakultas_id.exists' => 'Fakultas tidak ditemukan!',
        ]);

        Prodi::create($validated);

        $redirect = Auth::user()->role === 'Fakultas' ? 'fakultas.dashboard' : 'jadwal-kuliah.index';

        return redirect()->route($redirect)->with('flash_success', 'Program studi berhasil ditambahkan!');
    }

    public function update(Request $request, Prodi $prodi): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:prodi,nama,'.$prodi->id,
            'kode' => 'nullable|string|max:20|unique:prodi,kode,'.$prodi->id,
            'fakultas_id' => 'required|exists:fakultas,id',
            'kaprodi' => 'nullable|string|max:100',
        ], [
            'nama.required' => 'Nama prodi wajib diisi!',
            'nama.unique' => 'Nama prodi sudah ada!',
            'kode.unique' => 'Kode prodi sudah ada!',
            'fakultas_id.required' => 'Pilih fakultas terlebih dahulu!',
            'fakultas_id.exists' => 'Fakultas tidak ditemukan!',
        ]);

        $prodi->update($validated);

        $redirect = Auth::user()->role === 'Fakultas' ? 'fakultas.dashboard' : 'jadwal-kuliah.index';

        return redirect()->route($redirect)->with('flash_success', 'Program studi berhasil diperbarui!');
    }

    public function destroy(Request $request, ?Prodi $prodi = null): RedirectResponse
    {
        if (! $prodi || ! $prodi->exists) {
            $prodiId = (int) $request->input('prodi_id');
            $prodi = Prodi::findOrFail($prodiId);
        }

        $namaProdi = $prodi->nama;
        $kodeProdi = $prodi->kode;
        $prodiId = $prodi->id;

        // 1. Identifikasi master mata kuliah milik prodi ini
        $masterMkQuery = MataKuliah::where('level_id', $prodiId)
            ->orWhere('pemilik', 'like', "%{$namaProdi}%");
        if (! empty($kodeProdi)) {
            $masterMkQuery->orWhere('kode', 'like', "{$kodeProdi}%");
        }
        $masterMkIds = $masterMkQuery->pluck('id')->toArray();

        // 2. Identifikasi MataKuliahDetail milik prodi ini
        $mkDetailQuery = MataKuliahDetail::where('prodi', 'like', "%{$namaProdi}%");
        if (! empty($masterMkIds)) {
            $mkDetailQuery->orWhereIn('master_mk_id', $masterMkIds);
        }
        if (! empty($kodeProdi)) {
            $mkDetailQuery->orWhere('kode_mk', 'like', "{$kodeProdi}%");
        }
        $mkDetailIds = $mkDetailQuery->pluck('id')->toArray();

        // 3. Identifikasi Rombel yang terhubung dengan MK Detail prodi ini
        $rombelIds = Rombel::whereIn('mata_kuliah_detail_id', $mkDetailIds)->pluck('id')->toArray();

        // 4. Hapus Jadwal dari rombel prodi ini
        if (! empty($rombelIds)) {
            DB::table('jadwals')->whereIn('rombel_id', $rombelIds)->delete();
            DB::table('rombels')->whereIn('id', $rombelIds)->delete();
        }

        // 5. Hapus MataKuliahDetail prodi ini
        if (! empty($mkDetailIds)) {
            MataKuliahDetail::whereIn('id', $mkDetailIds)->delete();
        }

        // 6. Hapus Master MataKuliah prodi ini
        if (! empty($masterMkIds)) {
            MataKuliah::whereIn('id', $masterMkIds)->delete();
        }

        // 7. Bersihkan relasi ruangan prodi
        Ruangan::where('prodi_id', $prodiId)->update(['prodi_id' => null]);

        // 8. Bersihkan cache log jadwal
        Cache::forget('penjadwalan_last_log');
        session()->forget(['log_generate', 'prodi_aktif']);

        // 9. Hapus prodi utama
        $prodi->delete();

        $redirect = Auth::user()->role === 'Fakultas' ? 'fakultas.dashboard' : 'jadwal-kuliah.index';

        return redirect()->route($redirect)->with('flash_success', "Program studi {$namaProdi} beserta seluruh mata kuliah dan jadwal kuliahnya berhasil dihapus!");
    }
}
