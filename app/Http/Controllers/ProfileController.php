<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{

    // Function untuk menampilkan halaman profil pengguna
    public function edit()
    {
        return Inertia::render('Profile');
    }

    // Function untuk mengupdate profil pengguna
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->getKey()],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah digunakan.',
        ]);

        $user->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diupdate.',
                'user' => $user,
            ]);
        }

        return back()->with('success', 'Profil berhasil diupdate.');
    }

    // Function untuk mengupdate password pengguna
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*()_\-+=\[\]{};:\'",.<>\/?\\|`~]/',
            ],
            'new_password_confirmation' => ['required', 'string'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal harus 8 karakter.',
            'new_password.regex' => 'Password baru harus mengandung huruf besar, huruf kecil, angka, dan simbol khusus.',
            'new_password_confirmation.required' => 'Konfirmasi password baru wajib diisi.',
        ], [
            'new_password.regex' => 'Password baru harus mengandung huruf besar, huruf kecil, angka, dan simbol khusus.',
        ]);

        // Cek password saat ini
        if (!Hash::check($validated['current_password'], $user->password)) {
            $message = 'Password yang diisi tidak sama dengan password saat ini.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'current_password' => [$message],
                    ],
                ], 422);
            }
            throw ValidationException::withMessages([
                'current_password' => $message,
            ]);
        }

        // Password baru tidak boleh sama dengan password saat ini
        if (Hash::check($validated['new_password'], $user->password)) {
            $message = 'Password baru tidak boleh sama dengan password saat ini.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'new_password' => [$message],
                    ],
                ], 422);
            }
            throw ValidationException::withMessages([
                'new_password' => $message,
            ]);
        }

        // Konfirmasi password baru harus sama
        if ($validated['new_password'] !== $validated['new_password_confirmation']) {
            $message = 'Konfirmasi password baru tidak cocok.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'new_password_confirmation' => [$message],
                    ],
                ], 422);
            }
            throw ValidationException::withMessages([
                'new_password_confirmation' => $message,
            ]);
        }

        // Cek password tidak mengandung nama/email/username
        $forbidden = [
            strtolower($user->name),
            strtolower($user->email),
            strtolower(explode('@', $user->email)[0]),
        ];
        $newPasswordLower = strtolower($validated['new_password']);
        foreach ($forbidden as $item) {
            if ($item && strlen($item) >= 3 && strpos($newPasswordLower, $item) !== false) {
                $message = 'Password baru tidak boleh mengandung nama, email, atau username Anda.';
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'new_password' => [$message],
                        ],
                    ], 422);
                }
                throw ValidationException::withMessages([
                    'new_password' => $message,
                ]);
            }
        }

        // Cek password tidak menggunakan pola berulang sederhana
        $commonPatterns = [
            '12345678',
            '123456789',
            'password',
            'qwerty',
            '111111',
            '123123',
            'abc123',
            'qwertyuiop'
        ];
        foreach ($commonPatterns as $pattern) {
            if (stripos($newPasswordLower, $pattern) !== false) {
                $message = 'Password baru terlalu mudah ditebak atau menggunakan pola berulang.';
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'new_password' => [$message],
                        ],
                    ], 422);
                }
                throw ValidationException::withMessages([
                    'new_password' => $message,
                ]);
            }
        }

        $user = $request->user();

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diupdate.',
            ]);
        }

        return back()->with('success', 'Password berhasil diupdate.');
    }
}