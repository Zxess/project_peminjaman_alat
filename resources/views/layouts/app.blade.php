<!DOCTYPE html> 
<html lang="id"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Aplikasi Peminjaman Alat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Navbar Modern */
        .navbar-modern {
            background: white !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid #e9eef3;
            padding: 12px 0;
        }

        .navbar-logo {
            height: 40px;
            width: auto;
            max-width: 50px;
            border-radius: 10px;
        }

        .navbar-brand-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .navbar-brand-text {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            letter-spacing: -0.2px;
        }

        .navbar-brand-text small {
            font-size: 11px;
            font-weight: 400;
            color: #64748b;
            display: block;
            margin-top: 2px;
        }

        /* Navbar Links */
        .nav-links {
            display: flex;
            gap: 4px;
            margin-left: 32px;
        }

        .nav-item-custom {
            list-style: none;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .nav-link-custom i {
            font-size: 14px;
            color: #94a3b8;
        }

        .nav-link-custom:hover {
            background: #f1f5f9;
            color: #3b82f6;
        }

        .nav-link-custom:hover i {
            color: #3b82f6;
        }

        .nav-link-custom.active {
            background: #eff6ff;
            color: #3b82f6;
        }

        .nav-link-custom.active i {
            color: #3b82f6;
        }

        /* User Dropdown */
        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 12px;
            background: #f8fafc;
            border-radius: 40px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            border: 1px solid #e9eef3;
        }

        .user-dropdown:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            line-height: 1.3;
        }

        .user-role {
            font-size: 10px;
            font-weight: 500;
            color: #64748b;
        }

        .dropdown-icon {
            color: #94a3b8;
            font-size: 12px;
        }

        /* Dropdown Menu Modern */
        .dropdown-menu-modern {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            padding: 8px 0;
            margin-top: 8px;
            min-width: 180px;
        }

        .dropdown-item-custom {
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }

        .dropdown-item-custom:hover {
            background: #f1f5f9;
            color: #ef4444;
        }

        .dropdown-item-custom i {
            font-size: 14px;
            width: 18px;
        }

        /* Login Button */
        .btn-login-modern {
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 8px 20px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-login-modern:hover {
            background: #2563eb;
            color: white;
        }

        /* Mobile Toggle */
        .navbar-toggler-custom {
            border: none;
            background: transparent;
            padding: 8px;
        }

        .navbar-toggler-custom i {
            font-size: 24px;
            color: #1e293b;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .nav-links {
                margin-left: 0;
                margin-top: 16px;
                flex-direction: column;
                width: 100%;
            }
            
            .nav-link-custom {
                padding: 10px 0;
            }
            
            .user-dropdown {
                margin-top: 12px;
                width: fit-content;
            }
        }

        /* Alert styling */
        .alert-modern {
            border: none;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 24px;
            font-size: 13px;
        }

        .alert-success-modern {
            background: #e6f7e6;
            color: #10b981;
            border-left: 3px solid #10b981;
        }

        .alert-danger-modern {
            background: #fee2e2;
            color: #dc2626;
            border-left: 3px solid #dc2626;
        }

        .alert-danger-modern ul {
            margin: 0;
            padding-left: 20px;
        }

        /* Dashboard Container */
        .dashboard-container {
            padding: 8px 0 24px 0;
        }

        /* Welcome Header */
        .welcome-header {
            margin-bottom: 28px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e9eef3;
        }

        .welcome-header h3 {
            font-size: 24px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .welcome-header p {
            font-size: 14px;
            color: #64748b;
            margin: 0;
        }

        /* Stats Grid - Menggunakan row col asli tapi di-styling ulang */
        .stats-row {
            margin-bottom: 28px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e9eef3;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: all 0.2s ease;
            overflow: hidden;
            height: 100%;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        /* Warna solid diganti dengan aksen garis gradient */
        .stat-card-primary .stat-card-header {
            border-left: 4px solid #3b82f6;
        }
        .stat-card-success .stat-card-header {
            border-left: 4px solid #10b981;
        }
        .stat-card-warning .stat-card-header {
            border-left: 4px solid #f59e0b;
        }
        .stat-card-danger .stat-card-header {
            border-left: 4px solid #ef4444;
        }
        .stat-card-info .stat-card-header {
            border-left: 4px solid #06b6d4;
        }

        .stat-card-header {
            padding: 16px 20px;
            background: #fafbfc;
            border-bottom: 1px solid #eef2f6;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            letter-spacing: 0.3px;
        }

        .stat-card-body {
            padding: 20px;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.2;
            margin-bottom: 6px;
        }

        .stat-number small {
            font-size: 14px;
            font-weight: 500;
            color: #64748b;
        }

        .stat-text {
            font-size: 13px;
            color: #64748b;
            margin: 0;
        }

        .stat-card-footer {
            padding: 12px 20px;
            background: #fafbfc;
            border-top: 1px solid #eef2f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-link {
            font-size: 12px;
            font-weight: 500;
            color: #3b82f6;
            text-decoration: none;
            transition: all 0.2s;
        }

        .stat-link:hover {
            color: #2563eb;
            text-decoration: underline;
        }

        .arrow-icon {
            font-size: 12px;
            color: #94a3b8;
            transition: transform 0.2s;
        }

        .stat-card:hover .arrow-icon {
            transform: translateX(4px);
            color: #3b82f6;
        }

        /* Activity Card */
        .activity-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e9eef3;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            margin-top: 8px;
        }

        .activity-header {
            padding: 16px 20px;
            background: #fafbfc;
            border-bottom: 1px solid #eef2f6;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .activity-header i {
            font-size: 18px;
            color: #3b82f6;
        }

        .activity-header h5 {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        /* Table Modern */
        .table-modern {
            margin-bottom: 0;
        }

        .table-modern thead th {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
            font-weight: 600;
            font-size: 12px;
            padding: 12px 16px;
            letter-spacing: 0.3px;
        }

        .table-modern tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 13px;
            vertical-align: middle;
        }

        .table-modern tbody tr:hover {
            background: #fafbfc;
        }

        /* Badge Role */
        .badge-role {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 600;
        }

        .badge-admin {
            background: #f1f5f9;
            color: #475569;
        }

        .badge-user {
            background: #e6f7e6;
            color: #10b981;
        }

        /* Action Badge */
        .action-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 500;
        }

        .action-login {
            background: #e6f7e6;
            color: #10b981;
        }

        .action-create {
            background: #e6f0ff;
            color: #3b82f6;
        }

        .action-update {
            background: #fef3e2;
            color: #f59e0b;
        }

        .action-delete {
            background: #fee2e2;
            color: #ef4444;
        }

        .action-default {
            background: #f1f5f9;
            color: #64748b;
        }

        /* Time text */
        .time-text {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #64748b;
        }

        /* Button Modern */
        .btn-modern {
            background: transparent;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 500;
            color: #475569;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-modern:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #1e293b;
        }

        .activity-footer {
            padding: 14px 20px;
            background: #fafbfc;
            border-top: 1px solid #eef2f6;
            text-align: right;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 40px;
            margin-bottom: 12px;
            opacity: 0.5;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stat-number {
                font-size: 28px;
            }
            
            .table-modern {
                min-width: 550px;
            }
            
            .stat-card-header, .stat-card-body, .stat-card-footer {
                padding-left: 16px;
                padding-right: 16px;
            }
        }
    </style>
