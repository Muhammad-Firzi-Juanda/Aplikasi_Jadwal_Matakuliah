<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $prodi->delete();

        $redirect = Auth::user()->role === 'Fakultas' ? 'fakultas.dashboard' : 'jadwal-kuliah.index';

        return redirect()->route($redirect)->with('flash_success', 'Program studi berhasil dihapus!');
    }
}
