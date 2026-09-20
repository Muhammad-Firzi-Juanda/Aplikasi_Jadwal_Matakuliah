<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display dashboard with list of users (excluding Super Admin).
     */
    public function index(): View
    {
        $users = User::where('role', '!=', 'Super Admin')->orderBy('id', 'asc')->get();
        return view('dashboard', compact('users'));
    }

    /**
     * Store a newly created user (only Fakultas, Jurusan, Prodi).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'role' => ['required', Rule::in(['Fakultas', 'Jurusan', 'Prodi'])],
            'password' => 'required|min:6',
        ], [
            'nama.required' => 'Nama wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.unique' => 'Email sudah terdaftar!',
            'role.required' => 'Role wajib dipilih!',
            'password.required' => 'Password wajib diisi!',
            'password.min' => 'Password minimal 6 karakter!',
        ]);

        User::create([
            'nama' => trim($validated['nama']),
            'email' => trim($validated['email']),
            'role' => $validated['role'],
            'password' => $validated['password'],
        ]);

        return redirect()->route('dashboard')->with('flash_success', 'Akun berhasil dibuat!');
    }

    /**
     * Update the specified user (supports route-model binding or input user_id).
     */
    public function update(Request $request, ?User $user = null): RedirectResponse
    {
        if (!$user || !$user->exists) {
            $userId = (int)$request->input('user_id');
            $user = User::findOrFail($userId);
        }

        // Super Admin cannot be updated from general user CRUD
        if ($user->role === 'Super Admin') {
            return redirect()->route('dashboard')->with('flash_error', 'Akun Super Admin dapat diubah melalui menu Edit Profile!');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['Fakultas', 'Jurusan', 'Prodi'])],
            'password' => 'nullable|min:6',
        ], [
            'nama.required' => 'Nama wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.unique' => 'Email sudah digunakan oleh user lain!',
            'role.required' => 'Role wajib dipilih!',
            'password.min' => 'Password minimal 6 karakter!',
        ]);

        $user->nama = trim($validated['nama']);
        $user->email = trim($validated['email']);
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()->route('dashboard')->with('flash_success', 'Data akun berhasil diperbarui!');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, ?User $user = null): RedirectResponse
    {
        if (!$user || !$user->exists) {
            $userId = (int)$request->input('user_id');
            $user = User::findOrFail($userId);
        }

        // Prevent deleting Super Admin
        if ($user->role === 'Super Admin') {
            return redirect()->route('dashboard')->with('flash_error', 'Akun Super Admin tidak dapat dihapus!');
        }

        // Prevent deleting currently authenticated user
        if ($user->id === Auth::id()) {
            return redirect()->route('dashboard')->with('flash_error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!');
        }

        $user->delete();

        return redirect()->route('dashboard')->with('flash_success', 'User berhasil dihapus!');
    }
}