</head> 
<body style="background: #f8fafc;"> 
    {{-- Navbar Modern --}}
    <nav class="navbar-modern">
        <div class="container">
            <!-- Logo + Brand -->
            <a class="navbar-brand-custom" href="/">
                <img src="{{ asset('images/pusdik1.png') }}" alt="Logo" class="navbar-logo">
                <div class="navbar-brand-text">
                    Sistem Peminjaman
                    <small>Pusdikhubad</small>
                </div>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler-custom d-lg-none" type="button" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Navbar Menu -->
            <div class="navbar-menu" id="navbarMenu" style="display: none;">
                <ul class="nav-links">
                    @auth 
                        @if(auth()->user()->role == 'admin') 
                            <li class="nav-item-custom">
                                <a class="nav-link-custom {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="/admin/dashboard">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li> 
                            <li class="nav-item-custom">
                                <a class="nav-link-custom {{ request()->is('categories*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                    <i class="fas fa-tags"></i> Kategori
                                </a>
                            </li> 
                            <li class="nav-item-custom">
                                <a class="nav-link-custom {{ request()->is('tools*') ? 'active' : '' }}" href="{{ route('tools.index') }}">
                                    <i class="fas fa-tools"></i> Alat
                                </a>
                            </li> 
                            <li class="nav-item-custom">
                                <a class="nav-link-custom {{ request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                    <i class="fas fa-users"></i> User
                                </a>
                            </li> 
                            <li class="nav-item-custom">
                                <a class="nav-link-custom {{ request()->is('admin/loans*') ? 'active' : '' }}" href="{{ route('admin.loans.index') }}">
                                    <i class="fas fa-hand-holding"></i> Peminjaman
                                </a>
                            </li> 
                            <li class="nav-item-custom">
                                <a class="nav-link-custom {{ request()->is('admin/returns*') ? 'active' : '' }}" href="{{ route('admin.returns.index') }}">
                                    <i class="fas fa-check-circle"></i> Pengembalian
                                </a>
                            </li> 
                            <li class="nav-item-custom">
                                <a class="nav-link-custom {{ request()->is('admin/fines*') ? 'active' : '' }}" href="{{ route('admin.fines.index') }}">
                                    <i class="fas fa-money-bill-wave"></i> Denda
                                </a>
                            </li> 
                        @elseif(auth()->user()->role == 'petugas') 
                            @php
                                $pendingFinesCount = \App\Models\Fine::where('status', 'pending')->count();
                            @endphp
                            <li class="nav-item-custom">
                                <a class="nav-link-custom {{ request()->is('petugas/dashboard') ? 'active' : '' }}" href="/petugas/dashboard">
                                    <i class="fas fa-check-double"></i> Validasi
                                </a>
                            </li> 
                            <li class="nav-item-custom">
                                <a class="nav-link-custom {{ request()->is('petugas/fines*') ? 'active' : '' }}" href="{{ route('petugas.fines.index') }}">
                                    <i class="fas fa-money-bill-wave"></i> Denda
                                    @if($pendingFinesCount > 0)
                                        <span class="badge bg-danger ms-1">{{ $pendingFinesCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item-custom">
                                <a class="nav-link-custom {{ request()->is('petugas/laporan') ? 'active' : '' }}" href="/petugas/laporan">
                                    <i class="fas fa-chart-line"></i> Laporan
                                </a>
                            </li> 
                        @elseif(auth()->user()->role == 'peminjam') 
                            @php
                                $pendingFinesCount = \App\Models\Fine::whereHas('loan', function($query) {
                                    $query->where('user_id', auth()->id());
                                })->where('status', 'pending')->count();
                                
                                $overdueLoansCount = \App\Models\Loan::where('user_id', auth()->id())
                                    ->where('status', 'disetujui')
                                    ->where('tanggal_kembali_rencana', '<', now())
                                    ->count();
                            @endphp
                            <li class="nav-item-custom">
                                <a class="nav-link-custom {{ request()->is('peminjam/dashboard') ? 'active' : '' }}" href="/peminjam/dashboard">
                                    <i class="fas fa-boxes"></i> Alat
                                    @if($pendingFinesCount > 0)
                                        <span class="badge bg-danger ms-1">{{ $pendingFinesCount }}</span>
                                    @endif
                                </a>
                            </li> 
                            <li class="nav-item-custom">
                                <a class="nav-link-custom {{ request()->is('peminjam/riwayat') ? 'active' : '' }}" href="/peminjam/riwayat">
                                    <i class="fas fa-history"></i> Riwayat
                                    @if($overdueLoansCount > 0)
                                        <span class="badge bg-warning ms-1">{{ $overdueLoansCount }}</span>
                                    @endif
                                </a>
                            </li> 
                            <li class="nav-item-custom">
                                <a class="nav-link-custom {{ request()->is('peminjam/denda') ? 'active' : '' }}" href="/peminjam/denda">
                                    <i class="fas fa-money-bill-wave"></i> Denda
                                    @if($pendingFinesCount > 0)
                                        <span class="badge bg-danger ms-1">{{ $pendingFinesCount }}</span>
                                    @endif
                                </a>
                            </li> 
                        @endif 
                    @endauth 
                </ul>

                <!-- User Menu -->
                <ul class="nav-links" style="margin-left: auto;">
                    @auth 
                        <li class="nav-item-custom">
                            <div class="dropdown">
                                <a class="user-dropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                    <div class="user-info">
                                        <span class="user-name">{{ auth()->user()->name }}</span>
                                        <span class="user-role">{{ ucfirst(auth()->user()->role) }}</span>
                                    </div>
                                    <i class="fas fa-chevron-down dropdown-icon"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-modern">
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf 
                                            <button type="submit" class="dropdown-item-custom w-100 text-start" style="background: none; border: none; cursor: pointer;">
                                                <i class="fas fa-sign-out-alt"></i> Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </li> 
                    @else 
                        <li class="nav-item-custom">
                            <a class="btn-login-modern" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li> 
                    @endauth 
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container" style="padding-top: 24px; padding-bottom: 40px;"> 
        @if(session('success')) 
            <div class="alert alert-modern alert-success-modern">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div> 
        @endif 
        
        @if($errors->any()) 
            <div class="alert alert-modern alert-danger-modern"> 
                <i class="fas fa-exclamation-circle me-2"></i>
                <ul class="mb-0 mt-1"> 
                    @foreach($errors->all() as $error) 
                        <li>{{ $error }}</li> 
                    @endforeach 
                </ul> 
            </div> 
        @endif 

        @yield('content') 
    </div> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('navbarMenu');
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'flex';
                menu.style.flexDirection = 'column';
                menu.style.width = '100%';
                menu.style.marginTop = '16px';
            } else {
                menu.style.display = 'none';
            }
        }

        // Responsive: reset display on window resize
        window.addEventListener('resize', function() {
            const menu = document.getElementById('navbarMenu');
            if (window.innerWidth >= 992) {
                menu.style.display = 'flex';
                menu.style.flexDirection = 'row';
            } else {
                if (menu.style.display !== 'none') {
                    menu.style.display = 'none';
                }
            }
        });

        // Initialize display for desktop
        if (window.innerWidth >= 992) {
            document.getElementById('navbarMenu').style.display = 'flex';
            document.getElementById('navbarMenu').style.flexDirection = 'row';
        }
    </script>
</body> 
</html>