<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KelasController extends Controller
{
    /**
     * Display a listing of kelas.
     */
    public function index()
    {
        $kelas = Kelas::with(['siswa', 'waliKelas'])
            ->withCount('siswa')
            ->orderBy('nama_kelas')
            ->paginate(10);

        return view('kelas.index', compact('kelas'));
    }

    /**
     * Show form create kelas.
     */
    public function create()
    {
        return view('kelas.create');
    }

    /**
     * Store kelas via AJAX.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas',
            'tingkat'    => 'required|string|in:X,XI,XII',
            'jurusan'    => 'required|string|max:100',
        ], [
            'nama_kelas.unique' => 'Nama kelas sudah ada.',
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'tingkat.required' => 'Tingkat wajib dipilih.',
            'tingkat.in' => 'Tingkat harus X, XI, atau XII.',
            'jurusan.required' => 'Jurusan wajib dipilih.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $kelas = Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'tingkat'    => $request->tingkat,
            'jurusan'    => $request->jurusan,
        ]);

        $kelas->loadCount('siswa');

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil ditambahkan.',
            'kelas'   => $kelas,
        ]);
    }

    /**
     * Show form edit kelas.
     */
    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        return view('kelas.edit', compact('kelas'));
    }

    /**
     * Update kelas via AJAX.
     */
    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas,' . $id,
            'tingkat'    => 'required|string|in:X,XI,XII',
            'jurusan'    => 'required|string|max:100',
        ], [
            'nama_kelas.unique' => 'Nama kelas sudah dipakai.',
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'tingkat.required' => 'Tingkat wajib dipilih.',
            'tingkat.in' => 'Tingkat harus X, XI, atau XII.',
            'jurusan.required' => 'Jurusan wajib dipilih.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'tingkat'    => $request->tingkat,
            'jurusan'    => $request->jurusan,
        ]);

        $kelas->loadCount('siswa');

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil diperbarui.',
            'kelas'   => $kelas,
        ]);
    }

    /**
     * Delete kelas via AJAX.
     */
    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);

        if ($kelas->siswa()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak dapat dihapus karena masih memiliki siswa.'
            ], 422);
        }

        if ($kelas->users()->where('role', 'guru')->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak dapat dihapus karena masih ada wali kelas.'
            ], 422);
        }

        $kelas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil dihapus.'
        ]);
    }
}