<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\MataKuliahDetail;
use App\Models\Prodi;
use App\Models\Rombel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MataKuliahController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'kode' => 'required|string|max:30|unique:mata_kuliah,kode',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:8',
            'tipe' => 'required|in:Fakultas,Prodi',
            'level_id' => 'required|integer',
            'deskripsi' => 'nullable|string',
        ], [
            'nama.required' => 'Nama mata kuliah wajib diisi!',
            'kode.required' => 'Kode mata kuliah wajib diisi!',
            'kode.unique' => 'Kode mata kuliah sudah ada!',
            'sks.required' => 'SKS wajib diisi!',
            'semester.required' => 'Semester wajib diisi!',
            'tipe.required' => 'Tipe wajib dipilih!',
            'level_id.required' => 'Pilih Fakultas/Prodi terlebih dahulu!',
        ]);

        if ($validated['tipe'] === 'Prodi') {
            $prodiObj = Prodi::find($validated['level_id']);
            $validated['pemilik'] = $prodiObj ? $prodiObj->nama : 'Teknik Informatika';
        } else {
            $validated['pemilik'] = 'Fakultas';
        }

        MataKuliah::create($validated);

        return redirect()->route('jadwal-kuliah.index')->with('flash_success', 'Mata kuliah berhasil ditambahkan!');
    }

    public function update(Request $request, MataKuliah $mataKuliah): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'kode' => 'required|string|max:30|unique:mata_kuliah,kode,'.$mataKuliah->id,
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:8',
            'tipe' => 'required|in:Fakultas,Prodi',
            'level_id' => 'required|integer',
            'deskripsi' => 'nullable|string',
        ], [
            'nama.required' => 'Nama mata kuliah wajib diisi!',
            'kode.required' => 'Kode mata kuliah wajib diisi!',
            'kode.unique' => 'Kode mata kuliah sudah ada!',
            'sks.required' => 'SKS wajib diisi!',
            'semester.required' => 'Semester wajib diisi!',
            'tipe.required' => 'Tipe wajib dipilih!',
            'level_id.required' => 'Pilih Fakultas/Prodi terlebih dahulu!',
        ]);

        if ($validated['tipe'] === 'Prodi') {
            $prodiObj = Prodi::find($validated['level_id']);
            $validated['pemilik'] = $prodiObj ? $prodiObj->nama : 'Teknik Informatika';
        } else {
            $validated['pemilik'] = 'Fakultas';
        }

        $mataKuliah->update($validated);

        return redirect()->route('jadwal-kuliah.index')->with('flash_success', 'Mata kuliah berhasil diperbarui!');
    }

    public function destroy(Request $request, ?MataKuliah $mataKuliah = null): RedirectResponse
    {
        if (! $mataKuliah || ! $mataKuliah->exists) {
            $mkId = (int) $request->input('mata_kuliah_id');
            $mataKuliah = MataKuliah::findOrFail($mkId);
        }

        $kodeMk = $mataKuliah->kode;
        $mkId = $mataKuliah->id;

        // 1. Cari MataKuliahDetails yang berelasi
        $mkDetailIds = MataKuliahDetail::where('master_mk_id', $mkId)
            ->orWhere('kode_mk', 'like', "{$kodeMk}%")
            ->pluck('id')
            ->toArray();

        // 2. Cari Rombel
        $rombelIds = Rombel::whereIn('mata_kuliah_detail_id', $mkDetailIds)->pluck('id')->toArray();

        // 3. Hapus Jadwal dari rombel mata kuliah ini
        if (! empty($rombelIds)) {
            DB::table('jadwals')->whereIn('rombel_id', $rombelIds)->delete();
            DB::table('rombels')->whereIn('id', $rombelIds)->delete();
        }

        // 4. Hapus MataKuliahDetails terkait
        if (! empty($mkDetailIds)) {
            MataKuliahDetail::whereIn('id', $mkDetailIds)->delete();
        }

        // 5. Hapus Master MataKuliah
        $mataKuliah->delete();

        Cache::forget('penjadwalan_last_log');
        session()->forget('log_generate');

        return redirect()->route('jadwal-kuliah.index')->with('flash_success', "Mata kuliah {$kodeMk} beserta rombel dan jadwal terkait berhasil dihapus!");
    }
}
