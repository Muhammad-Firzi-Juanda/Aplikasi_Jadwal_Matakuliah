<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login authentication.
     */
    public function login(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $email = trim($request->input('email', ''));
        $password = $request->input('password', '');

        if (empty($email) || empty($password)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau Password Anda Salah',
                ], 422);
            }

            return redirect()->route('login')
                ->withInput($request->only('email'))
                ->with('show_error', true);
        }

        $authenticated = false;

        // Attempt normal authentication
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $authenticated = true;
        } else {
            // Fallback for legacy plain text passwords if any
            $user = User::where('email', $email)->first();
            if ($user && $user->password === $password) {
                $user->password = $password; // Re-hash automatically via casts
                $user->save();
                Auth::login($user);
                $authenticated = true;
            }
        }

        if ($authenticated) {
            $request->session()->regenerate();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login Berhasil',
                    'redirect' => route('dashboard'),
                ]);
            }

            return redirect()->intended(route('dashboard'));
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password Anda Salah',
            ], 422);
        }

        return redirect()->route('login')
            ->withInput($request->only('email'))
            ->with('show_error', true);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Handle change password for authenticated user.
     */
    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6',
        ], [
            'password_lama.required' => 'Password lama wajib diisi!',
            'password_baru.required' => 'Password baru wajib diisi!',
            'password_baru.min' => 'Password baru minimal 6 karakter!',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!Hash::check($request->input('password_lama'), $user->password)) {
            return redirect()->route('dashboard')->with('flash_error', 'Password lama yang Anda masukkan salah!');
        }

        $user->password = $request->input('password_baru');
        $user->save();

        return redirect()->route('dashboard')->with('flash_success', 'Password berhasil diubah!');
    }

    /**
     * Handle edit profile (name, email, password) for authenticated user (e.g. Super Admin).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password_lama' => 'nullable',
            'password_baru' => 'nullable|min:6',
        ], [
            'nama.required' => 'Nama wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.unique' => 'Email sudah digunakan oleh user lain!',
            'password_baru.min' => 'Password baru minimal 6 karakter!',
        ]);

        // If user provides new password
        if (!empty($request->input('password_baru'))) {
            if (empty($request->input('password_lama'))) {
                return redirect()->route('dashboard')->with('flash_error', 'Password lama wajib diisi untuk mengubah password!');
            }

            if (!Hash::check($request->input('password_lama'), $user->password)) {
                return redirect()->route('dashboard')->with('flash_error', 'Password lama yang Anda masukkan salah!');
            }

            $user->password = $request->input('password_baru');
        }

        $user->nama = trim($request->input('nama'));
        $user->email = trim($request->input('email'));
        $user->save();

        return redirect()->route('dashboard')->with('flash_success', 'Profile berhasil diperbarui!');
    }
}
