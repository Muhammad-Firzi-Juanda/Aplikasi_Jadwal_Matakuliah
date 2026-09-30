<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FakultasController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:fakultas,nama',
            'kode' => 'nullable|string|max:20|unique:fakultas,kode',
            'dekan' => 'nullable|string|max:100',
        ], [
            'nama.required' => 'Nama fakultas wajib diisi!',
            'nama.unique' => 'Nama fakultas sudah ada!',
            'kode.unique' => 'Kode fakultas sudah ada!',
        ]);

        Fakultas::create($validated);

        return redirect()->route('jadwal-kuliah.index')->with('flash_success', 'Fakultas berhasil ditambahkan!');
    }

    public function update(Request $request, Fakultas $fakultas): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:fakultas,nama,' . $fakultas->id,
            'kode' => 'nullable|string|max:20|unique:fakultas,kode,' . $fakultas->id,
            'dekan' => 'nullable|string|max:100',
        ], [
            'nama.required' => 'Nama fakultas wajib diisi!',
            'nama.unique' => 'Nama fakultas sudah ada!',
            'kode.unique' => 'Kode fakultas sudah ada!',
        ]);

        $fakultas->update($validated);

        return redirect()->route('jadwal-kuliah.index')->with('flash_success', 'Fakultas berhasil diperbarui!');
    }

    public function destroy(Request $request, ?Fakultas $fakultas = null): RedirectResponse
    {
        if (!$fakultas || !$fakultas->exists) {
            $fakultasId = (int)$request->input('fakultas_id');
            $fakultas = Fakultas::findOrFail($fakultasId);
        }

        $fakultas->delete();

        return redirect()->route('jadwal-kuliah.index')->with('flash_success', 'Fakultas berhasil dihapus!');
    }
}
