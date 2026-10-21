<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin - SIMAD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --primary: #1a237e; --primary-light: #283593; --sidebar-width: 250px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; overflow-x: hidden; }

        /* SIDEBAR */
        .sidebar { position: fixed; left: 0; top: 0; width: var(--sidebar-width); height: 100%; background: var(--primary); color: white; padding: 20px 0; transition: all 0.3s; z-index: 1000; overflow-y: auto; }
        .sidebar .brand { padding: 0 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar .brand h4 { font-weight: 700; }
        .sidebar .brand h4 span { color: #ffd54f; }
        .sidebar .brand small { opacity: 0.7; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 25px; border-radius: 0; transition: all 0.3s; cursor: pointer; border: none; background: none; width: 100%; text-align: left; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
        .sidebar .nav-link i { width: 20px; margin-right: 10px; }

        /* MAIN */
        .main-content { margin-left: var(--sidebar-width); padding: 20px 30px; transition: all 0.3s; }
        .topbar { background: white; padding: 15px 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }
        .topbar h5 { margin: 0; color: var(--primary); }
        .topbar .user-info { display: flex; align-items: center; gap: 15px; }
        .topbar .user-info .avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; }

        /* CARDS */
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card .number { font-size: 28px; font-weight: 700; color: var(--primary); }
        .stat-card .label { color: #666; font-size: 14px; }
        .stat-card .icon { font-size: 32px; color: var(--primary-light); opacity: 0.3; }

        .filter-section { background: white; padding: 15px 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .filter-section .btn-filter { padding: 5px 15px; border-radius: 20px; border: 1px solid #ddd; background: white; color: #666; font-size: 13px; transition: all 0.3s; cursor: pointer; }
        .filter-section .btn-filter:hover, .filter-section .btn-filter.active { background: var(--primary); color: white; border-color: var(--primary); }

        .table-documents { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .table-documents table { margin-bottom: 0; }
        .table-documents th { color: #666; font-weight: 600; font-size: 13px; border-bottom: 2px solid #eee; white-space: nowrap; }
        .table-documents td { vertical-align: middle; font-size: 14px; white-space: nowrap; }

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
        .badge-kelas { background: #e3f2fd; color: #0d47a1; padding: 3px 10px; border-radius: 12px; font-size: 12px; }
        .btn-kelas { padding: 2px 10px; border-radius: 12px; font-size: 12px; }

        @media (max-width: 991px) {
            .sidebar { left: -260px; }
            .sidebar.show { left: 0; }
            .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; }
            .sidebar-overlay.show { display: block; }
            .main-content { margin-left: 0; padding: 15px; }
            .topbar { flex-direction: column; align-items: flex-start; gap: 10px; }
        }
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    {{-- SIDEBAR --}}
    <div class="sidebar" id="sidebar">
        <div class="brand">
            <h4><i class="bi bi-file-earmark-text"></i> SIMAD<span>.</span></h4>
            <small>v1.0</small>
        </div>
        <div class="px-3 mb-3">
            <div class="text-white-50 small">Menu</div>
        </div>
        <nav class="nav flex-column">
            <button class="nav-link active" data-page="dashboard"><i class="bi bi-speedometer2"></i> Dashboard</button>
            <button class="nav-link" data-page="dokumen"><i class="bi bi-file-earmark"></i> Dokumen</button>
            <button class="nav-link" data-page="siswa"><i class="bi bi-people"></i> Data Siswa</button>
            <button class="nav-link" data-page="guru"><i class="bi bi-person-badge"></i> Kelola Guru</button>
            <button class="nav-link" data-page="kelas"><i class="bi bi-building"></i> Kelas</button>
            <button class="nav-link" data-page="profil"><i class="bi bi-person-circle"></i> Profil</button>
        </nav>
        <div class="position-absolute bottom-0 start-0 w-100 p-3">
            <a href="#" class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>

    {{-- MAIN --}}
    <div class="main-content">
        <div class="topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-light d-lg-none shadow-sm px-2" onclick="toggleSidebar()">
                    <i class="bi bi-list fs-3"></i>
                </button>
                <div>
                    <h5><i class="bi bi-grid d-none d-md-inline"></i> <span id="page-title">Dashboard</span></h5>
                    <small class="text-muted d-none d-sm-block">SMK 5 SURAKARTA - {{ date('l, d F Y') }}</small>
                </div>
            </div>
            <div class="user-info">
                <span class="text-muted small d-none d-sm-inline">{{ $user->nama }}</span>
                <div class="avatar">{{ substr($user->nama, 0, 1) }}</div>
            </div>
        </div>

        {{-- ============ PAGE: DASHBOARD ============ --}}
        <div id="page-dashboard" class="page-section active">
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-card d-flex justify-content-between align-items-center">
                        <div><div class="number">{{ $data['total_dokumen'] ?? 0 }}</div><div class="label">Total Dokumen</div></div>
                        <div class="icon d-none d-sm-block"><i class="bi bi-file-earmark"></i></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card d-flex justify-content-between align-items-center">
                        <div><div class="number">{{ $data['total_terverifikasi'] ?? 0 }}</div><div class="label">Terverifikasi</div></div>
                        <div class="icon d-none d-sm-block"><i class="bi bi-check-circle"></i></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card d-flex justify-content-between align-items-center">
                        <div><div class="number">{{ $data['total_menunggu'] ?? 0 }}</div><div class="label">Menunggu</div></div>
                        <div class="icon d-none d-sm-block"><i class="bi bi-clock"></i></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card d-flex justify-content-between align-items-center">
                        <div><div class="number">{{ $data['total_siswa'] ?? 0 }}</div><div class="label">Total Siswa</div></div>
                        <div class="icon d-none d-sm-block"><i class="bi bi-people"></i></div>
                    </div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-6">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between">
                            <span class="label">Total Guru</span>
                            <span class="number">{{ $data['total_guru'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between">
                            <span class="label">Total Kelas</span>
                            <span class="number" id="totalKelas">{{ $data['total_kelas'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ PAGE: DOKUMEN ============ --}}
        <div id="page-dokumen" class="page-section">
            <div class="filter-section">
                <div class="row align-items-center g-2">
                    <div class="col-12 col-md-4 mb-2 mb-md-0">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchDokumen" class="form-control border-start-0" placeholder="Cari siswa/dokumen...">
                        </div>
                    </div>
                    <div class="col-12 col-md-8">
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn-filter active" data-filter="all">Semua</button>
                            <button class="btn-filter" data-filter="RAPOR">RAPOR</button>
                            <button class="btn-filter" data-filter="IJAZAH">IJAZAH</button>
                            <button class="btn-filter" data-filter="TOEIC">TOEIC</button>
                            <button class="btn-filter" data-filter="UKK">UKK</button>
                            <button class="btn-filter" data-filter="PKL">PKL</button>
                            <span class="mx-2 text-muted d-none d-sm-inline">|</span>
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
                            <tr>
                                <th>ID</th>
                                <th>DOKUMEN</th>
                                <th>SISWA</th>
                                <th>KELAS</th>
                                <th>KATEGORI</th>
                                <th>STATUS</th>
                                <th>TANGGAL</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="dokumenBody">
                            @forelse($data['dokumen'] ?? [] as $dok)
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
                                    <a href="{{ route('dokumen.preview', $dok->id) }}" target="_blank" class="btn btn-sm btn-outline-primary btn-action">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('dokumen.download', $dok->id) }}" class="btn btn-sm btn-outline-success btn-action">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted">Belum ada dokumen</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-2">
                    @if(isset($data['dokumen']) && $data['dokumen'] instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        {{ $data['dokumen']->links() }}
                    @endif
                </div>
            </div>
        </div>

        {{-- ============ PAGE: SISWA ============ --}}
        <div id="page-siswa" class="page-section">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-people"></i> Data Siswa</h6>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#siswaModal">
                        <i class="bi bi-plus-circle"></i> Tambah Siswa
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr><th>NIS</th><th>Nama</th><th>Kelas</th><th>Dokumen</th><th>Aksi</th></tr>
                            </thead>
                            <tbody id="tableSiswaBody">
                                @forelse($data['siswa'] ?? [] as $s)
                                <tr data-id="{{ $s->id }}">
                                    <td>{{ $s->nis }}</td>
                                    <td>{{ $s->nama }}</td>
                                    <td><span class="badge-kelas">{{ $s->kelas->nama_kelas ?? 'Belum Punya Kelas' }}</span></td>
                                    <td>{{ $s->dokumen->count() }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-outline-danger btn-hapus-user" data-id="{{ $s->id }}"><i class="bi bi-trash"></i></button>
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

        {{-- ============ PAGE: GURU ============ --}}
        <div id="page-guru" class="page-section">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-person-badge"></i> Kelola Guru</h6>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#guruModal">
                        <i class="bi bi-plus-circle"></i> Tambah Guru
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-guru">
                            <thead>
                                <tr><th>ID</th><th>Nama</th><th>Email</th><th>Kelas</th><th>Status</th><th>Aksi</th></tr>
                            </thead>
                            <tbody id="tableGuruBody">
                                @forelse($data['guru'] ?? [] as $g)
                                <tr data-id="{{ $g->id }}">
                                    <td>#{{ $g->id }}</td>
                                    <td>
                                        <span class="avatar-sm me-2">{{ substr($g->nama, 0, 2) }}</span>
                                        {{ $g->nama }}
                                    </td>
                                    <td>{{ $g->email }}</td>
                                    <td>
                                        @if($g->kelas)
                                            <span class="badge-kelas"><i class="bi bi-building"></i> {{ $g->kelas->nama_kelas }}</span>
                                        @else
                                            <span class="text-muted">Belum diarahkan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $g->kelas_id ? 'bg-success' : 'bg-warning' }}">
                                            {{ $g->kelas_id ? 'Aktif' : 'Belum Dipetakan' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary btn-kelas"
                                                data-bs-toggle="modal"
                                                data-bs-target="#arahkanModal"
                                                data-id="{{ $g->id }}"
                                                data-nama="{{ $g->nama }}"
                                                data-kelas="{{ $g->kelas_id }}">
                                            <i class="bi bi-arrow-right"></i> Arahkan
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-hapus-user" data-id="{{ $g->id }}"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted">Belum ada guru</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ PAGE: KELAS ============ --}}
        <div id="page-kelas" class="page-section">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-building"></i> Data Kelas</h6>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kelasModal">
                        <i class="bi bi-plus-circle"></i> Tambah Kelas
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr><th>ID</th><th>Nama Kelas</th><th>Tingkat</th><th>Jurusan</th><th>Jumlah Siswa</th><th>Wali Kelas</th><th>Aksi</th></tr>
                            </thead>
                            <tbody id="tableKelasBody">
                                @forelse($data['kelas'] ?? [] as $k)
                                <tr data-id="{{ $k->id }}">
                                    <td>#{{ $k->id }}</td>
                                    <td><strong>{{ $k->nama_kelas }}</strong></td>
                                    <td>{{ $k->tingkat }}</td>
                                    <td><span class="badge bg-secondary">{{ $k->jurusan }}</span></td>
                                    <td><span class="badge bg-info">{{ $k->siswa->count() }}</span></td>
                                    <td>
                                        @if($k->waliKelas)
                                            <span class="badge-kelas"><i class="bi bi-person"></i> {{ $k->waliKelas->nama }}</span>
                                        @else
                                            <span class="text-muted">Belum ada wali kelas</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning btn-edit-kelas"
                                                data-id="{{ $k->id }}"
                                                data-nama="{{ $k->nama_kelas }}"
                                                data-tingkat="{{ $k->tingkat }}"
                                                data-jurusan="{{ $k->jurusan }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#kelasEditModal">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-hapus-kelas" data-id="{{ $k->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted">Belum ada kelas</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ PAGE: PROFIL ============ --}}
        <div id="page-profil" class="page-section">
            <div class="card">
                <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-person-circle"></i> Profil Saya</h6></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="avatar-sm" style="width: 100px; height: 100px; font-size: 40px; margin: 0 auto;">
                                {{ substr($user->nama, 0, 2) }}
                            </div>
                            <h5 class="mt-3">{{ $user->nama }}</h5>
                            <span class="badge bg-danger">Admin</span>
                        </div>
                        <div class="col-md-9">
                            <table class="table table-sm">
                                <tr><th width="200">Nama Lengkap</th><td>{{ $user->nama }}</td></tr>
                                <tr><th>Email</th><td>{{ $user->email ?? '-' }}</td></tr>
                                <tr><th>Role</th><td><span class="badge bg-danger">Admin</span></td></tr>
                                <tr><th>Bergabung Sejak</th><td>{{ $user->created_at->format('d M Y') }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ MODAL UPLOAD DOKUMEN ============ --}}
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-upload"></i> Upload Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('dokumen.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label fw-bold">Judul <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control" required>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori_id" class="form-control" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($data['kategori'] ?? [] as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label fw-bold">Pilih Siswa <span class="text-danger">*</span></label>
                                <select name="siswa_id" class="form-control" required>
                                    <option value="">Pilih Siswa</option>
                                    @foreach($data['siswa'] ?? [] as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->nis }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <select name="status" class="form-control">
                                    <option value="pending">Menunggu</option>
                                    <option value="aktif">Terverifikasi</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">File <span class="text-danger">*</span></label>
                                <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============ MODAL TAMBAH GURU ============ --}}
    <div class="modal fade" id="guruModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Tambah Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTambahGuru" action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="role" value="guru">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" minlength="8" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kelas</label>
                            <select name="kelas_id" class="form-control">
                                <option value="">-- Belum Dipetakan --</option>
                                @foreach($data['kelas'] ?? [] as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-danger d-none" id="errorGuru"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============ MODAL ARAHKAN KELAS ============ --}}
    <div class="modal fade" id="arahkanModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-arrow-right"></i> Arahkan Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="arahkanForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p>Mengarahkan guru: <strong id="guruNama">-</strong></p>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Kelas</label>
                            <select name="kelas_id" class="form-control" id="arahkanKelas">
                                <option value="">-- Lepas dari Kelas --</option>
                                @foreach($data['kelas'] ?? [] as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Pilih "-- Lepas dari Kelas --" untuk melepas guru dari kelas saat ini.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============ MODAL TAMBAH SISWA ============ --}}
    <div class="modal fade" id="siswaModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Tambah Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTambahSiswa" action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="role" value="siswa">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">NIS</label>
                            <input type="text" name="nis" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" minlength="8" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kelas</label>
                            <select name="kelas_id" class="form-control" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($data['kelas'] ?? [] as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-danger d-none" id="errorSiswa"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============ MODAL TAMBAH KELAS ============ --}}
    <div class="modal fade" id="kelasModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-building-add"></i> Tambah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTambahKelas" action="{{ route('kelas.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kelas" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tingkat <span class="text-danger">*</span></label>
                            <select name="tingkat" class="form-control" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="X">X</option>
                                <option value="XI">XI</option>
                                <option value="XII">XII</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jurusan <span class="text-danger">*</span></label>
                            <select name="jurusan" class="form-control" required>
                                <option value="">Pilih Jurusan</option>
                                <option value="RPL">RPL</option>
                                <option value="TKJ">TKJ</option>
                                <option value="MM">MM</option>
                                <option value="BC">BC</option>
                                <option value="Otomasi">Otomasi</option>
                                <option value="Mesin">Mesin</option>
                                <option value="Otomotif">Otomotif</option>
                                <option value="DPIB">DPIB</option>
                                <option value="TKP">TKP</option>
                                <option value="Elektronika">Elektronika</option>
                                <option value="Listrik">Listrik</option>
                            </select>
                        </div>
                        <div class="alert alert-danger d-none" id="errorKelas"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============ MODAL EDIT KELAS ============ --}}
    <div class="modal fade" id="kelasEditModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEditKelas" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kelas" id="editNamaKelas" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tingkat <span class="text-danger">*</span></label>
                            <select name="tingkat" id="editTingkat" class="form-control" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="X">X</option>
                                <option value="XI">XI</option>
                                <option value="XII">XII</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jurusan <span class="text-danger">*</span></label>
                            <select name="jurusan" id="editJurusan" class="form-control" required>
                                <option value="">Pilih Jurusan</option>
                                <option value="RPL">RPL</option>
                                <option value="TKJ">TKJ</option>
                                <option value="MM">MM</option>
                                <option value="BC">BC</option>
                                <option value="Otomasi">Otomasi</option>
                                <option value="Mesin">Mesin</option>
                                <option value="Otomotif">Otomotif</option>
                                <option value="DPIB">DPIB</option>
                                <option value="TKP">TKP</option>
                                <option value="Elektronika">Elektronika</option>
                                <option value="Listrik">Listrik</option>
                            </select>
                        </div>
                        <div class="alert alert-danger d-none" id="errorEditKelas"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- TOAST --}}
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
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        /* ================= SIDEBAR ================= */
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        /* ================= NAVIGASI ================= */
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function () {
                document.querySelectorAll('.sidebar .nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');

                document.querySelectorAll('.page-section').forEach(p => p.classList.remove('active'));
                const page = this.dataset.page;
                document.getElementById('page-' + page).classList.add('active');

                const titles = {
                    dashboard: 'Dashboard',
                    dokumen: 'Manajemen Dokumen',
                    siswa: 'Data Siswa',
                    guru: 'Kelola Guru',
                    kelas: 'Data Kelas',
                    profil: 'Profil'
                };
                document.getElementById('page-title').textContent = titles[page] || page;

                if (window.innerWidth < 992) toggleSidebar();
            });
        });

        /* ================= TOAST ================= */
        function showToast(message, type = 'info') {
            const toast = document.getElementById('liveToast');
            const body = document.getElementById('toastMessage');
            const colors = { success: '#d4edda', danger: '#f8d7da', warning: '#fff3cd', info: '#d1ecf1' };
            toast.querySelector('.toast-header').style.backgroundColor = colors[type] || '#fff';
            body.textContent = message;
            new bootstrap.Toast(toast).show();
        }

        @if(session('success')) showToast('{{ session('success') }}', 'success'); @endif
        @if(session('error'))   showToast('{{ session('error') }}', 'danger');   @endif

        /* ================= FILTER DOKUMEN ================= */
        document.querySelectorAll('.btn-filter').forEach(btn => {
            btn.addEventListener('click', function () {
                this.parentElement.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                filterDokumen();
            });
        });

        document.getElementById('searchDokumen')?.addEventListener('input', filterDokumen);

        function filterDokumen() {
            const activeFilter = document.querySelector('.btn-filter.active');
            const keyword = (document.getElementById('searchDokumen')?.value || '').toLowerCase();
            const rows = document.querySelectorAll('#dokumenBody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const kategori = row.querySelector('.badge-category')?.textContent.trim() || '';
                const status = row.querySelector('.badge-status')?.textContent.trim() || '';

                let matchFilter = true;
                if (activeFilter) {
                    if (activeFilter.dataset.filter && activeFilter.dataset.filter !== 'all') {
                        matchFilter = kategori === activeFilter.dataset.filter;
                    } else if (activeFilter.dataset.status) {
                        const wanted = activeFilter.dataset.status === 'aktif' ? 'Terverifikasi' : 'Menunggu';
                        matchFilter = status === wanted;
                    }
                }
                row.style.display = (matchFilter && text.includes(keyword)) ? '' : 'none';
            });
        }

        /* ================= FORM TAMBAH GURU ================= */
        document.getElementById('formTambahGuru')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const form = this;
            const btn = form.querySelector('button[type="submit"]');
            const errorBox = document.getElementById('errorGuru');
            const original = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';
            errorBox.classList.add('d-none');

            fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: new FormData(form),
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                showToast(data.message || 'Guru berhasil ditambahkan', 'success');
                bootstrap.Modal.getInstance(document.getElementById('guruModal'))?.hide();
                form.reset();
                setTimeout(() => location.reload(), 800);
            })
            .catch(err => {
                let msg = err.message || 'Terjadi kesalahan';
                if (err.errors) msg = Object.values(err.errors).flat().join('\n');
                errorBox.textContent = msg;
                errorBox.classList.remove('d-none');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = original;
            });
        });

        /* ================= FORM TAMBAH SISWA ================= */
        document.getElementById('formTambahSiswa')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const form = this;
            const btn = form.querySelector('button[type="submit"]');
            const errorBox = document.getElementById('errorSiswa');
            const original = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';
            errorBox.classList.add('d-none');

            fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: new FormData(form),
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                showToast(data.message || 'Siswa berhasil ditambahkan', 'success');
                bootstrap.Modal.getInstance(document.getElementById('siswaModal'))?.hide();
                form.reset();
                setTimeout(() => location.reload(), 800);
            })
            .catch(err => {
                let msg = err.message || 'Terjadi kesalahan';
                if (err.errors) msg = Object.values(err.errors).flat().join('\n');
                errorBox.textContent = msg;
                errorBox.classList.remove('d-none');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = original;
            });
        });

        /* ================= MODAL ARAHKAN KELAS ================= */
        document.querySelectorAll('[data-bs-target="#arahkanModal"]').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                document.getElementById('guruNama').textContent = this.dataset.nama;
                document.getElementById('arahkanKelas').value = this.dataset.kelas || '';
                document.getElementById('arahkanForm').action = `{{ url('/users') }}/${id}`;
            });
        });

        document.getElementById('arahkanForm')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const form = this;
            const btn = form.querySelector('button[type="submit"]');
            const original = btn.innerHTML;
            const kelasId = form.querySelector('[name="kelas_id"]').value;

            // Konfirmasi khusus kalau melepas kelas
            if (!kelasId) {
                if (!confirm('Yakin ingin melepas guru ini dari kelasnya?')) return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

            fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: new FormData(form),
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal menyimpan');
                return data;
            })
            .then(data => {
                showToast(data.message || 'Berhasil disimpan', 'success');
                bootstrap.Modal.getInstance(document.getElementById('arahkanModal'))?.hide();
                setTimeout(() => location.reload(), 800);
            })
            .catch(err => showToast(err.message, 'danger'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = original;
            });
        });

        /* ================= FORM TAMBAH KELAS ================= */
        document.getElementById('formTambahKelas')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const form = this;
            const btn = form.querySelector('button[type="submit"]');
            const errorBox = document.getElementById('errorKelas');
            const original = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';
            errorBox.classList.add('d-none');

            fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: new FormData(form),
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                showToast(data.message || 'Kelas berhasil ditambahkan', 'success');
                bootstrap.Modal.getInstance(document.getElementById('kelasModal'))?.hide();
                form.reset();
                setTimeout(() => location.reload(), 800);
            })
            .catch(err => {
                let msg = err.message || 'Terjadi kesalahan';
                if (err.errors) msg = Object.values(err.errors).flat().join('\n');
                errorBox.textContent = msg;
                errorBox.classList.remove('d-none');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = original;
            });
        });

        /* ================= MODAL EDIT KELAS ================= */
        document.querySelectorAll('.btn-edit-kelas').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                document.getElementById('editNamaKelas').value = this.dataset.nama;
                document.getElementById('editTingkat').value = this.dataset.tingkat;
                document.getElementById('editJurusan').value = this.dataset.jurusan;
                document.getElementById('formEditKelas').action = `{{ url('/kelas') }}/${id}`;
            });
        });

        document.getElementById('formEditKelas')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const form = this;
            const btn = form.querySelector('button[type="submit"]');
            const errorBox = document.getElementById('errorEditKelas');
            const original = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';
            errorBox.classList.add('d-none');

            fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: new FormData(form),
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                showToast(data.message || 'Kelas berhasil diperbarui', 'success');
                bootstrap.Modal.getInstance(document.getElementById('kelasEditModal'))?.hide();
                setTimeout(() => location.reload(), 800);
            })
            .catch(err => {
                let msg = err.message || 'Terjadi kesalahan';
                if (err.errors) msg = Object.values(err.errors).flat().join('\n');
                errorBox.textContent = msg;
                errorBox.classList.remove('d-none');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = original;
            });
        });

        /* ================= HAPUS KELAS ================= */
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-hapus-kelas');
            if (!btn) return;

            const id = btn.dataset.id;
            const row = btn.closest('tr');
            const namaKelas = row.querySelector('td:nth-child(2) strong')?.textContent.trim() || 'kelas ini';
            const jumlahSiswa = parseInt(
                row.querySelector('td:nth-child(5) .badge')?.textContent.trim() || '0'
            );

            let pesan = `Yakin ingin menghapus kelas "${namaKelas}"?`;
            if (jumlahSiswa > 0) {
                pesan += `\n\n⚠️ Kelas ini masih memiliki ${jumlahSiswa} siswa. Hapus akan gagal jika siswa belum dipindahkan.`;
            }

            if (!confirm(pesan)) return;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch(`{{ url('/kelas') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal menghapus kelas');
                return data;
            })
            .then(data => {
                showToast(data.message || 'Kelas berhasil dihapus!', 'success');

                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';

                setTimeout(() => {
                    row.remove();

                    const totalEl = document.getElementById('totalKelas');
                    if (totalEl) {
                        totalEl.textContent = Math.max(0, parseInt(totalEl.textContent || '0') - 1);
                    }

                    const tbody = document.getElementById('tableKelasBody');
                    if (tbody && tbody.querySelectorAll('tr').length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Belum ada kelas</td></tr>';
                    }
                }, 300);
            })
            .catch(err => {
                showToast(err.message || 'Terjadi kesalahan', 'danger');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-trash"></i>';
            });
        });

        /* ================= HAPUS USER (SISWA/GURU) ================= */
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-hapus-user');
            if (!btn) return;

            const id = btn.dataset.id;
            const row = btn.closest('tr');

            if (!confirm('Yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.')) return;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch(`{{ url('/users') }}/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal menghapus data');
                return data;
            })
            .then(data => {
                showToast(data.message || 'Data berhasil dihapus', 'success');
                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 300);
            })
            .catch(err => {
                showToast(err.message, 'danger');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-trash"></i>';
            });
        });
    </script>
</body>
</html>