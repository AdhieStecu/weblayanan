<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registrasi pengguna / mahasiswa baru.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['nullable', 'string', Rule::in(['admin', 'user', 'mahasiswa'])],
            'nim' => ['nullable', 'string', 'max:30', 'unique:users,nim'],
            'program_studi' => ['nullable', 'string', 'max:100'],
            'semester' => ['nullable', 'integer', 'min:1', 'max:14'],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'avatar_url' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'nim.unique' => 'NIM ini sudah terdaftar.',
        ]);

        $role = ($validated['role'] ?? 'mahasiswa') === 'admin' ? 'admin' : 'mahasiswa';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'nim' => $validated['nim'] ?? null,
            'program_studi' => $validated['program_studi'] ?? null,
            'semester' => $validated['semester'] ?? 1,
            'no_hp' => $validated['no_hp'] ?? null,
            'avatar_url' => $validated['avatar_url'] ?? null,
            'role' => $role,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi akun mahasiswa berhasil!',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Login menggunakan Email atau NIM + Password.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'identifier.required' => 'Email atau NIM wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $identifier = trim($validated['identifier']);

        // Cari berdasarkan email atau NIM
        $user = User::where('email', $identifier)
            ->orWhere('nim', $identifier)
            ->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email/NIM atau password yang dimasukkan salah.',
            ], 422);
        }

        // Buat token akses personal Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil! Selamat datang, ' . $user->name,
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    /**
     * Ambil data profil user saat ini.
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user(),
        ]);
    }

    /**
     * Update profil pengguna yang sedang login.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nim' => ['nullable', 'string', 'max:30', Rule::unique('users')->ignore($user->id)],
            'program_studi' => ['nullable', 'string', 'max:100'],
            'semester' => ['nullable', 'integer', 'min:1', 'max:14'],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'avatar_url' => ['nullable', 'string', 'max:255'],
            'current_password' => ['nullable', 'string'],
            'new_password' => ['nullable', 'string', 'min:6'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah dipakai oleh akun lain.',
            'nim.unique' => 'NIM ini sudah terdaftar pada akun lain.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
        ]);

        // Cek jika user ingin mengganti password
        if (! empty($validated['new_password'])) {
            if (empty($validated['current_password'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Password lama wajib diisi untuk mengubah password.',
                    'errors' => ['current_password' => ['Password saat ini wajib diisi.']],
                ], 422);
            }

            if (! Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Password lama tidak sesuai.',
                    'errors' => ['current_password' => ['Password lama tidak cocok.']],
                ], 422);
            }

            $user->password = Hash::make($validated['new_password']);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (array_key_exists('nim', $validated)) {
            $user->nim = $validated['nim'];
        }
        if (array_key_exists('program_studi', $validated)) {
            $user->program_studi = $validated['program_studi'];
        }
        if (array_key_exists('semester', $validated)) {
            $user->semester = $validated['semester'];
        }
        if (array_key_exists('no_hp', $validated)) {
            $user->no_hp = $validated['no_hp'];
        }
        if (array_key_exists('avatar_url', $validated)) {
            $user->avatar_url = $validated['avatar_url'];
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui!',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * Logout dan revokasi token akses.
     */
    public function logout(Request $request): JsonResponse
    {
        // Hapus token saat ini
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil. Sesi telah diakhiri.',
        ]);
    }

    /**
     * Ambil daftar seluruh user (Khusus Administrator).
     */
    public function users(Request $request): JsonResponse
    {
        $query = User::select('id', 'name', 'email', 'nim', 'role', 'program_studi', 'semester', 'no_hp', 'created_at');

        if ($request->filled('role') && $request->query('role') !== 'semua') {
            $query->where('role', $request->query('role'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('program_studi', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $users,
            'total' => $users->count(),
        ]);
    }
}
