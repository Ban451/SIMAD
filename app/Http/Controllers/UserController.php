<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('kelas')->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        return view('users.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nama'     => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:admin,guru,siswa',
        ];

        if ($request->role === 'siswa') {
            $rules['nis']      = 'required|string|max:20|unique:users,nis';
            $rules['kelas_id'] = 'required|exists:kelas,id';
        } else {
            $rules['email']    = 'required|email|max:255|unique:users,email';
            $rules['kelas_id'] = 'nullable|exists:kelas,id';
        }

        $validator = Validator::make($request->all(), $rules, [
            'nis.unique'      => 'NIS sudah terdaftar.',
            'email.unique'    => 'Email sudah terdaftar.',
            'kelas_id.exists' => 'Kelas tidak ditemukan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $payload = [
                'nama'     => $request->nama,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
                'kelas_id' => $request->filled('kelas_id') ? $request->kelas_id : null,
            ];

            if ($request->role === 'siswa') {
                $payload['nis'] = $request->nis;
            } else {
                $payload['email'] = $request->email;
            }

            User::create($payload);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan user: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(User $user)
    {
        $user->load(['kelas', 'dokumen']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        return view('users.edit', compact('user', 'kelas'));
    }

    /**
     * Update user — dipakai untuk arahkan / lepas kelas.
     *
     * PERBAIKAN:
     * - Normalisasi "" → null supaya bisa "lepas dari kelas"
     * - Cek konflik wali kelas: 1 kelas hanya boleh 1 guru
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Normalisasi: "" atau tidak dikirim → null (lepas kelas)
        $kelasId = $request->filled('kelas_id') ? $request->kelas_id : null;

        try {
            // Cegah 1 kelas punya 2 wali kelas
            if ($kelasId && $user->role === 'guru') {
                $konflik = User::where('role', 'guru')
                    ->where('kelas_id', $kelasId)
                    ->where('id', '!=', $user->id)
                    ->first();

                if ($konflik) {
                    return response()->json([
                        'success' => false,
                        'message' => "Kelas ini sudah memiliki wali kelas ({$konflik->nama}). Lepas dulu wali kelas sebelumnya.",
                    ], 422);
                }
            }

            $user->update(['kelas_id' => $kelasId]);

            $message = $kelasId
                ? "Guru berhasil diarahkan ke kelas."
                : "Guru berhasil dilepas dari kelas.";

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui user: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: ' . $e->getMessage(),
            ], 500);
        }
    }
}