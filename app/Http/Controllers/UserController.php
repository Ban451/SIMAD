<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('kelas')->orderBy('role')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        return view('users.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users',
            'nis' => 'nullable|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,guru,siswa',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        // Validasi: jika siswa, NIS wajib diisi
        if ($request->role == 'siswa' && !$request->nis) {
            return back()->withErrors(['nis' => 'NIS wajib diisi untuk siswa.']);
        }

        // Validasi: jika guru, kelas_id wajib diisi
        if ($request->role == 'guru' && !$request->kelas_id) {
            return back()->withErrors(['kelas_id' => 'Kelas wajib dipilih untuk guru.']);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nis' => $request->nis,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'kelas_id' => $request->kelas_id,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $kelas = Kelas::all();
        return view('users.edit', compact('user', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'nis' => 'nullable|unique:users,nis,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,guru,siswa',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'nis' => $request->nis,
            'role' => $request->role,
            'kelas_id' => $request->kelas_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id == auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}