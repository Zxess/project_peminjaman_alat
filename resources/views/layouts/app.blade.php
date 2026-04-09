<!DOCTYPE html> 
<html lang="id"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Aplikasi Peminjaman Alat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-logo {
            height: 40px;
            width: auto;
            max-width: 50px;
        }
    </style>
</head> 
<body> 
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <!-- Logo + Brand -->
        <a class="navbar-brand d-flex align-items-center fw-semibold" href="/">
            <img src="{{ asset('images/pusdik1.png') }}" alt="Logo" class="navbar-logo me-2">
            <span>Sistem Peminjaman</span>
        </a>

        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        
        <div class="collapse navbar-collapse" id="navbarNav">
            
            <ul class="navbar-nav me-auto">
                @auth 
                    @if(auth()->user()->role == 'admin') 
                        <li class="nav-item"><a class="nav-link" href="/admin/dashboard">Dashboard</a></li> 
                        <li class="nav-item"><a class="nav-link" href="{{ route('categories.index') }}">Kategori</a></li> 
                        <li class="nav-item"><a class="nav-link" href="{{ route('tools.index') }}">Alat</a></li> 
                        <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">User</a></li> 
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.loans.index') }}">Peminjaman</a></li> 
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.returns.index') }}">Pengembalian</a></li> 
                    @elseif(auth()->user()->role == 'petugas') 
                        <li class="nav-item"><a class="nav-link" href="/petugas/dashboard">Validasi</a></li> 
                        <li class="nav-item"><a class="nav-link" href="/petugas/laporan">Laporan</a></li> 
                    @elseif(auth()->user()->role == 'peminjam') 
                        <li class="nav-item"><a class="nav-link" href="/peminjam/dashboard">Alat</a></li> 
                        <li class="nav-item"><a class="nav-link" href="/peminjam/riwayat">Riwayat</a></li> 
                    @endif 
                @endauth 
            </ul>

            <!-- Menu kanan -->
            <ul class="navbar-nav ms-auto align-items-center">
                @auth 
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <span class="me-1">{{ auth()->user()->name }}</span>
                            <small class="text-light opacity-75">({{ ucfirst(auth()->user()->role) }})</small>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf 
                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li> 
                @else 
                    <li class="nav-item">
                        <a class="btn btn-light btn-sm" href="{{ route('login') }}">Login</a>
                    </li> 
                @endauth 
            </ul>
        </div>
    </div>
</nav>
 
    <div class="container"> 
        @if(session('success')) 
            <div class="alert alert-success">{{ session('success') }}</div> 
        @endif 
        @if($errors->any()) 
            <div class="alert alert-danger"> 
                <ul> 
                    @foreach($errors->all() as $error) 
                        <li>{{ $error }}</li> 
                    @endforeach 
                </ul> 
 
            </div> 
        @endif 
 
        @yield('content') 
    </div> 
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> 
</body> 
</html>