<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        // Ambil 5 dokumen terbaru untuk ditampilkan di landing page
        $recentDocuments = Dokumen::with(['siswa', 'kategori'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return view('landing', compact('recentDocuments'));
    }
}