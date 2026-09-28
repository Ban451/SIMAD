<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\User;
use App\Models\KategoriDokumen;
use App\Models\DokumenRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    // ========================================
    // METHOD UNTUK ADMIN (Full Access)
    // ========================================

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat mengakses halaman ini.');
        }

        $query = Dokumen::with(['siswa.kelas', 'kategori', 'uploader']);

        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori_id', $request->kategori);
        }

        if ($request->has('siswa') && $request->siswa) {
            $query->where('siswa_id', $request->siswa);
        }

        $dokumen = $query->orderBy('created_at', 'desc')->paginate(15);
        $kategori = KategoriDokumen::all();
        $siswa = User::where('role', 'siswa')->with('kelas')->get();

        return view('dokumen.index', compact('dokumen', 'kategori', 'siswa'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role === 'siswa') {
            abort(403, 'Siswa tidak dapat menambah dokumen.');
        }

        $kategori = KategoriDokumen::all();

        if ($user->role === 'guru') {
            $siswa = User::where('role', 'siswa')
                ->where('kelas_id', $user->kelas_id)
                ->get();
        } else {
            $siswa = User::where('role', 'siswa')->get();
        }

        return view('dokumen.create', compact('kategori', 'siswa'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'siswa') {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak dapat menambah dokumen.'
            ], 403);
        }

        $request->validate([
            'judul'        => 'required|string|max:255',
            'siswa_id'     => 'required|exists:users,id',
            'kategori_id'  => 'required|exists:kategori_dokumen,id',
            'file'         => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'semester'     => 'nullable|string',
            'tahun_ajaran' => 'nullable|string',
            'keterangan'   => 'nullable|string',
            'status'       => 'nullable|in:aktif,pending',
        ]);

        if ($user->role === 'guru') {
            $siswa = User::findOrFail($request->siswa_id);
            if ($siswa->kelas_id != $user->kelas_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke siswa ini.'
                ], 403);
            }
        }

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('dokumen', $fileName, 'public');

            $dokumen = Dokumen::create([
                'judul'        => $request->judul,
                'file_path'    => $filePath,
                'file_name'    => $file->getClientOriginalName(),
                'file_size'    => $file->getSize(),
                'keterangan'   => $request->keterangan,
                'siswa_id'     => $request->siswa_id,
                'kategori_id'  => $request->kategori_id,
                'uploaded_by'  => $user->id,
                'semester'     => $request->semester,
                'tahun_ajaran' => $request->tahun_ajaran,
                'status'       => $request->status ?? 'pending',
            ]);

            $dokumen->load(['siswa.kelas', 'kategori', 'uploader']);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diunggah.',
                'dokumen' => $dokumen
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $dokumen = Dokumen::with('siswa')->findOrFail($id);

        if ($user->role === 'siswa') {
            abort(403, 'Siswa tidak dapat mengubah dokumen.');
        }

        if ($user->role === 'guru') {
            if ($dokumen->siswa->kelas_id != $user->kelas_id) {
                abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
            }
        }

        $kategori = KategoriDokumen::all();
        $siswa = $user->role === 'admin' ? User::where('role', 'siswa')->get() : [];

        return view('dokumen.edit', compact('dokumen', 'kategori', 'siswa'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $dokumen = Dokumen::with('siswa')->findOrFail($id);

        if ($user->role === 'siswa') {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak dapat mengubah dokumen.'
            ], 403);
        }

        if ($user->role === 'guru') {
            if ($dokumen->siswa->kelas_id != $user->kelas_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke dokumen ini.'
                ], 403);
            }
        }

        $request->validate([
            'judul'        => 'required|string|max:255',
            'kategori_id'  => 'required|exists:kategori_dokumen,id',
            'file'         => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'semester'     => 'nullable|string',
            'tahun_ajaran' => 'nullable|string',
            'keterangan'   => 'nullable|string',
            'status'       => 'nullable|in:aktif,pending',
        ]);

        try {
            $data = [
                'judul'        => $request->judul,
                'kategori_id'  => $request->kategori_id,
                'semester'     => $request->semester,
                'tahun_ajaran' => $request->tahun_ajaran,
                'keterangan'   => $request->keterangan,
                'status'       => $request->status ?? $dokumen->status,
            ];

            if ($request->hasFile('file')) {
                if (Storage::disk('public')->exists($dokumen->file_path)) {
                    Storage::disk('public')->delete($dokumen->file_path);
                }

                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('dokumen', $fileName, 'public');

                $data['file_path'] = $filePath;
                $data['file_name'] = $file->getClientOriginalName();
                $data['file_size'] = $file->getSize();
            }

            $dokumen->update($data);
            $dokumen->load(['siswa.kelas', 'kategori', 'uploader']);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diperbarui.',
                'dokumen' => $dokumen
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $dokumen = Dokumen::with('siswa')->findOrFail($id);

        if ($user->role === 'siswa') {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak dapat menghapus dokumen.'
            ], 403);
        }

        if ($user->role === 'guru') {
            if ($dokumen->siswa->kelas_id != $user->kelas_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke dokumen ini.'
                ], 403);
            }
        }

        try {
            if (Storage::disk('public')->exists($dokumen->file_path)) {
                Storage::disk('public')->delete($dokumen->file_path);
            }

            $dokumen->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal hapus: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // METHOD UNTUK GURU
    // ========================================

    public function guruIndex()
    {
        $user = Auth::user();
        $kelasId = $user->kelas_id;

        $dokumen = Dokumen::with(['siswa.kelas', 'kategori', 'uploader'])
            ->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $siswa = User::where('kelas_id', $kelasId)
            ->where('role', 'siswa')
            ->with('kelas')
            ->get();

        $kategori = KategoriDokumen::all();

        return view('dashboard-guru', compact('dokumen', 'siswa', 'kategori'));
    }

    public function guruCreate()
    {
        $user = Auth::user();
        $kelasId = $user->kelas_id;

        $siswa = User::where('kelas_id', $kelasId)
            ->where('role', 'siswa')
            ->get();

        $kategori = KategoriDokumen::all();

        return view('guru.dokumen.create', compact('siswa', 'kategori'));
    }

    public function guruStore(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'judul'        => 'required|string|max:255',
            'kategori_id'  => 'required|exists:kategori_dokumen,id',
            'siswa_id'     => 'required|exists:users,id',
            'file'         => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'status'       => 'nullable|in:aktif,pending',
            'keterangan'   => 'nullable|string',
            'request_id'   => 'nullable|exists:dokumen_requests,id',
        ]);

        try {
            // Validasi akses guru
            $siswa = User::findOrFail($request->siswa_id);
            if ($siswa->kelas_id != $user->kelas_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak berada di kelas anda.'
                ], 403);
            }

            // Upload file
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('dokumen', $fileName, 'public');

            // Simpan dokumen
            $dokumen = Dokumen::create([
                'judul'       => $request->judul,
                'kategori_id' => $request->kategori_id,
                'siswa_id'    => $request->siswa_id,
                'uploaded_by' => $user->id,
                'file_path'   => $filePath,
                'file_name'   => $file->getClientOriginalName(),
                'file_size'   => $file->getSize(),
                'status'      => $request->status ?? 'pending',
                'keterangan'  => $request->keterangan,
            ]);

            // Kalau upload untuk request, tandai request selesai
            if ($request->filled('request_id')) {
                $req = DokumenRequest::find($request->request_id);
                if ($req && $req->siswa_id == $request->siswa_id) {
                    $req->update([
                        'status'       => 'approved',
                        'processed_at' => now(),
                    ]);
                }
            }

            // Load relasi untuk response AJAX
            $dokumen->load(['siswa.kelas', 'kategori', 'uploader']);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diupload!',
                'dokumen' => $dokumen
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload: ' . $e->getMessage()
            ], 500);
        }
    }

    public function guruEdit($id)
    {
        $user = Auth::user();
        $dokumen = Dokumen::with('siswa')->findOrFail($id);

        if ($dokumen->siswa->kelas_id != $user->kelas_id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        $kategori = KategoriDokumen::all();
        $siswa = User::where('kelas_id', $user->kelas_id)
            ->where('role', 'siswa')
            ->get();

        return view('guru.dokumen.edit', compact('dokumen', 'kategori', 'siswa'));
    }

    public function guruUpdate(Request $request, $id)
    {
        $user = Auth::user();
        $dokumen = Dokumen::with('siswa')->findOrFail($id);

        if ($dokumen->siswa->kelas_id != $user->kelas_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke dokumen ini.'
            ], 403);
        }

        $request->validate([
            'judul'       => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_dokumen,id',
            'status'      => 'nullable|in:aktif,pending',
            'keterangan'  => 'nullable|string',
        ]);

        try {
            $dokumen->update([
                'judul'       => $request->judul,
                'kategori_id' => $request->kategori_id,
                'status'      => $request->status ?? $dokumen->status,
                'keterangan'  => $request->keterangan,
            ]);

            $dokumen->load(['siswa.kelas', 'kategori', 'uploader']);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diupdate!',
                'dokumen' => $dokumen
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update: ' . $e->getMessage()
            ], 500);
        }
    }

    public function guruDestroy($id)
    {
        $user = Auth::user();
        $dokumen = Dokumen::with('siswa')->findOrFail($id);

        if ($dokumen->siswa->kelas_id != $user->kelas_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke dokumen ini.'
            ], 403);
        }

        try {
            if (Storage::disk('public')->exists($dokumen->file_path)) {
                Storage::disk('public')->delete($dokumen->file_path);
            }

            $dokumen->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal hapus: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // METHOD UNTUK SISWA
    // ========================================

    public function indexSiswa()
    {
        $user = Auth::user();
        $dokumen = Dokumen::where('siswa_id', $user->id)
            ->with(['kategori', 'uploader'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dokumen.saya', compact('dokumen'));
    }

    // ========================================
    // METHOD DOWNLOAD
    // ========================================

    public function download($id)
    {
        try {
            $user = Auth::user();
            $dokumen = Dokumen::with('siswa')->findOrFail($id);

            if ($user->role === 'siswa' && $dokumen->siswa_id != $user->id) {
                abort(403, 'Anda hanya dapat mengunduh dokumen sendiri.');
            }

            if ($user->role === 'guru') {
                if ($dokumen->siswa->kelas_id != $user->kelas_id) {
                    abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
                }
            }

            $filePath = storage_path('app/public/' . $dokumen->file_path);

            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'File tidak ditemukan di server.');
            }

            return response()->download($filePath, $dokumen->file_name);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal download: ' . $e->getMessage());
        }
    }

    public function guruDownload($id)
    {
        try {
            $user = Auth::user();
            $dokumen = Dokumen::with('siswa')->findOrFail($id);

            if ($dokumen->siswa->kelas_id != $user->kelas_id) {
                abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
            }

            $filePath = storage_path('app/public/' . $dokumen->file_path);

            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'File tidak ditemukan!');
            }

            return response()->download($filePath, $dokumen->file_name);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal download: ' . $e->getMessage());
        }
    }

    public function downloadSiswa($id)
    {
        try {
            $dokumen = Dokumen::where('id', $id)
                ->where('siswa_id', Auth::id())
                ->firstOrFail();

            $filePath = storage_path('app/public/' . $dokumen->file_path);

            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'File tidak ditemukan!');
            }

            return response()->download($filePath, $dokumen->file_name);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal download: ' . $e->getMessage());
        }
    }

    // ========================================
    // METHOD PREVIEW
    // ========================================

    public function preview($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        $filePath = storage_path('app/public/' . $dokumen->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file($filePath);
    }

    public function guruPreview($id)
    {
        $user = Auth::user();
        $dokumen = Dokumen::with('siswa')->findOrFail($id);

        if ($dokumen->siswa->kelas_id != $user->kelas_id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        $filePath = storage_path('app/public/' . $dokumen->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file($filePath);
    }

    public function previewSiswa($id)
    {
        $dokumen = Dokumen::where('id', $id)
            ->where('siswa_id', Auth::id())
            ->firstOrFail();

        $filePath = storage_path('app/public/' . $dokumen->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file($filePath);
    }
}