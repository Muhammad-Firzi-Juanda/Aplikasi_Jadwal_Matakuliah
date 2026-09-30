<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\MataKuliah;
use App\Models\KelasJadwal;
use App\Models\DosenMengajar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::where('role', '!=', UserRole::SUPER_ADMIN)->orderBy('id', 'asc')->get();
        
        $stats = [
            'total_users' => User::count(),
            'total_fakultas' => Fakultas::count(),
            'total_prodi' => Prodi::count(),
            'total_mata_kuliah' => MataKuliah::count(),
            'total_kelas' => KelasJadwal::count(),
            'total_dosen' => DosenMengajar::count(),
        ];
        
        return view('dashboard', compact('users', 'stats'));
    }

    public function manajemenAkun(): View
    {
        $users = User::where('role', '!=', UserRole::SUPER_ADMIN)->orderBy('id', 'asc')->get();
        return view('admin.manajemen-akun', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'role' => ['required', Rule::in(UserRole::nonAdmin())],
            'password' => 'required|min:12|confirmed',
        ], [
            'nama.required' => 'Nama wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.unique' => 'Email sudah terdaftar!',
            'role.required' => 'Role wajib dipilih!',
            'password.required' => 'Password wajib diisi!',
            'password.min' => 'Password minimal 12 karakter!',
            'password.confirmed' => 'Konfirmasi password tidak cocok!',
        ]);

        User::create([
            'nama' => trim($validated['nama']),
            'email' => trim($validated['email']),
            'role' => $validated['role'],
            'password' => $validated['password'],
        ]);

        return redirect()->back()->with('flash_success', 'Akun berhasil dibuat!');
    }

    public function update(Request $request, ?User $user = null): RedirectResponse
    {
        if (!$user || !$user->exists) {
            $userId = (int)$request->input('user_id');
            $user = User::findOrFail($userId);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(UserRole::nonAdmin())],
            'password' => 'nullable|min:12|confirmed',
        ], [
            'nama.required' => 'Nama wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.unique' => 'Email sudah digunakan oleh user lain!',
            'role.required' => 'Role wajib dipilih!',
            'password.min' => 'Password minimal 12 karakter!',
            'password.confirmed' => 'Konfirmasi password tidak cocok!',
        ]);

        $user->nama = trim($validated['nama']);
        $user->email = trim($validated['email']);
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()->back()->with('flash_success', 'Data akun berhasil diperbarui!');
    }

    public function destroy(Request $request, ?User $user = null): RedirectResponse
    {
        if (!$user || !$user->exists) {
            $userId = (int)$request->input('user_id');
            $user = User::findOrFail($userId);
        }

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('flash_error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!');
        }

        $user->delete();

        return redirect()->back()->with('flash_success', 'User berhasil dihapus!');
    }
}
