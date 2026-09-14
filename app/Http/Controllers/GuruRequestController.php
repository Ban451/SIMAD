<?php

namespace App\Http\Controllers;

use App\Models\DokumenRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruRequestController extends Controller
{
    public function process(Request $request, $id)
    {
        $req = DokumenRequest::with('siswa')->findOrFail($id);
        
        // Validasi akses guru
        $user = Auth::user();
        if ($req->siswa->kelas_id != $user->kelas_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke request ini.'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'alasan' => 'nullable|string',
        ]);

        try {
            $req->update([
                'status' => $request->status,
                'alasan' => $request->alasan,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            $message = $request->status == 'approved' 
                ? 'Request berhasil disetujui!' 
                : 'Request ditolak.';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function detail($id)
    {
        $req = DokumenRequest::with('siswa')->findOrFail($id);
        
        $user = Auth::user();
        if ($req->siswa->kelas_id != $user->kelas_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke request ini.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'request' => [
                'id' => $req->id,
                'judul' => $req->judul,
                'kategori_id' => $req->kategori_id,
                'siswa_id' => $req->siswa_id,
                'keterangan' => $req->keterangan,
            ]
        ]);
    }
}