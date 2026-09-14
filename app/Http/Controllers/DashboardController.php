<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\User;
use App\Models\Kelas;
use App\Models\KategoriDokumen;
use App\Models\DokumenRequest;  // ← TAMBAHKAN INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirect ke dashboard sesuai role
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Redirect ke dashboard sesuai role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isGuru()) {
            return redirect()->route('guru.dashboard');
        } elseif ($user->isSiswa()) {
            return redirect()->route('siswa.dashboard');
        }

        return redirect('/login');
    }

    /**
     * Dashboard Admin
     */
    public function adminDashboard()
    {
        $user = Auth::user();
        
        // Ambil semua data yang dibutuhkan untuk admin
        $data = [
            // Statistik
            'total_dokumen' => Dokumen::count(),
            'total_terverifikasi' => Dokumen::where('status', 'aktif')->count(),
            'total_menunggu' => Dokumen::where('status', 'pending')->count(),
            'total_siswa' => User::where('role', 'siswa')->count(),
            'total_guru' => User::where('role', 'guru')->count(),
            'total_kelas' => Kelas::count(),
            
            // Dokumen per kategori
            'dokumen_per_kategori' => Dokumen::selectRaw('kategori_id, count(*) as total')
                ->groupBy('kategori_id')
                ->with('kategori')
                ->get(),
            
            // Data dokumen dengan relasi
            'dokumen' => Dokumen::with(['siswa', 'siswa.kelas', 'kategori', 'uploader'])
                ->orderBy('created_at', 'desc')
                ->paginate(10),
            
            // Data siswa dengan relasi kelas dan dokumen
            'siswa' => User::where('role', 'siswa')
                ->with(['kelas', 'dokumen'])
                ->orderBy('nama')
                ->get(),
            
            // Data guru dengan relasi kelas
            'guru' => User::where('role', 'guru')
                ->with('kelas')
                ->orderBy('nama')
                ->get(),
            
            // Data kelas dengan relasi siswa dan waliKelas
            'kelas' => Kelas::with(['siswa', 'waliKelas'])
                ->orderBy('nama_kelas')
                ->get(),
        ];

        return view('dashboard-admin', compact('user', 'data'));
    }

    /**
     * Dashboard Guru
     */
    public function guruDashboard()
{
    $user = Auth::user();
    $kelasId = $user->kelas_id;
    
    // Ambil request dari siswa di kelas yang diampu
    $requests = DokumenRequest::whereHas('siswa', function($q) use ($kelasId) {
        $q->where('kelas_id', $kelasId);
    })->with(['siswa', 'kategori'])
    ->orderBy('created_at', 'desc')
    ->paginate(10);
    
    $data = [
        'total_dokumen' => Dokumen::whereHas('siswa', function($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        })->count(),
        'total_terverifikasi' => Dokumen::whereHas('siswa', function($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        })->where('status', 'aktif')->count(),
        'total_menunggu' => Dokumen::whereHas('siswa', function($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        })->where('status', 'pending')->count(),
        'total_request' => DokumenRequest::whereHas('siswa', function($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        })->count(),
        'request_count' => DokumenRequest::whereHas('siswa', function($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        })->where('status', 'pending')->count(),
        'dokumen_per_kategori' => Dokumen::whereHas('siswa', function($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        })->selectRaw('kategori_id, count(*) as total')
        ->groupBy('kategori_id')
        ->with('kategori')
        ->get(),
        'kelas_diampu' => $user->kelas,
        'dokumen' => Dokumen::with(['siswa', 'siswa.kelas', 'kategori', 'uploader'])
            ->whereHas('siswa', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10),
        'siswa' => User::where('kelas_id', $kelasId)
            ->where('role', 'siswa')
            ->with(['kelas', 'dokumen'])
            ->orderBy('nama')
            ->get(),
        'kategori' => KategoriDokumen::all(),
        'requests' => $requests,
    ];

    return view('dashboard-guru', compact('user', 'data'));
}
    /**
     * Dashboard Siswa
     */
    public function siswaDashboard()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Ambil data request untuk siswa
        $requests = DokumenRequest::where('siswa_id', $user->id)
            ->with(['kategori'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            // Statistik
            'total_dokumen' => Dokumen::where('siswa_id', $user->id)->count(),
            'total_terverifikasi' => Dokumen::where('siswa_id', $user->id)
                ->where('status', 'aktif')
                ->count(),
            'total_menunggu' => Dokumen::where('siswa_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            
            // Dokumen per kategori
            'dokumen_per_kategori' => Dokumen::where('siswa_id', $user->id)
                ->selectRaw('kategori_id, count(*) as total')
                ->groupBy('kategori_id')
                ->with('kategori')
                ->get(),
            
            // Kelas siswa
            'kelas_saya' => $user->kelas,
            
            // Data dokumen siswa
            'dokumen' => Dokumen::with(['kategori', 'uploader'])
                ->where('siswa_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10),
            
            // Data request dokumen
            'requests' => $requests,
            
            // Jumlah request pending
            'request_count' => DokumenRequest::where('siswa_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            
            // Kategori untuk dropdown request
            'kategori' => KategoriDokumen::all(),
        ];

        return view('dashboard-siswa', compact('user', 'data'));
    }
}