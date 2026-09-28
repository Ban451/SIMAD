<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::with('kelas')->orderBy('role')->paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Show form create user.
     */
    public function create()
    {
        $kelas = Kelas::all();
        return view('users.create', compact('kelas'));
    }

    /**
     * Store user via AJAX (guru / siswa / admin).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'     => 'required|string|max:255',
            'email'    => 'nullable|email|unique:users,email',
            'nis'      => 'nullable|unique:users,nis',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:admin,guru,siswa',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Validasi tambahan: siswa wajib NIS
        if ($request->role === 'siswa' && !$request->nis) {
            return response()->json([
                'success' => false,
                'message' => 'NIS wajib diisi untuk siswa.'
            ], 422);
        }

        // Validasi tambahan: guru wajib kelas
        if ($request->role === 'guru' && !$request->kelas_id) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas wajib dipilih untuk guru.'
            ], 422);
        }

        $user = User::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'nis'      => $request->nis,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'kelas_id' => $request->kelas_id,
        ]);

        $user->load('kelas');

        return response()->json([
            'success' => true,
            'message' => 'Data ' . $request->role . ' berhasil disimpan.',
            'user'    => $user
        ]);
    }

    /**
     * Show form edit user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $kelas = Kelas::all();
        return view('users.edit', compact('user', 'kelas'));
    }

    /**
     * Update user via AJAX.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama'     => 'required|string|max:255',
            'email'    => 'nullable|email|unique:users,email,' . $id,
            'nis'      => 'nullable|unique:users,nis,' . $id,
            'password' => 'nullable|string|min:8',
            'role'     => 'required|in:admin,guru,siswa',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = [
            'nama'     => $request->nama,
            'email'    => $request->email,
            'nis'      => $request->nis,
            'role'     => $request->role,
            'kelas_id' => $request->kelas_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->load('kelas');

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui.',
            'user'    => $user
        ]);
    }

    /**
     * Delete user via AJAX.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id == Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus akun sendiri.'
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus.'
        ]);
    }

    /**
     * Guru: lihat siswa di kelas yang diampu.
     */
    public function guruSiswa()
    {
        $guru = Auth::user();
        $siswa = User::where('role', 'siswa')
            ->where('kelas_id', $guru->kelas_id)
            ->with('kelas')
            ->paginate(15);

        return view('guru.siswa', compact('siswa'));
    }
}