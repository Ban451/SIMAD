<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Siswa - SIMAD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- LIBRARY DOCX PREVIEW -->
    <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.2.0/dist/docx-preview.min.js"></script>

    <style>
        :root { --primary: #1a237e; --primary-light: #283593; --sidebar-width: 250px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; overflow-x: hidden; }
        
        /* SIDEBAR */
        .sidebar { position: fixed; left: 0; top: 0; width: var(--sidebar-width); height: 100%; background: var(--primary); color: white; padding: 20px 0; z-index: 1000; transition: all 0.3s; overflow-y: auto; }
        .sidebar .brand { padding: 0 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar .brand h4 { font-weight: 700; }
        .sidebar .brand h4 span { color: #ffd54f; }
        .sidebar .brand small { opacity: 0.7; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 25px; transition: all 0.3s; cursor: pointer; border: none; background: none; width: 100%; text-align: left; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
        .sidebar .nav-link i { width: 20px; margin-right: 10px; }
        .sidebar .user-profile { padding: 15px 20px; border-top: 1px solid rgba(255,255,255,0.1); margin-top: 20px; }
        .sidebar .user-profile .avatar { width: 50px; height: 50px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 20px; }

        /* MAIN CONTENT */
        .main-content { margin-left: var(--sidebar-width); padding: 20px 30px; transition: all 0.3s; }
        .topbar { background: white; padding: 15px 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }
        .topbar h5 { margin: 0; color: var(--primary); }
        
        /* DESAIN ASLI LAINNYA */
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card .number { font-size: 28px; font-weight: 700; color: var(--primary); }
        .stat-card .label { color: #666; font-size: 14px; }
        .stat-card .icon { font-size: 32px; color: var(--primary-light); opacity: 0.3; }
        .table-documents { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
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
        .request-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: transform 0.3s; cursor: pointer; }
        .request-card:hover { transform: translateY(-5px); }
        .request-card .icon { font-size: 40px; }
        .request-card .title { font-weight: 600; color: var(--primary); }
        .request-card .desc { color: #666; font-size: 14px; }
        .badge-request { padding: 4px 12px; border-radius: 12px; font-size: 12px; }
        .badge-request.pending { background: #fff3cd; color: #856404; }
        .badge-request.approved { background: #d4edda; color: #155724; }
        .badge-request.rejected { background: #f8d7da; color: #721c24; }
        .list-group-item-request { border-left: 4px solid var(--primary); }
        .list-group-item-request.pending { border-left-color: #ffc107; }
        .list-group-item-request.approved { border-left-color: #28a745; }
        .list-group-item-request.rejected { border-left-color: #dc3545; }

        /* MODAL PREVIEW */
        .preview-container { width: 100%; height: 75vh; overflow: auto; position: relative; background: #e9ecef; border: 1px solid #dee2e6; border-radius: 8px; }
        .preview-container iframe, .preview-container img { width: 100%; height: 100%; border: none; transition: transform 0.3s ease; transform-origin: top center; }
        .preview-toolbar { background: #f1f3f5; border-bottom: 1px solid #dee2e6; padding: 8px 15px; display: flex; justify-content: space-between; align-items: center; border-radius: 8px 8px 0 0; }
        #previewDocx { display: none; padding: 20px; width: 100%; min-height: 100%; transform-origin: top center; transition: transform 0.3s ease; }
        #previewDocx .docx-wrapper { background: transparent !important; padding: 0 !important; }
        #previewDocx .docx { background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin: 0 auto; }

        /* ========================================================
           CSS KHUSUS RESPONSIVE (Hanya aktif di HP/Tablet)
           ======================================================== */
        @media (max-width: 991px) {
            .sidebar { left: -260px; }
            .sidebar.show { left: 0; }
            .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; }
            .sidebar-overlay.show { display: block; }
            .main-content { margin-left: 0; padding: 15px; }
        }
    </style>
</head>
<body>

    <!-- Overlay untuk HP -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="brand">
            <h4><i class="bi bi-file-earmark-text"></i> SIMAD<span>.</span></h4>
            <small>v1.0</small>
        </div>
        <nav class="nav flex-column">
            <button class="nav-link active" data-page="dashboard"><i class="bi bi-speedometer2"></i> Dashboard</button>
            <button class="nav-link" data-page="dokumen"><i class="bi bi-file-earmark"></i> Dokumen Saya</button>
            <button class="nav-link" data-page="request">
                <i class="bi bi-envelope-paper"></i> Request Dokumen
                @if(isset($data['request_count']) && $data['request_count'] > 0)
                    <span class="badge bg-danger ms-2">{{ $data['request_count'] }}</span>
                @endif
            </button>
            <button class="nav-link" data-page="profil"><i class="bi bi-person-circle"></i> Profil</button>
        </nav>
        <div class="user-profile">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar">{{ substr($user->nama, 0, 2) }}</div>
                <div>
                    <div class="fw-bold">{{ $user->nama }}</div>
                    <small class="text-white-50">{{ $user->kelas->nama_kelas ?? 'Siswa' }}</small>
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
            <div class="d-flex align-items-center gap-2">
                <!-- TOMBOL HAMBURGER (Hanya muncul di HP) -->
                <button class="btn btn-light d-lg-none shadow-sm px-2" onclick="toggleSidebar()">
                    <i class="bi bi-list fs-3"></i>
                </button>
                <div>
                    <h5><i class="bi bi-grid d-none d-md-inline"></i> <span id="page-title">Dashboard Siswa</span></h5>
                    <small class="text-muted d-none d-sm-block">SMK 5 SURAKARTA - {{ date('l, d F Y') }}</small>
                </div>
            </div>
            <div><span class="badge bg-success">Siswa</span></div>
        </div>

        <!-- PAGE: DASHBOARD -->
        <div id="page-dashboard" class="page-section active">
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-4">
                    <div class="stat-card d-flex justify-content-between align-items-center">
                        <div><div class="number">{{ $data['total_dokumen'] }}</div><div class="label">Total Dokumen</div></div>
                        <div class="icon d-none d-sm-block"><i class="bi bi-file-earmark"></i></div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="stat-card d-flex justify-content-between align-items-center">
                        <div><div class="number">{{ $data['total_terverifikasi'] }}</div><div class="label">Terverifikasi</div></div>
                        <div class="icon d-none d-sm-block"><i class="bi bi-check-circle"></i></div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="stat-card d-flex justify-content-between align-items-center">
                        <div><div class="number">{{ $data['total_menunggu'] }}</div><div class="label">Menunggu</div></div>
                        <div class="icon d-none d-sm-block"><i class="bi bi-clock"></i></div>
                    </div>
                </div>
            </div>

            <div class="table-documents">
                <h6 class="mb-3"><i class="bi bi-clock-history"></i> Dokumen Terbaru</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr><th>ID</th><th>DOKUMEN</th><th>KATEGORI</th><th>STATUS</th><th>TANGGAL</th><th>AKSI</th></tr>
                        </thead>
                        <tbody>
                            @forelse($data['dokumen']->take(5) as $dok)
                            <tr>
                                <td>#{{ $dok->id }}</td>
                                <td>{{ $dok->judul }}</td>
                                <td><span class="badge-category">{{ $dok->kategori->nama_kategori ?? '-' }}</span></td>
                                <td>
                                    <span class="badge-status {{ $dok->status == 'aktif' ? 'verified' : 'pending' }}">
                                        {{ $dok->status == 'aktif' ? 'Terverifikasi' : 'Menunggu' }}
                                    </span>
                                </td>
                                <td>{{ $dok->created_at->format('d M Y') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-action" onclick="openPreview('{{ route('dokumen.saya.preview', $dok->id) }}', '{{ addslashes($dok->judul) }}', '{{ $dok->nama_file }}')"><i class="bi bi-eye"></i></button>
                                    <a href="{{ route('dokumen.saya.download', $dok->id) }}" class="btn btn-sm btn-outline-success btn-action"><i class="bi bi-download"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted">Belum ada dokumen</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PAGE: DOKUMEN SAYA -->
        <div id="page-dokumen" class="page-section">
            <div class="table-documents">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h6 class="mb-0"><i class="bi bi-table"></i> Dokumen Saya</h6>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#requestModal">
                        <i class="bi bi-envelope-paper"></i> Request Dokumen
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr><th>ID</th><th>DOKUMEN</th><th>KATEGORI</th><th>TAHUN</th><th>DIUNGGAH</th><th>TANGGAL</th><th>STATUS</th><th>AKSI</th></tr>
                        </thead>
                        <tbody>
                            @forelse($data['dokumen'] as $dok)
                            <tr>
                                <td><span class="fw-bold">#{{ $dok->id }}</span></td>
                                <td>{{ $dok->judul }}</td>
                                <td><span class="badge-category">{{ $dok->kategori->nama_kategori ?? '-' }}</span></td>
                                <td>{{ $dok->created_at->format('Y') }}</td>
                                <td>{{ $dok->uploader->nama ?? 'Sistem' }}</td>
                                <td>{{ $dok->created_at->format('d M Y') }}</td>
                                <td>
                                    <span class="badge-status {{ $dok->status == 'aktif' ? 'verified' : 'pending' }}">
                                        {{ $dok->status == 'aktif' ? 'Terverifikasi' : 'Menunggu' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-action" onclick="openPreview('{{ route('dokumen.saya.preview', $dok->id) }}', '{{ addslashes($dok->judul) }}', '{{ $dok->nama_file }}')"><i class="bi bi-eye"></i></button>
                                    <a href="{{ route('dokumen.saya.download', $dok->id) }}" class="btn btn-sm btn-outline-success btn-action"><i class="bi bi-download"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted">Belum ada dokumen</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-2">{{ $data['dokumen']->links() }}</div>
            </div>
        </div>

        <!-- PAGE: REQUEST -->
        <div id="page-request" class="page-section">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="mb-0"><i class="bi bi-envelope-paper"></i> Riwayat Request</h6>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#requestModal">
                        <i class="bi bi-plus-circle"></i> Request Baru
                    </button>
                </div>
                <div class="card-body">
                    @if(isset($data['requests']) && $data['requests']->count() > 0)
                        <div class="list-group">
                            @foreach($data['requests'] as $req)
                            <div class="list-group-item list-group-item-request {{ $req->status }}">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h6 class="mb-1">{{ $req->judul }}</h6>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar"></i> {{ $req->created_at->format('d M Y H:i') }}
                                            <span class="mx-2">|</span>
                                            <i class="bi bi-tag"></i> {{ $req->kategori->nama_kategori ?? 'Umum' }}
                                        </small>
                                    </div>
                                    <div>
                                        <span class="badge-request {{ $req->status }}">
                                            {{ ucfirst($req->status) }}
                                        </span>
                                    </div>
                                </div>
                                @if($req->keterangan)
                                    <p class="mb-0 mt-2 small text-muted">{{ $req->keterangan }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-2">{{ $data['requests']->links() }}</div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i> Belum ada request dokumen
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- PAGE: PROFIL -->
        <div id="page-profil" class="page-section">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-person-circle"></i> Profil</h6></div>
                <div class="card-body text-center text-md-start">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="avatar-sm mx-auto" style="width: 100px; height: 100px; font-size: 40px; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                {{ substr($user->nama, 0, 2) }}
                            </div>
                            <h5 class="mt-3">{{ $user->nama }}</h5>
                            <span class="badge bg-success">Siswa</span>
                        </div>
                        <div class="col-md-9">
                            <table class="table table-sm">
                                <tr><th width="150">Nama Lengkap</th><td>{{ $user->nama }}</td></tr>
                                <tr><th>NIS</th><td>{{ $user->nis ?? '-' }}</td></tr>
                                <tr><th>Kelas</th><td>{{ $user->kelas->nama_kelas ?? '-' }}</td></tr>
                                <tr><th>Email</th><td>{{ $user->email ?? '-' }}</td></tr>
                                <tr><th>Total Dokumen</th><td>{{ $data['total_dokumen'] }}</td></tr>
                                <tr><th>Bergabung Sejak</th><td>{{ $user->created_at->format('d M Y') }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PREVIEW DOKUMEN -->
    <div class="modal fade" id="previewModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-file-earmark-text"></i> <span id="previewTitle">Preview</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="closePreview()"></button>
                </div>
                <div class="modal-body p-0" style="background: #e9ecef;">
                    <div class="preview-toolbar">
                        <span id="fileNameLabel" class="text-muted small fw-bold">Memuat file...</span>
                        <div>
                            <button class="btn btn-sm btn-outline-dark" onclick="zoomIn()"><i class="bi bi-zoom-in"></i></button>
                            <button class="btn btn-sm btn-outline-dark" onclick="zoomOut()"><i class="bi bi-zoom-out"></i></button>
                            <button class="btn btn-sm btn-outline-dark" onclick="resetZoom()"><i class="bi bi-arrow-counterclockwise"></i></button>
                        </div>
                    </div>
                    <div class="preview-container">
                        <iframe id="previewFrame" src="" style="display: none;"></iframe>
                        <img id="previewImage" src="" style="display: none;" alt="Preview Dokumen">
                        <div id="previewDocx"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closePreview()">Close</button>
                    <a href="#" id="downloadLinkBtn" class="btn btn-primary"><i class="bi bi-download"></i> Download</a>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL REQUEST -->
    <div class="modal fade" id="requestModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-envelope-paper"></i> Request Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="requestForm" action="{{ route('siswa.request.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jenis Dokumen <span class="text-danger">*</span></label>
                            <select name="kategori_id" class="form-control" required>
                                <option value="">Pilih Jenis Dokumen</option>
                                @foreach($data['kategori'] ?? [] as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Judul Request <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" placeholder="Contoh: Request Rapor" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Jelaskan..."></textarea>
                        </div>
                        <div class="alert alert-info py-2 mb-0">
                            <small><i class="bi bi-info-circle"></i> Request akan dikirim ke wali kelas.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="liveToast" class="toast" role="alert">
            <div class="toast-header">
                <strong class="me-auto"><i class="bi bi-info-circle"></i> Info</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body" id="toastMessage">Pesan</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar di HP
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        // Navigasi
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                document.querySelectorAll('.sidebar .nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                document.querySelectorAll('.page-section').forEach(p => p.classList.remove('active'));
                const page = this.dataset.page;
                document.getElementById('page-' + page).classList.add('active');
                const titles = {'dashboard': 'Dashboard Siswa', 'dokumen': 'Dokumen Saya', 'request': 'Request Dokumen', 'profil': 'Profil'};
                document.getElementById('page-title').textContent = titles[page] || page;

                // Tutup menu saat di klik (Hanya HP)
                if (window.innerWidth < 992) toggleSidebar();
            });
        });

        // Toast
        function showToast(message, type = 'info') {
            const toast = document.getElementById('liveToast');
            const colors = {'success': '#d4edda', 'danger': '#f8d7da', 'warning': '#fff3cd', 'info': '#d1ecf1'};
            if(toast) {
                toast.querySelector('.toast-header').style.backgroundColor = colors[type] || '#fff';
                document.getElementById('toastMessage').textContent = message;
                new bootstrap.Toast(toast).show();
            }
        }

        @if(session('success')) showToast('{{ session('success') }}', 'success'); @endif
        @if(session('error')) showToast('{{ session('error') }}', 'danger'); @endif

        // FITUR PREVIEW DOKUMEN
        let currentZoom = 100;
        let currentType = '';

        function openPreview(url, title, filename) {
            const ext = filename.split('.').pop().toLowerCase();
            const iframe = document.getElementById('previewFrame');
            const img = document.getElementById('previewImage');
            const docxContainer = document.getElementById('previewDocx');
            
            currentZoom = 100;
            currentType = ext;
            
            document.getElementById('previewTitle').textContent = title;
            document.getElementById('fileNameLabel').innerHTML = `${currentZoom}%`;
            document.getElementById('downloadLinkBtn').href = url.replace('preview', 'download');

            iframe.style.display = 'none'; iframe.src = '';
            img.style.display = 'none'; img.src = '';
            docxContainer.style.display = 'none'; docxContainer.innerHTML = ''; docxContainer.style.transform = `scale(1)`;

            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                img.style.display = 'block';
                img.src = url;
                img.style.transform = `scale(1)`;
            } else if (ext === 'docx') {
                docxContainer.style.display = 'block';
                docxContainer.innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-primary"></div></div>';
                fetch(url).then(r => r.blob()).then(blob => {
                    docxContainer.innerHTML = '';
                    docx.renderAsync(blob, docxContainer, null, { className: "docx", inWrapper: true }).then(() => {
                        docxContainer.style.transform = `scale(1)`;
                    });
                }).catch(err => { docxContainer.innerHTML = `<div class="alert alert-danger m-3">Gagal memuat: ${err.message}</div>`; });
            } else if (ext === 'doc') {
                iframe.style.display = 'block';
                iframe.src = `https://docs.google.com/viewer?url=${encodeURIComponent(url)}&embedded=true`;
                iframe.style.transform = `scale(1)`;
            } else {
                iframe.style.display = 'block';
                iframe.src = url; 
                iframe.style.transform = `scale(1)`;
            }
            new bootstrap.Modal(document.getElementById('previewModal')).show();
        }

        function closePreview() {
            document.getElementById('previewFrame').src = '';
            document.getElementById('previewImage').src = '';
            document.getElementById('previewDocx').innerHTML = '';
        }

        function zoomIn() { if (currentZoom < 200) { currentZoom += 25; applyZoom(); } }
        function zoomOut() { if (currentZoom > 50) { currentZoom -= 25; applyZoom(); } }
        function resetZoom() { currentZoom = 100; applyZoom(); }

        function applyZoom() {
            const scale = currentZoom / 100;
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(currentType)) {
                document.getElementById('previewImage').style.transform = `scale(${scale})`;
            } else if (currentType === 'docx') {
                document.getElementById('previewDocx').style.transform = `scale(${scale})`;
            } else {
                document.getElementById('previewFrame').style.transform = `scale(${scale})`;
            }
            document.getElementById('fileNameLabel').innerHTML = `${currentZoom}%`;
        }
    </script>
</body>
</html>