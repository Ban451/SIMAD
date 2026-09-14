<?php

namespace App\Http\Controllers;

use App\Models\DokumenRequest;
use App\Models\KategoriDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DokumenRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori_dokumen,id',
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DokumenRequest::create([
                'siswa_id' => Auth::id(),
                'kategori_id' => $request->kategori_id,
                'judul' => $request->judul,
                'keterangan' => $request->keterangan,
                'status' => 'pending',
            ]);

            return redirect()->back()->with('success', 'Request dokumen berhasil dikirim!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengirim request: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $requests = DokumenRequest::where('siswa_id', Auth::id())
            ->with(['kategori'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('siswa.requests', compact('requests'));
    }
}