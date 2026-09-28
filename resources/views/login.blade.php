<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SIMAD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%);
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%) !important;
            padding: 25px !important;
        }
        .card-header h4 {
            font-weight: 700;
        }
        .btn-primary {
            background: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 35, 126, 0.4);
        }
        .btn-back {
            border: 2px solid #1a237e;
            color: #1a237e;
            padding: 12px;
            font-weight: 600;
            background: white;
            border-radius: 8px;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        .btn-back:hover {
            background: #1a237e;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 35, 126, 0.3);
        }
        .form-control:focus {
            border-color: #1a237e;
            box-shadow: 0 0 0 0.2rem rgba(26, 35, 126, 0.25);
        }
        .login-icon {
            font-size: 48px;
            display: block;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-5">
                <div class="card shadow-lg">
                    <div class="card-header text-white text-center py-4">
                        <div class="login-icon">📚</div>
                        <h4 class="mb-0"><i class="bi bi-file-earmark-text"></i> SIMAD</h4>
                        <small>Sistem Informasi Manajemen Dokumen Akademik</small>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="text-center mb-4">Login</h5>

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="login" class="form-label fw-bold">
                                    <i class="bi bi-person-badge"></i> Email / NIS
                                </label>
                                <input type="text" name="login" id="login" class="form-control"
                                    placeholder="Masukkan email (Guru/Admin) atau NIS (Siswa)"
                                    value="{{ old('login') }}" required autofocus>
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i>
                                    Guru/Admin: gunakan email | Siswa: gunakan NIS
                                </small>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">
                                    <i class="bi bi-lock"></i> Password
                                </label>
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="Masukkan password" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                                <label for="remember" class="form-check-label">Ingat saya</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </form>

                        <hr class="my-4">

                        <a href="{{ route('landing') }}" class="btn-back">
                            <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                        </a>

                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="bi bi-building"></i> SMK Negeri 5 Surakarta • XII RPL C
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>