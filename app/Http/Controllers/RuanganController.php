<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RuanganController extends Controller
{
    public function index(): View
    {
        $ruangans = Ruangan::orderBy('tipe')->orderBy('kode')->get();

        return view('ruangan.index', compact('ruangans'));
    }

    public function show(Ruangan $ruangan): JsonResponse
    {
        return response()->json($ruangan);
    }

    public function create(): View
    {
        return view('ruangan.create');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        if ($user?->role === 'Jurusan') {
            $msg = 'Peran Jurusan hanya memiliki akses monitoring dan evaluasi.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }

            return redirect()->back()->with('flash_error', $msg)->with('tab_aktif', 'tab-ruangan');
        }

        try {
            $validated = $request->validate([
                'kode' => 'required|string|max:20|unique:ruangans,kode',
                'nama' => 'required|string|max:100',
                'tipe' => 'required|in:Lab,Ruang Kelas,Aula,Lainnya',
                'kapasitas' => 'required|integer|min:1|max:500',
                'gedung' => 'nullable|string|max:50',
                'lantai' => 'nullable|max:10',
            ], [
                'kode.required' => 'Kode ruangan wajib diisi.',
                'kode.unique' => 'Kode ruangan sudah digunakan.',
                'kode.max' => 'Kode ruangan maksimal 20 karakter.',
                'nama.required' => 'Nama ruangan wajib diisi.',
                'nama.max' => 'Nama ruangan maksimal 100 karakter.',
                'tipe.required' => 'Tipe ruangan wajib dipilih.',
                'tipe.in' => 'Tipe ruangan harus berupa Lab, Ruang Kelas, Aula, atau Lainnya.',
                'kapasitas.required' => 'Kapasitas ruangan wajib diisi.',
                'kapasitas.integer' => 'Kapasitas harus berupa angka.',
                'kapasitas.min' => 'Kapasitas minimal 1 orang.',
                'kapasitas.max' => 'Kapasitas maksimal 500 orang.',
                'gedung.max' => 'Nama gedung maksimal 50 karakter.',
                'lantai.max' => 'Lantai maksimal 10 karakter.',
            ]);

            // Handle is_active checkbox
            $validated['is_active'] = $request->boolean('is_active', $request->has('is_active'));

            // Set nullable fields to null if empty
            if (empty($validated['gedung'])) {
                $validated['gedung'] = null;
            }
            if (empty($validated['lantai'])) {
                $validated['lantai'] = null;
            }

            $ruangan = Ruangan::create($validated);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ruangan berhasil ditambahkan.',
                    'data' => $ruangan,
                ]);
            }

            if (str_contains(url()->previous() ?? '', 'penjadwalan')) {
                return redirect()->route('penjadwalan.index')->with('flash_success', 'Ruangan berhasil ditambahkan.')->with('tab_aktif', 'tab-ruangan');
            }

            return redirect()->route('ruangan.index')->with('flash_success', 'Ruangan berhasil ditambahkan.');
        } catch (ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => implode(' ', Arr::flatten($e->errors())),
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->back()->withInput()->withErrors($e->errors())->with('tab_aktif', 'tab-ruangan');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()])->with('tab_aktif', 'tab-ruangan');
        }
    }

    public function edit(Ruangan $ruangan): View
    {
        return view('ruangan.edit', compact('ruangan'));
    }

    public function update(Request $request, Ruangan $ruangan): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        if ($user?->role === 'Jurusan') {
            $msg = 'Peran Jurusan hanya memiliki akses monitoring dan evaluasi.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }

            return redirect()->back()->with('flash_error', $msg)->with('tab_aktif', 'tab-ruangan');
        }

        try {
            $validated = $request->validate([
                'kode' => 'required|string|max:20|unique:ruangans,kode,'.$ruangan->id,
                'nama' => 'required|string|max:100',
                'tipe' => 'required|in:Lab,Ruang Kelas,Aula,Lainnya',
                'kapasitas' => 'required|integer|min:1|max:500',
                'gedung' => 'nullable|string|max:50',
                'lantai' => 'nullable|max:10',
            ], [
                'kode.required' => 'Kode ruangan wajib diisi.',
                'kode.unique' => 'Kode ruangan sudah digunakan.',
                'kode.max' => 'Kode ruangan maksimal 20 karakter.',
                'nama.required' => 'Nama ruangan wajib diisi.',
                'nama.max' => 'Nama ruangan maksimal 100 karakter.',
                'tipe.required' => 'Tipe ruangan wajib dipilih.',
                'tipe.in' => 'Tipe ruangan harus berupa Lab, Ruang Kelas, Aula, atau Lainnya.',
                'kapasitas.required' => 'Kapasitas ruangan wajib diisi.',
                'kapasitas.integer' => 'Kapasitas harus berupa angka.',
                'kapasitas.min' => 'Kapasitas minimal 1 orang.',
                'kapasitas.max' => 'Kapasitas maksimal 500 orang.',
                'gedung.max' => 'Nama gedung maksimal 50 karakter.',
                'lantai.max' => 'Lantai maksimal 10 karakter.',
            ]);

            // Handle is_active checkbox
            $validated['is_active'] = $request->boolean('is_active', $request->has('is_active'));

            // Set nullable fields to null if empty
            if (empty($validated['gedung'])) {
                $validated['gedung'] = null;
            }
            if (empty($validated['lantai'])) {
                $validated['lantai'] = null;
            }

            $ruangan->update($validated);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ruangan berhasil diperbarui.',
                    'data' => $ruangan,
                ]);
            }

            if (str_contains(url()->previous() ?? '', 'penjadwalan')) {
                return redirect()->route('penjadwalan.index')->with('flash_success', 'Ruangan berhasil diperbarui.')->with('tab_aktif', 'tab-ruangan');
            }

            return redirect()->route('ruangan.index')->with('flash_success', 'Ruangan berhasil diperbarui.');
        } catch (ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => implode(' ', Arr::flatten($e->errors())),
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->back()->withInput()->withErrors($e->errors())->with('tab_aktif', 'tab-ruangan');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()])->with('tab_aktif', 'tab-ruangan');
        }
    }

    public function destroy(Request $request, Ruangan $ruangan): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        if ($user?->role === 'Jurusan') {
            $msg = 'Peran Jurusan hanya memiliki akses monitoring dan evaluasi.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }

            return redirect()->back()->with('flash_error', $msg)->with('tab_aktif', 'tab-ruangan');
        }

        if ($ruangan->isDefault()) {
            $message = "Ruangan bawaan kampus ({$ruangan->kode}) tidak dapat dihapus. Anda dapat menonaktifkannya jika tidak digunakan.";
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->back()->withErrors(['error' => $message])->with('tab_aktif', 'tab-ruangan');
        }

        try {
            $ruangan->delete();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Ruangan berhasil dihapus.']);
            }

            if (str_contains(url()->previous() ?? '', 'penjadwalan')) {
                return redirect()->route('penjadwalan.index')->with('flash_success', 'Ruangan berhasil dihapus.')->with('tab_aktif', 'tab-ruangan');
            }

            return redirect()->route('ruangan.index')->with('flash_success', 'Ruangan berhasil dihapus.');
        } catch (\Exception $e) {
            $message = 'Ruangan tidak dapat dihapus karena masih digunakan dalam jadwal perkuliahan.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->back()->withErrors(['error' => $message])->with('tab_aktif', 'tab-ruangan');
        }
    }

    public function toggleStatus(Request $request, Ruangan $ruangan): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        if ($user?->role === 'Jurusan') {
            $msg = 'Peran Jurusan hanya memiliki akses monitoring dan evaluasi.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }

            return redirect()->back()->with('flash_error', $msg)->with('tab_aktif', 'tab-ruangan');
        }

        $ruangan->is_active = ! $ruangan->is_active;
        $ruangan->save();

        $status = $ruangan->is_active ? 'diaktifkan' : 'dinonaktifkan';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Ruangan {$ruangan->kode} berhasil {$status}.",
                'is_active' => (bool) $ruangan->is_active,
            ]);
        }

        if (str_contains(url()->previous() ?? '', 'penjadwalan')) {
            return redirect()->route('penjadwalan.index')->with('flash_success', "Ruangan {$ruangan->kode} berhasil {$status}.")->with('tab_aktif', 'tab-ruangan');
        }

        return redirect()->route('ruangan.index')->with('flash_success', "Ruangan {$ruangan->kode} berhasil {$status}.");
    }
}
