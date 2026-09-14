<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\User;
use App\Models\KategoriDokumen;
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

        $query = Dokumen::with(['siswa', 'kategori', 'uploader']);

        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori_id', $request->kategori);
        }

        if ($request->has('siswa') && $request->siswa) {
            $query->where('siswa_id', $request->siswa);
        }

        $dokumen = $query->orderBy('created_at', 'desc')->paginate(15);
        $kategori = KategoriDokumen::all();
        $siswa = User::where('role', 'siswa')->get();

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
            abort(403, 'Siswa tidak dapat menambah dokumen.');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'siswa_id' => 'required|exists:users,id',
            'kategori_id' => 'required|exists:kategori_dokumen,id',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'semester' => 'nullable|string',
            'tahun_ajaran' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'status' => 'nullable|in:aktif,pending',
        ]);

        if ($user->role === 'guru') {
            $siswa = User::findOrFail($request->siswa_id);
            if ($siswa->kelas_id != $user->kelas_id) {
                abort(403, 'Anda tidak memiliki akses ke siswa ini.');
            }
        }

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('dokumen', $fileName, 'public');

            Dokumen::create([
                'judul' => $request->judul,
                'file_path' => $filePath,
                'nama_file' => $file->getClientOriginalName(),
                'keterangan' => $request->keterangan,
                'siswa_id' => $request->siswa_id,
                'kategori_id' => $request->kategori_id,
                'uploaded_by' => $user->id,
                'semester' => $request->semester,
                'tahun_ajaran' => $request->tahun_ajaran,
                'status' => $request->status ?? 'pending',
            ]);

            return redirect()->route('dokumen.index')
                ->with('success', 'Dokumen berhasil diunggah.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal upload: ' . $e->getMessage())
                ->withInput();
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
            abort(403, 'Siswa tidak dapat mengubah dokumen.');
        }

        if ($user->role === 'guru') {
            if ($dokumen->siswa->kelas_id != $user->kelas_id) {
                abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
            }
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_dokumen,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'semester' => 'nullable|string',
            'tahun_ajaran' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'status' => 'nullable|in:aktif,pending',
        ]);

        try {
            $data = [
                'judul' => $request->judul,
                'kategori_id' => $request->kategori_id,
                'semester' => $request->semester,
                'tahun_ajaran' => $request->tahun_ajaran,
                'keterangan' => $request->keterangan,
                'status' => $request->status ?? $dokumen->status,
            ];

            if ($request->hasFile('file')) {
                if (Storage::disk('public')->exists($dokumen->file_path)) {
                    Storage::disk('public')->delete($dokumen->file_path);
                }

                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('dokumen', $fileName, 'public');

                $data['file_path'] = $filePath;
                $data['nama_file'] = $file->getClientOriginalName();
            }

            $dokumen->update($data);

            return redirect()->route('dokumen.index')
                ->with('success', 'Dokumen berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $dokumen = Dokumen::with('siswa')->findOrFail($id);

        if ($user->role === 'siswa') {
            abort(403, 'Siswa tidak dapat menghapus dokumen.');
        }

        if ($user->role === 'guru') {
            if ($dokumen->siswa->kelas_id != $user->kelas_id) {
                abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
            }
        }

        try {
            if (Storage::disk('public')->exists($dokumen->file_path)) {
                Storage::disk('public')->delete($dokumen->file_path);
            }
            
            $dokumen->delete();

            return redirect()->route('dokumen.index')
                ->with('success', 'Dokumen berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal hapus: ' . $e->getMessage());
        }
    }

    // ========================================
    // METHOD UNTUK GURU
    // ========================================

    public function guruIndex()
    {
        $user = Auth::user();
        $kelasId = $user->kelas_id;
        
        $dokumen = Dokumen::with(['siswa', 'kategori', 'uploader'])
            ->whereHas('siswa', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $siswa = User::where('kelas_id', $kelasId)
            ->where('role', 'siswa')
            ->get();
            
        $kategori = KategoriDokumen::all();
        
        return view('dashboard-guru', compact('dokumen', 'siswa', 'kategori'));
    }

    public function guruStore(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_dokumen,id',
            'siswa_id' => 'required|exists:users,id',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'status' => 'nullable|in:aktif,pending',
        ]);

        try {
            $siswa = User::findOrFail($request->siswa_id);
            if ($siswa->kelas_id != $user->kelas_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak berada di kelas anda.'
                ], 403);
            }

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('dokumen', $filename, 'public');

            Dokumen::create([
                'judul' => $request->judul,
                'kategori_id' => $request->kategori_id,
                'siswa_id' => $request->siswa_id,
                'uploaded_by' => $user->id,
                'file_path' => $path,
                'nama_file' => $file->getClientOriginalName(),
                'status' => $request->status ?? 'pending',
                'keterangan' => $request->keterangan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diupload!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload: ' . $e->getMessage()
            ], 500);
        }
    }

    public function guruUpdate(Request $request, $id)
    {
        $user = Auth::user();
        $dokumen = Dokumen::with('siswa')->findOrFail($id);

        if ($dokumen->siswa->kelas_id != $user->kelas_id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }
        
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_dokumen,id',
            'status' => 'nullable|in:aktif,pending',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $dokumen->update([
                'judul' => $request->judul,
                'kategori_id' => $request->kategori_id,
                'status' => $request->status ?? $dokumen->status,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->back()->with('success', 'Dokumen berhasil diupdate!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function guruDestroy($id)
    {
        $user = Auth::user();
        $dokumen = Dokumen::with('siswa')->findOrFail($id);

        if ($dokumen->siswa->kelas_id != $user->kelas_id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        try {
            if (Storage::disk('public')->exists($dokumen->file_path)) {
                Storage::disk('public')->delete($dokumen->file_path);
            }
            
            $dokumen->delete();

            return redirect()->back()->with('success', 'Dokumen berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal hapus: ' . $e->getMessage());
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
    // METHOD DOWNLOAD (Per Role)
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

            return response()->download($filePath, $dokumen->nama_file);

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

            return response()->download($filePath, $dokumen->nama_file);

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

            return response()->download($filePath, $dokumen->nama_file);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal download: ' . $e->getMessage());
        }
    }

    // ========================================
    // METHOD PREVIEW (Lihat Dokumen)
    // ========================================

    /**
     * Preview dokumen (Admin)
     */
    public function preview($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        $filePath = storage_path('app/public/' . $dokumen->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file($filePath);
    }

    /**
     * Preview dokumen (Guru)
     */
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

    /**
     * Preview dokumen (Siswa - hanya milik sendiri)
     */
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