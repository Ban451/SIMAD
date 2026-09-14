<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Guru - SIMAD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --primary: #1a237e; --primary-light: #283593; --sidebar-width: 250px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
        .sidebar { position: fixed; left: 0; top: 0; width: var(--sidebar-width); height: 100%; background: var(--primary); color: white; padding: 20px 0; z-index: 1000; }
        .sidebar .brand { padding: 0 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar .brand h4 { font-weight: 700; }
        .sidebar .brand h4 span { color: #ffd54f; }
        .sidebar .brand small { opacity: 0.7; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 25px; transition: all 0.3s; cursor: pointer; border: none; background: none; width: 100%; text-align: left; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
        .sidebar .nav-link i { width: 20px; margin-right: 10px; }
        .sidebar .nav-link .badge-request { background: #dc3545; color: white; font-size: 10px; padding: 2px 8px; border-radius: 10px; margin-left: 5px; }
        .sidebar .user-profile { padding: 15px 20px; border-top: 1px solid rgba(255,255,255,0.1); margin-top: 20px; }
        .sidebar .user-profile .avatar { width: 50px; height: 50px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 20px; }
        .main-content { margin-left: var(--sidebar-width); padding: 20px 30px; }
        .topbar { background: white; padding: 15px 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }
        .topbar h5 { margin: 0; color: var(--primary); }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card .number { font-size: 28px; font-weight: 700; color: var(--primary); }
        .stat-card .label { color: #666; font-size: 14px; }
        .filter-section { background: white; padding: 15px 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .filter-section .btn-filter { padding: 5px 15px; border-radius: 20px; border: 1px solid #ddd; background: white; color: #666; font-size: 13px; cursor: pointer; transition: all 0.3s; }
        .filter-section .btn-filter:hover, .filter-section .btn-filter.active { background: var(--primary); color: white; border-color: var(--primary); }
        .table-documents { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .table-documents th { color: #666; font-weight: 600; font-size: 13px; border-bottom: 2px solid #eee; }
        .table-documents td { vertical-align: middle; font-size: 14px; }
        .badge-status { padding: 4px 12px; border-radius: 12px; font-size: 12px; }
        .badge-status.verified { background: #d4edda; color: #155724; }
        .badge-status.pending { background: #fff3cd; color: #856404; }
        .badge-category { background: #e8eaf6; color: var(--primary); padding: 2px 10px; border-radius: 10px; font-size: 11px; font-weight: 600; }
        .btn-action { padding: 2px 8px; border-radius: 5px; font-size: 12px; }
        .logout-btn { color: #dc3545; text-decoration: none; cursor: pointer; }
        .logout-btn:hover { color: #bd2130; }
        .page-section { display: none; }
        .page-section.active { display: block; }
        .modal-content { border-radius: 15px; }
        .modal-header { background: var(--primary); color: white; border-radius: 15px 15px 0 0; }
        .modal-header .btn-close { filter: brightness(0) invert(1); }
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; }
        .avatar-sm { width: 35px; height: 35px; border-radius: 50%; background: var(--primary); color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; }
        .request-card { background: white; border-radius: 12px; padding: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 4px solid var(--primary); margin-bottom: 10px; }
        .request-card.pending { border-left-color: #ffc107; }
        .request-card .request-info { display: flex; justify-content: space-between; align-items: center; }
        .request-card .request-meta { color: #666; font-size: 13px; }
        .badge-request-status { padding: 4px 12px; border-radius: 12px; font-size: 12px; }
        .badge-request-status.pending { background: #fff3cd; color: #856404; }
        .badge-request-status.approved { background: #d4edda; color: #155724; }
        .badge-request-status.rejected { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand">
            <h4><i class="bi bi-file-earmark-text"></i> SIMAD<span>.</span></h4>
            <small>v1.0</small>
        </div>
        <div class="px-3 mb-2"><div class="text-white-50 small">Menu</div></div>
        <nav class="nav flex-column">
            <button class="nav-link active" data-page="dashboard"><i class="bi bi-speedometer2"></i> Dashboard</button>
            <button class="nav-link" data-page="dokumen"><i class="bi bi-file-earmark"></i> Dokumen</button>
            <button class="nav-link" data-page="siswa"><i class="bi bi-people"></i> Data Siswa</button>
            <button class="nav-link" data-page="request">
                <i class="bi bi-envelope-paper"></i> Request Siswa
                @if(isset($data['request_count']) && $data['request_count'] > 0)
                    <span class="badge-request">{{ $data['request_count'] }}</span>
                @endif
            </button>
            <button class="nav-link" data-page="profil"><i class="bi bi-person-circle"></i> Profil</button>
        </nav>
        <div class="user-profile">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar">{{ substr($user->nama, 0, 2) }}</div>
                <div>
                    <div class="fw-bold">{{ $user->nama }}</div>
                    <small class="text-white-50">{{ $user->kelas->nama_kelas ?? 'Guru' }}</small>
                </div>
            </div>
        </div>
        <div class="position-absolute bottom-0 start-0 w-100 p-3">
            <a href="#" class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <div>
                <h5><i class="bi bi-grid"></i> <span id="page-title">Dashboard</span></h5>
                <small class="text-muted">SMK Amadeo - {{ date('l, d F Y') }}</small>
            </div>
            <div><span class="badge bg-primary">Guru</span></div>
        </div>

        <!-- PAGE: DASHBOARD -->
        <div id="page-dashboard" class="page-section active">
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="stat-card d-flex justify-content-between align-items-center">
                        <div><div class="number">{{ $data['total_dokumen'] ?? 0 }}</div><div class="label">Total Dokumen</div></div>
                        <i class="bi bi-file-earmark fs-1 text-primary opacity-50"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card d-flex justify-content-between align-items-center">
                        <div><div class="number">{{ $data['total_terverifikasi'] ?? 0 }}</div><div class="label">Terverifikasi</div></div>
                        <i class="bi bi-check-circle fs-1 text-success opacity-50"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card d-flex justify-content-between align-items-center">
                        <div><div class="number">{{ $data['total_menunggu'] ?? 0 }}</div><div class="label">Menunggu</div></div>
                        <i class="bi bi-clock fs-1 text-warning opacity-50"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card d-flex justify-content-between align-items-center">
                        <div><div class="number">{{ $data['total_request'] ?? 0 }}</div><div class="label">Request Masuk</div></div>
                        <i class="bi bi-envelope-paper fs-1 text-info opacity-50"></i>
                    </div>
                </div>
            </div>

            @if(isset($data['requests']) && $data['requests']->count() > 0)
            <div class="table-documents mb-4">
                <h6 class="mb-3"><i class="bi bi-envelope-paper"></i> Request Terbaru</h6>
                @foreach($data['requests']->take(3) as $req)
                <div class="request-card pending">
                    <div class="request-info">
                        <div>
                            <strong>{{ $req->judul }}</strong>
                            <div class="request-meta">
                                <i class="bi bi-person"></i> {{ $req->siswa->nama ?? '-' }}
                                <span class="mx-2">|</span>
                                <i class="bi bi-tag"></i> {{ $req->kategori->nama_kategori ?? 'Umum' }}
                                <span class="mx-2">|</span>
                                <i class="bi bi-calendar"></i> {{ $req->created_at->format('d M Y H:i') }}
                            </div>
                        </div>
                        <div>
                            <span class="badge-request-status pending">Menunggu</span>
                            <button class="btn btn-sm btn-success ms-2" onclick="processRequest({{ $req->id }}, 'approved')"><i class="bi bi-check"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="processRequest({{ $req->id }}, 'rejected')"><i class="bi bi-x"></i></button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <div class="table-documents">
                <h6 class="mb-3"><i class="bi bi-clock-history"></i> Dokumen Terbaru</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>ID</th><th>Judul</th><th>Siswa</th><th>Kategori</th><th>Status</th><th>Tanggal</th></tr></thead>
                        <tbody>
                            @forelse($data['dokumen']->take(5) as $dok)
                            <tr>
                                <td>#{{ $dok->id }}</td>
                                <td>{{ $dok->judul }}</td>
                                <td>{{ $dok->siswa->nama ?? '-' }}</td>
                                <td><span class="badge-category">{{ $dok->kategori->nama_kategori ?? '-' }}</span></td>
                                <td>
                                    <span class="badge-status {{ $dok->status == 'aktif' ? 'verified' : 'pending' }}">
                                        {{ $dok->status == 'aktif' ? 'Terverifikasi' : 'Menunggu' }}
                                    </span>
                                </td>
                                <td>{{ $dok->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted">Belum ada dokumen</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PAGE: DOKUMEN -->
        <div id="page-dokumen" class="page-section">
            <div class="filter-section">
                <div class="row align-items-center g-2">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchDokumen" class="form-control border-start-0" placeholder="Cari nama siswa atau dokumen...">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn-filter active" data-filter="all">Semua</button>
                            <button class="btn-filter" data-filter="RAPOR">RAPOR</button>
                            <button class="btn-filter" data-filter="IJAZAH">IJAZAH</button>
                            <button class="btn-filter" data-filter="TOEIC">TOEIC</button>
                            <button class="btn-filter" data-filter="UKK">UKK</button>
                            <button class="btn-filter" data-filter="PKL">PKL</button>
                            <span class="mx-2 text-muted">|</span>
                            <button class="btn-filter" data-status="aktif">Verified</button>
                            <button class="btn-filter" data-status="pending">Pending</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-documents">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0"><i class="bi bi-table"></i> Daftar Dokumen</h6>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="bi bi-plus-circle"></i> Upload Dokumen
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr><th>ID</th><th>DOKUMEN</th><th>SISWA</th><th>KELAS</th><th>KATEGORI</th><th>STATUS</th><th>TANGGAL</th><th>AKSI</th></tr>
                        </thead>
                        <tbody id="dokumenBody">
                            @forelse($data['dokumen'] as $dok)
                            <tr>
                                <td><span class="fw-bold">#{{ $dok->id }}</span></td>
                                <td>{{ $dok->judul }}</td>
                                <td>{{ $dok->siswa->nama ?? '-' }}</td>
                                <td>{{ $dok->siswa->kelas->nama_kelas ?? '-' }}</td>
                                <td><span class="badge-category">{{ $dok->kategori->nama_kategori ?? '-' }}</span></td>
                                <td>
                                    <span class="badge-status {{ $dok->status == 'aktif' ? 'verified' : 'pending' }}">
                                        {{ $dok->status == 'aktif' ? 'Terverifikasi' : 'Menunggu' }}
                                    </span>
                                </td>
                                <td>{{ $dok->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('guru.dokumen.preview', $dok->id) }}" 
                                       target="_blank"
                                       class="btn btn-sm btn-outline-primary btn-action">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('guru.dokumen.download', $dok->id) }}" 
                                       class="btn btn-sm btn-outline-success btn-action">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-warning btn-action" onclick="editDokumen({{ $dok->id }})"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteDokumen({{ $dok->id }})"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted">Belum ada dokumen</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $data['dokumen']->links() }}
            </div>
        </div>

        <!-- PAGE: SISWA -->
        <div id="page-siswa" class="page-section">
            <div class="card">
                <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-people"></i> Data Siswa</h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>NIS</th><th>Nama</th><th>Kelas</th><th>Total Dokumen</th><th>Aksi</th></tr></thead>
                            <tbody>
                                @forelse($data['siswa'] ?? [] as $s)
                                <tr>
                                    <td>{{ $s->nis }}</td>
                                    <td>{{ $s->nama }}</td>
                                    <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                                    <td>{{ $s->dokumen->count() }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="uploadForSiswa({{ $s->id }})">
                                            <i class="bi bi-upload"></i> Upload
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted">Belum ada siswa</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- PAGE: REQUEST SISWA -->
        <div id="page-request" class="page-section">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-envelope-paper"></i> Request Dokumen dari Siswa</h6>
                    <span class="badge bg-primary">{{ $data['request_count'] ?? 0 }} Menunggu</span>
                </div>
                <div class="card-body">
                    @if(isset($data['requests']) && $data['requests']->count() > 0)
                        @foreach($data['requests'] as $req)
                        <div class="request-card {{ $req->status }}">
                            <div class="request-info">
                                <div>
                                    <h6 class="mb-1">{{ $req->judul }}</h6>
                                    <div class="request-meta">
                                        <i class="bi bi-person"></i> <strong>{{ $req->siswa->nama ?? '-' }}</strong>
                                        <span class="mx-2">|</span>
                                        <i class="bi bi-tag"></i> {{ $req->kategori->nama_kategori ?? 'Umum' }}
                                        <span class="mx-2">|</span>
                                        <i class="bi bi-calendar"></i> {{ $req->created_at->format('d M Y H:i') }}
                                    </div>
                                    @if($req->keterangan)
                                        <p class="mt-2 mb-0 small text-muted"><i class="bi bi-chat"></i> {{ $req->keterangan }}</p>
                                    @endif
                                </div>
                                <div>
                                    <span class="badge-request-status {{ $req->status }}">
                                        {{ $req->status == 'pending' ? 'Menunggu' : ($req->status == 'approved' ? 'Disetujui' : 'Ditolak') }}
                                    </span>
                                    @if($req->status == 'pending')
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-success" onclick="processRequest({{ $req->id }}, 'approved')"><i class="bi bi-check"></i> Setujui</button>
                                            <button class="btn btn-sm btn-danger" onclick="processRequest({{ $req->id }}, 'rejected')"><i class="bi bi-x"></i> Tolak</button>
                                            <button class="btn btn-sm btn-primary" onclick="uploadForRequest({{ $req->id }})"><i class="bi bi-upload"></i> Upload</button>
                                        </div>
                                    @endif
                                    @if($req->status == 'rejected' && $req->alasan)
                                        <p class="mt-1 small text-danger"><i class="bi bi-x-circle"></i> Alasan: {{ $req->alasan }}</p>
                                    @endif
                                    @if($req->status == 'approved')
                                        <div class="mt-2">
                                            <small class="text-success">
                                                <i class="bi bi-check-circle"></i> Disetujui 
                                                @if($req->processed_at) pada {{ $req->processed_at->format('d M Y H:i') }} @endif
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                        {{ $data['requests']->links() }}
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada request dari siswa
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- PAGE: PROFIL -->
        <div id="page-profil" class="page-section">
            <div class="card">
                <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-person-circle"></i> Profil Saya</h6></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="avatar-sm" style="width: 100px; height: 100px; font-size: 40px; margin: 0 auto; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                {{ substr($user->nama, 0, 2) }}
                            </div>
                            <h5 class="mt-3">{{ $user->nama }}</h5>
                            <span class="badge bg-primary">{{ ucfirst($user->role) }}</span>
                        </div>
                        <div class="col-md-9">
                            <table class="table">
                                <tr><th width="200">Nama Lengkap</th><td>{{ $user->nama }}</td></tr>
                                <tr><th>Email</th><td>{{ $user->email ?? '-' }}</td></tr>
                                <tr><th>Role</th><td>{{ ucfirst($user->role) }}</td></tr>
                                <tr><th>Kelas Diampu</th><td>{{ $user->kelas->nama_kelas ?? '-' }}</td></tr>
                                <tr><th>Bergabung Sejak</th><td>{{ $user->created_at->format('d M Y') }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL UPLOAD -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-upload"></i> Upload Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="uploadForm" action="{{ route('guru.dokumen.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Judul Dokumen <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori_id" class="form-control" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($data['kategori'] ?? [] as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Pilih Siswa <span class="text-danger">*</span></label>
                                <select name="siswa_id" class="form-control" required>
                                    <option value="">Pilih Siswa</option>
                                    @foreach($data['siswa'] ?? [] as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->nis }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <select name="status" class="form-control">
                                    <option value="pending">Menunggu</option>
                                    <option value="aktif">Terverifikasi</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">File Dokumen <span class="text-danger">*</span></label>
                                <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-cloud-upload"></i> Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL UPLOAD UNTUK REQUEST -->
    <div class="modal fade" id="uploadRequestModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-upload"></i> Upload Dokumen untuk Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="uploadRequestForm" action="{{ route('guru.dokumen.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info"><i class="bi bi-info-circle"></i> Upload dokumen untuk memenuhi request siswa.</div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Judul Dokumen <span class="text-danger">*</span></label>
                                <input type="text" name="judul" id="requestJudul" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori_id" id="requestKategori" class="form-control" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($data['kategori'] ?? [] as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Pilih Siswa <span class="text-danger">*</span></label>
                                <select name="siswa_id" id="requestSiswa" class="form-control" required>
                                    <option value="">Pilih Siswa</option>
                                    @foreach($data['siswa'] ?? [] as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->nis }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <select name="status" class="form-control">
                                    <option value="aktif">Terverifikasi</option>
                                    <option value="pending">Menunggu</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">File Dokumen <span class="text-danger">*</span></label>
                                <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <input type="hidden" name="request_id" id="requestId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-cloud-upload"></i> Upload & Selesaikan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast-container">
        <div id="liveToast" class="toast" role="alert">
            <div class="toast-header">
                <strong class="me-auto"><i class="bi bi-info-circle"></i> SIMAD</strong>
                <small>Baru saja</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body" id="toastMessage">Pesan</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // NAVIGASI
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                document.querySelectorAll('.sidebar .nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                document.querySelectorAll('.page-section').forEach(p => p.classList.remove('active'));
                const page = this.dataset.page;
                document.getElementById('page-' + page).classList.add('active');
                const titles = {'dashboard': 'Dashboard', 'dokumen': 'Manajemen Dokumen', 'siswa': 'Data Siswa', 'request': 'Request Siswa', 'profil': 'Profil'};
                document.getElementById('page-title').textContent = titles[page] || page;
            });
        });

        // FILTER
        document.querySelectorAll('.btn-filter').forEach(btn => {
            btn.addEventListener('click', function() {
                const parent = this.closest('.filter-section');
                parent.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const filter = this.dataset.filter;
                const status = this.dataset.status;
                document.querySelectorAll('#dokumenBody tr').forEach(row => {
                    let show = true;
                    if (filter && filter !== 'all') {
                        const kategori = row.querySelector('.badge-category');
                        if (kategori && kategori.textContent.trim() !== filter) show = false;
                    }
                    if (status && show) {
                        const badge = row.querySelector('.badge-status');
                        if (badge) {
                            const isVerified = badge.classList.contains('verified');
                            const isPending = badge.classList.contains('pending');
                            if (status === 'aktif' && !isVerified) show = false;
                            if (status === 'pending' && !isPending) show = false;
                        }
                    }
                    row.style.display = show ? '' : 'none';
                });
            });
        });

        // SEARCH
        document.getElementById('searchDokumen')?.addEventListener('keyup', function() {
            const search = this.value.toLowerCase();
            document.querySelectorAll('#dokumenBody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
            });
        });

        // SUBMIT FORM (AJAX)
        document.getElementById('uploadForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm(this);
        });
        document.getElementById('uploadRequestForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm(this);
        });

        function submitForm(form) {
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Uploading...';
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast('danger', data.message || 'Gagal upload');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-cloud-upload"></i> Upload';
                }
            })
            .catch(error => {
                showToast('danger', 'Terjadi kesalahan');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-cloud-upload"></i> Upload';
            });
        }

        // PROCESS REQUEST
        function processRequest(id, status) {
            if (!confirm('Yakin ingin ' + (status === 'approved' ? 'menyetujui' : 'menolak') + ' request ini?')) return;
            const formData = new FormData();
            formData.append('status', status);
            if (status === 'rejected') {
                const alasan = prompt('Masukkan alasan penolakan:');
                if (!alasan) return;
                formData.append('alasan', alasan);
            }
            fetch('/guru/request/' + id + '/process', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast('danger', data.message || 'Gagal memproses');
                }
            })
            .catch(error => showToast('danger', 'Terjadi kesalahan'));
        }

        // UPLOAD FOR REQUEST
        function uploadForRequest(id) {
            fetch('/guru/request/' + id + '/detail')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const req = data.request;
                        document.getElementById('requestId').value = req.id;
                        document.getElementById('requestJudul').value = req.judul;
                        document.getElementById('requestKategori').value = req.kategori_id;
                        document.getElementById('requestSiswa').value = req.siswa_id;
                        new bootstrap.Modal(document.getElementById('uploadRequestModal')).show();
                    } else {
                        showToast('danger', data.message || 'Gagal mengambil data');
                    }
                })
                .catch(error => showToast('danger', 'Terjadi kesalahan'));
        }

        // TOAST
        function showToast(type, message) {
            const toast = document.getElementById('liveToast');
            const colors = {'success': '#d4edda', 'danger': '#f8d7da', 'warning': '#fff3cd', 'info': '#d1ecf1'};
            toast.querySelector('.toast-header').style.backgroundColor = colors[type] || '#fff';
            document.getElementById('toastMessage').textContent = message;
            new bootstrap.Toast(toast).show();
        }

        function editDokumen(id) { showToast('info', 'Fitur edit dokumen sedang dikembangkan'); }
        function deleteDokumen(id) { if (confirm('Yakin ingin menghapus dokumen ini?')) showToast('warning', 'Fitur hapus dokumen sedang dikembangkan'); }
        function uploadForSiswa(id) {
            const select = document.querySelector('#uploadForm select[name="siswa_id"]');
            if (select) { select.value = id; new bootstrap.Modal(document.getElementById('uploadModal')).show(); }
        }

        @if(session('success')) showToast('success', '{{ session('success') }}'); @endif
        @if(session('error')) showToast('danger', '{{ session('error') }}'); @endif
    </script>
</body>
</html>