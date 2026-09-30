<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use App\Models\Fakultas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

        return redirect()->route('jadwal-kuliah.index')->with('flash_success', 'Program studi berhasil ditambahkan!');
    }

    public function update(Request $request, Prodi $prodi): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:prodi,nama,' . $prodi->id,
            'kode' => 'nullable|string|max:20|unique:prodi,kode,' . $prodi->id,
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

        return redirect()->route('jadwal-kuliah.index')->with('flash_success', 'Program studi berhasil diperbarui!');
    }

    public function destroy(Request $request, ?Prodi $prodi = null): RedirectResponse
    {
        if (!$prodi || !$prodi->exists) {
            $prodiId = (int)$request->input('prodi_id');
            $prodi = Prodi::findOrFail($prodiId);
        }

        $prodi->delete();

        return redirect()->route('jadwal-kuliah.index')->with('flash_success', 'Program studi berhasil dihapus!');
    }
}
