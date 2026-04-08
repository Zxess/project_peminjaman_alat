<!DOCTYPE html> 
<html lang="id"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Sistem Peminjaman Alat | PINJAM</title> 
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <!-- Google Fonts -->
    <link href="https://googleapis.com" rel="stylesheet">
    <!-- Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cloudflare.com">
    
    <style> 
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        /* Navbar Custom */
        .navbar {
            backdrop-filter: blur(10px);
            background-color: rgba(25, 135, 84, 0.9) !important; /* Hijau Bootstrap dengan transparansi */
        }

        /* Hero Section Modern */
        .hero-section { 
            background: linear-gradient(135deg, rgba(25, 135, 84, 0.85), rgba(13, 110, 253, 0.7)), 
                        url('https://unsplash.com'); 
            background-size: cover; 
            background-position: center; 
            color: white; 
            padding: 140px 0 100px; 
            border-radius: 0 0 50% 50% / 10%; 
        } 

        .hero-section h1 {
            font-weight: 800;
            letter-spacing: -1px;
        }

        /* Card Customization */
        .feature-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 20px;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
        }

        .feature-icon { 
            width: 70px;
            height: 70px;
            background: #eefaf3;
            color: #198754;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            font-size: 1.8rem; 
            margin: 0 auto 1.5rem;
            transition: 0.3s;
        }

        .feature-card:hover .feature-icon {
            background: #198754;
            color: white;
        }

        .navbar-logo {
            height: 45px;
            filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.1));
        }

        .btn-warning {
            background-color: #ffc107;
            border: none;
            color: #212529;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.4);
        }

        footer {
            background: #151d19 !important;
        }
    </style> 
</head> 
<body class="bg-light d-flex flex-column min-vh-100"> 
 
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm"> 
        <div class="container"> 
            <a class="navbar-brand d-flex align-items-center fw-bold" href="/">
                <img src="{{ asset('images/pusdik1.png') }}" alt="Logo" class="navbar-logo me-2">
                <span class="tracking-tight">PINJAM</span>
            </a> 
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto"> 
                    <a href="{{ route('login') }}" class="btn btn-outline-light px-4 rounded-pill fw-bold">Login</a> 
                </div> 
            </div>
        </div> 
    </nav> 
 

    <div class="hero-section text-center mb-5"> 
        <div class="container"> 
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-3 mb-3">Peminjaman Alat <br><span class="text-warning">Lebih Praktis</span></h1> 
                    <p class="lead mb-5 opacity-90">Solusi manajemen alat laboratorium dan bengkel sekolah yang terintegrasi, transparan, dan dapat dipantau secara real-time.</p> 
                    <a href="{{ route('login') }}" class="btn btn-lg btn-warning fw-bold px-5 py-3 rounded-pill shadow">Mulai Peminjaman <i class="fas fa-arrow-right ms-2"></i></a> 
                </div>
            </div>
        </div> 
    </div> 
 

    <div class="container mb-5"> 
        <div class="row g-4 text-center"> 
            <div class="col-md-4"> 
                <div class="card h-100 shadow-sm feature-card p-4"> 
                    <div class="card-body"> 
                        <div class="feature-icon text-primary">
                            <i class="fas fa-search"></i>
                        </div> 
                        <h4 class="fw-bold">Cari Alat</h4> 
                        <p class="text-muted">Pantau ketersediaan stok alat secara akurat tanpa harus datang ke gudang penyimpanan.</p> 
                    </div> 
                </div> 
            </div> 
            <div class="col-md-4"> 
                <div class="card h-100 shadow-sm feature-card p-4"> 
                    <div class="card-body"> 
                        <div class="feature-icon text-success">
                            <i class="fas fa-file-signature"></i>
                        </div> 
                        <h4 class="fw-bold">Ajukan Pinjaman</h4> 
                        <p class="text-muted">Proses pengajuan digital yang cepat dengan sistem approval otomatis dari petugas.</p> 
                    </div> 
                </div> 
            </div> 
            <div class="col-md-4"> 
                <div class="card h-100 shadow-sm feature-card p-4"> 
                    <div class="card-body"> 
                        <div class="feature-icon text-danger">
                            <i class="fas fa-undo-alt"></i>
                        </div> 
                        <h4 class="fw-bold">Pengembalian</h4> 
                        <p class="text-muted">Notifikasi pengembalian otomatis membantu Anda menjaga aset sekolah tetap aman.</p> 
                    </div> 
                </div> 
            </div> 
        </div> 
    </div> 
 

    <footer class="text-light text-center py-4 mt-auto"> 
        <div class="container"> 
            <p class="mb-0 opacity-75 small">&copy; {{ date('Y') }} <strong>Sistem Peminjaman Alat</strong>. Built with <i class="fas fa-heart text-danger"></i> & Laravel.</p> 
        </div> 
    </footer> 
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> 
</body> 
</html>
