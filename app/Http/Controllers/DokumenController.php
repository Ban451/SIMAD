<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\User;
use App\Models\KategoriDokumen;
use App\Models\DokumenRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

class DokumenController extends Controller
{
    // ========================================
    // METHOD UNTUK ADMIN
    // ========================================

    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') abort(403, 'Hanya admin yang dapat mengakses halaman ini.');

        $query = Dokumen::with(['siswa.kelas', 'kategori', 'uploader']);
        if ($request->has('kategori') && $request->kategori) $query->where('kategori_id', $request->kategori);
        if ($request->has('siswa') && $request->siswa) $query->where('siswa_id', $request->siswa);

        $dokumen = $query->orderBy('created_at', 'desc')->paginate(15);
        $kategori = KategoriDokumen::all();
        $siswa = User::where('role', 'siswa')->with('kelas')->get();

        return view('dokumen.index', compact('dokumen', 'kategori', 'siswa'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'siswa') return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);

        $request->validate([
            'judul' => 'required|string|max:255',
            'siswa_id' => 'required|exists:users,id',
            'kategori_id' => 'required|exists:kategori_dokumen,id',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string',
            'status' => 'nullable|in:aktif,pending',
        ]);

        if ($user->role === 'guru') {
            $siswa = User::findOrFail($request->siswa_id);
            if ($siswa->kelas_id != $user->kelas_id) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak ke siswa ini.'], 403);
            }
        }

        try {
            $file = $request->file('file');
            
            if (!$file->isValid() || $file->getSize() == 0) {
                return response()->json(['success' => false, 'message' => 'File kosong atau gagal diunggah.'], 400);
            }

            // UBAH FILE MENJADI BASE64
            $fileData = base64_encode(file_get_contents($file->getRealPath()));

            $dokumen = Dokumen::create([
                'judul' => $request->judul,
                'file_path' => $fileData,
                'nama_file' => $file->getClientOriginalName(),
                'keterangan' => $request->keterangan,
                'siswa_id' => $request->siswa_id,
                'kategori_id' => $request->kategori_id,
                'uploaded_by' => $user->id,
                'status' => $request->status ?? 'pending',
            ]);

            $dokumen->load(['siswa.kelas', 'kategori', 'uploader']);

            return response()->json(['success' => true, 'message' => 'Dokumen berhasil diunggah.', 'dokumen' => $dokumen]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal upload: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $dokumen = Dokumen::findOrFail($id);

        if ($user->role === 'siswa') return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        if ($user->role === 'guru' && $dokumen->siswa->kelas_id != $user->kelas_id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $dokumen->delete();
        return response()->json(['success' => true, 'message' => 'Dokumen berhasil dihapus.']);
    }

    // ========================================
    // METHOD UNTUK GURU
    // ========================================

    public function guruStore(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_dokumen,id',
            'siswa_id' => 'required|exists:users,id',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'status' => 'nullable|in:aktif,pending',
            'keterangan' => 'nullable|string',
            'request_id' => 'nullable|exists:dokumen_requests,id',
        ]);

        try {
            $siswa = User::findOrFail($request->siswa_id);
            if ($siswa->kelas_id != $user->kelas_id) {
                return response()->json(['success' => false, 'message' => 'Siswa tidak berada di kelas anda.'], 403);
            }

            $file = $request->file('file');

            if (!$file->isValid() || $file->getSize() == 0) {
                return response()->json(['success' => false, 'message' => 'File kosong atau gagal diunggah.'], 400);
            }

            // UBAH FILE MENJADI BASE64
            $fileData = base64_encode(file_get_contents($file->getRealPath()));

            $dokumen = Dokumen::create([
                'judul' => $request->judul,
                'kategori_id' => $request->kategori_id,
                'siswa_id' => $request->siswa_id,
                'uploaded_by' => $user->id,
                'file_path' => $fileData,
                'nama_file' => $file->getClientOriginalName(),
                'status' => $request->status ?? 'pending',
                'keterangan' => $request->keterangan,
            ]);

            if ($request->filled('request_id')) {
                $req = DokumenRequest::find($request->request_id);
                if ($req && $req->siswa_id == $request->siswa_id) {
                    $req->update(['status' => 'approved', 'processed_at' => now(), 'processed_by' => $user->id]);
                }
            }

            $dokumen->load(['siswa.kelas', 'kategori', 'uploader']);
            return response()->json(['success' => true, 'message' => 'Dokumen berhasil diupload!', 'dokumen' => $dokumen]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal upload: ' . $e->getMessage()], 500);
        }
    }

    public function guruDestroy($id)
    {
        $user = Auth::user();
        $dokumen = Dokumen::with('siswa')->findOrFail($id);

        if ($dokumen->siswa->kelas_id != $user->kelas_id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $dokumen->delete();
        return response()->json(['success' => true, 'message' => 'Dokumen berhasil dihapus!']);
    }

    // ========================================
    // FUNGSI BANTUAN: KONVERSI GAMBAR KE PDF (FIX DATA URI)
    // ========================================
    private function generatePdfFromBase64($base64Data, $fileName)
    {
        // 1. Tentukan tipe MIME gambar (default PNG)
        $mimeType = 'image/png';
        if (strpos(strtolower($fileName), '.jpg') !== false || strpos(strtolower($fileName), '.jpeg') !== false) {
            $mimeType = 'image/jpeg';
        }

        // 2. Buat HTML menggunakan Data URI (Tidak perlu file sementara)
        // Ini akan mencegah gambar "kotak silang" (broken image) di DomPDF
        $html = '
            <html>
            <head>
                <style>
                    @page { margin: 0; }
                    body { margin: 0; padding: 0; text-align: center; background: white; }
                    img { max-width: 100%; height: auto; display: block; margin: 0 auto; }
                </style>
            </head>
            <body>
                <img src="data:' . $mimeType . ';base64,' . $base64Data . '">
            </body>
            </html>
        ';

        // 3. Generate PDF
        $pdf = Pdf::loadHTML($html);
        
        // 4. Return PDF (ubah ekstensi nama file menjadi .pdf)
        $pdfName = pathinfo($fileName, PATHINFO_FILENAME) . '.pdf';
        return $pdf->download($pdfName);
    }

    // ========================================
    // METHOD PREVIEW & DOWNLOAD (BASE64)
    // ========================================

    public function download($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        if (empty($dokumen->file_path) || strlen($dokumen->file_path) < 50) {
            abort(404, 'File dokumen tidak valid atau rusak.');
        }

        $ext = strtolower(pathinfo($dokumen->nama_file, PATHINFO_EXTENSION));

        // Jika Gambar, ubah ke PDF
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return $this->generatePdfFromBase64($dokumen->file_path, $dokumen->nama_file);
        }

        // Jika PDF/DOCX, download langsung
        $fileData = base64_decode($dokumen->file_path);
        return response($fileData)->header('Content-Disposition', 'attachment; filename="' . $dokumen->nama_file . '"');
    }

    public function guruDownload($id)
    {
        $user = Auth::user();
        $dokumen = Dokumen::with('siswa')->findOrFail($id);
        if ($dokumen->siswa->kelas_id != $user->kelas_id) abort(403, 'Akses ditolak.');

        if (empty($dokumen->file_path) || strlen($dokumen->file_path) < 50) {
            abort(404, 'File dokumen tidak valid atau rusak.');
        }

        $ext = strtolower(pathinfo($dokumen->nama_file, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return $this->generatePdfFromBase64($dokumen->file_path, $dokumen->nama_file);
        }

        $fileData = base64_decode($dokumen->file_path);
        return response($fileData)->header('Content-Disposition', 'attachment; filename="' . $dokumen->nama_file . '"');
    }

    public function downloadSiswa($id)
    {
        $dokumen = Dokumen::where('id', $id)->where('siswa_id', Auth::id())->firstOrFail();

        if (empty($dokumen->file_path) || strlen($dokumen->file_path) < 50) {
            abort(404, 'File dokumen tidak valid atau rusak.');
        }

        $ext = strtolower(pathinfo($dokumen->nama_file, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return $this->generatePdfFromBase64($dokumen->file_path, $dokumen->nama_file);
        }

        $fileData = base64_decode($dokumen->file_path);
        return response($fileData)->header('Content-Disposition', 'attachment; filename="' . $dokumen->nama_file . '"');
    }

    public function preview($id)
    {
        $dokumen = Dokumen::findOrFail($id);

        if (empty($dokumen->file_path) || strlen($dokumen->file_path) < 50) {
            abort(404, 'File dokumen tidak valid atau rusak.');
        }

        $fileData = base64_decode($dokumen->file_path);
        
        $finfo = finfo_open();
        $mimeType = finfo_buffer($finfo, $fileData, FILEINFO_MIME_TYPE);
        
        return response($fileData)->header('Content-Type', $mimeType);
    }

    public function guruPreview($id)
    {
        $user = Auth::user();
        $dokumen = Dokumen::with('siswa')->findOrFail($id);
        if ($dokumen->siswa->kelas_id != $user->kelas_id) abort(403, 'Akses ditolak.');

        if (empty($dokumen->file_path) || strlen($dokumen->file_path) < 50) {
            abort(404, 'File dokumen tidak valid atau rusak.');
        }

        $fileData = base64_decode($dokumen->file_path);
        
        $finfo = finfo_open();
        $mimeType = finfo_buffer($finfo, $fileData, FILEINFO_MIME_TYPE);
        
        return response($fileData)->header('Content-Type', $mimeType);
    }

    public function previewSiswa($id)
    {
        $dokumen = Dokumen::where('id', $id)->where('siswa_id', Auth::id())->firstOrFail();

        if (empty($dokumen->file_path) || strlen($dokumen->file_path) < 50) {
            abort(404, 'File dokumen tidak valid atau rusak.');
        }

        $fileData = base64_decode($dokumen->file_path);
        
        $finfo = finfo_open();
        $mimeType = finfo_buffer($finfo, $fileData, FILEINFO_MIME_TYPE);
        
        return response($fileData)->header('Content-Type', $mimeType);
    }
}