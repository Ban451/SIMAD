<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIMAD - Sistem Manajemen Dokumen Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #1a237e;
            --primary-light: #283593;
            --secondary: #0d47a1;
            --accent: #e8eaf6;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }
        .navbar-custom {
            background: var(--primary);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-custom .navbar-brand {
            font-weight: 700;
            font-size: 24px;
            color: white;
        }
        .navbar-custom .navbar-brand span {
            color: #ffd54f;
        }
        .navbar-custom .nav-link {
            color: rgba(255,255,255,0.8) !important;
            transition: all 0.3s;
        }
        .navbar-custom .nav-link:hover {
            color: white !important;
        }
        .btn-outline-light:hover {
            background: white;
            color: var(--primary);
        }
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 80px 0 60px;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .hero h1 span {
            color: #ffd54f;
        }
        .hero .subtitle {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .hero .btn-primary-custom {
            background: #ffd54f;
            color: var(--primary);
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .hero .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 213, 79, 0.4);
            color: var(--primary);
        }
        .hero .btn-outline-custom {
            border: 2px solid white;
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .hero .btn-outline-custom:hover {
            background: white;
            color: var(--primary);
        }
        .hero .illustration {
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 30px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .hero .illustration i {
            font-size: 80px;
            color: #ffd54f;
        }
        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
            height: 100%;
            border: 1px solid #eee;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        .feature-card .icon {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 15px;
        }
        .feature-card h5 {
            font-weight: 600;
            color: var(--primary);
        }
        .feature-card p {
            color: #666;
            font-size: 14px;
        }
        .section-title {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 40px;
            position: relative;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
        }
        .recent-docs {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        .recent-docs .doc-item {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s;
        }
        .recent-docs .doc-item:hover {
            background: #f8f9fa;
            margin: 0 -10px;
            padding: 12px 10px;
            border-radius: 8px;
        }
        .recent-docs .doc-item:last-child {
            border-bottom: none;
        }
        .recent-docs .doc-title {
            font-weight: 600;
            color: var(--primary);
        }
        .recent-docs .doc-meta {
            color: #666;
            font-size: 14px;
        }
        .badge-category {
            background: var(--accent);
            color: var(--primary);
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-status {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 12px;
        }
        .badge-status.verified {
            background: #d4edda;
            color: #155724;
        }
        .badge-status.pending {
            background: #fff3cd;
            color: #856404;
        }
        .badge-status.draft {
            background: #f8d7da;
            color: #721c24;
        }
        .footer {
            background: var(--primary);
            color: white;
            padding: 30px 0;
            margin-top: 60px;
        }
        .footer a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        .footer a:hover {
            color: white;
        }
        .footer .social-icons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            margin: 0 5px;
            transition: all 0.3s;
        }
        .footer .social-icons a:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-3px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 32px;
            }
            .hero .subtitle {
                font-size: 16px;
            }
            .hero {
                padding: 60px 0 40px;
            }
            .hero .illustration {
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="{{ route('landing') }}">
                <i class="bi bi-file-earmark-text"></i> SIMAD<span>.</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="#fitur">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link" href="#hak-akses">Hak Akses</a></li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm ms-2" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> Masuk
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero" id="tentang">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1>Satu Platform. <br><span>Semua Dokumen.</span></h1>
                    <p class="subtitle">
                        SIMAD memusatkan pengelolaan <strong>rapor, ijazah, dan sertifikat</strong> 
                        (TOEIC, UKK, PKL) siswa dalam satu sistem berbasis web dengan hak akses 
                        bertingkat yang aman dan terkontrol.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('login') }}" class="btn btn-primary-custom">
                            Mulai Sekarang <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="#fitur" class="btn btn-outline-custom">
                            Pelajari Lebih <i class="bi bi-play-circle"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 text-center">
                    <div class="illustration">
                        <i class="bi bi-file-earmark-text"></i>
                        <h5 class="mt-3 text-white">SIMAD v1.0</h5>
                        <p class="text-white-50 small">Sistem Manajemen Dokumen Akademik</p>
                        <div class="mt-3">
                            <span class="badge bg-light text-primary me-1">Rapor</span>
                            <span class="badge bg-light text-primary me-1">Ijazah</span>
                            <span class="badge bg-light text-primary">Sertifikat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-5" id="fitur">
        <div class="container">
            <h3 class="section-title text-center">Mengapa SIMAD?</h3>
            <div class="row g-4 mt-3">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="icon"><i class="bi bi-shield-check"></i></div>
                        <h5>Keamanan Terjamin</h5>
                        <p>Hak akses bertingkat (Admin, Guru, Siswa) untuk menjaga integritas dokumen.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="icon"><i class="bi bi-cloud-upload"></i></div>
                        <h5>Penyimpanan Terpusat</h5>
                        <p>Semua dokumen tersimpan dalam satu platform, mudah diakses kapan saja.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="icon"><i class="bi bi-search"></i></div>
                        <h5>Pencarian Cepat</h5>
                        <p>Temukan dokumen siswa dengan cepat menggunakan fitur pencarian.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Hak Akses -->
    <section class="py-5 bg-white" id="hak-akses">
        <div class="container">
            <h3 class="section-title text-center">Hak Akses Pengguna</h3>
            <div class="row g-4 mt-3">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="icon"><i class="bi bi-person-badge"></i></div>
                        <h5>Admin</h5>
                        <p class="text-muted">Full Access & User Management</p>
                        <ul class="list-unstyled text-start small">
                            <li><i class="bi bi-check-circle text-success"></i> Kelola siswa & guru</li>
                            <li><i class="bi bi-check-circle text-success"></i> Kelola kelas & kategori</li>
                            <li><i class="bi bi-check-circle text-success"></i> Akses semua dokumen</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="icon"><i class="bi bi-person-workspace"></i></div>
                        <h5>Guru / Wali Kelas</h5>
                        <p class="text-muted">CRUD Dokumen Akademik</p>
                        <ul class="list-unstyled text-start small">
                            <li><i class="bi bi-check-circle text-success"></i> Upload dokumen siswa</li>
                            <li><i class="bi bi-check-circle text-success"></i> Edit & hapus dokumen</li>
                            <li><i class="bi bi-check-circle text-success"></i> Kelola kelas diampu</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="icon"><i class="bi bi-person"></i></div>
                        <h5>Siswa</h5>
                        <p class="text-muted">Read Only & Cetak</p>
                        <ul class="list-unstyled text-start small">
                            <li><i class="bi bi-check-circle text-success"></i> Lihat dokumen sendiri</li>
                            <li><i class="bi bi-check-circle text-success"></i> Unduh dokumen</li>
                            <li><i class="bi bi-check-circle text-success"></i> Cetak dokumen</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Documents -->
    <section class="py-4">
        <div class="container">
            <div class="recent-docs">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Dokumen Terbaru</h5>
                    <span class="text-muted small">{{ $recentDocuments->count() }} dokumen</span>
                </div>
                
                @forelse($recentDocuments as $dok)
                <div class="doc-item d-flex justify-content-between align-items-center">
                    <div>
                        <span class="doc-title">{{ $dok->judul }}</span>
                        <span class="badge-category ms-2">{{ $dok->kategori->nama_kategori ?? 'Umum' }}</span>
                        <div class="doc-meta">
                            {{ $dok->siswa->nama ?? 'Tidak Diketahui' }} 
                            @if($dok->siswa && $dok->siswa->kelas)
                                - {{ $dok->siswa->kelas->nama_kelas }}
                            @endif
                            <span class="mx-1">•</span>
                            {{ $dok->created_at->format('d M Y') }}
                        </div>
                    </div>
                    <span class="badge-status {{ $dok->status == 'aktif' ? 'verified' : 'pending' }}">
                        {{ $dok->status == 'aktif' ? 'Terverifikasi' : 'Menunggu' }}
                    </span>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    Belum ada dokumen yang diupload
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3"><i class="bi bi-file-earmark-text"></i> SIMAD</h5>
                    <p class="text-white-50 small">Sistem Informasi Manajemen Dokumen Akademik Siswa</p>
                    <p class="mb-0 small">&copy; 2026 SIMAD - SMK 5 SURAKARTA. All rights reserved.</p>
                    <small class="text-white-50">slamd.amadee.sch.id</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-envelope"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                    </div>
                    <p class="mt-3 small text-white-50">
                        Dibuat dengan <i class="bi bi-heart-fill text-danger"></i> oleh 
                        <a href="#" class="text-white">Amadeo Brian R.P</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll untuk anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>