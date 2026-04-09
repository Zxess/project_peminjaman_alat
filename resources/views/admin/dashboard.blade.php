@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    {{-- Welcome Header --}}
    <div class="welcome-header">
        <h3>
            <i class="fas fa-tachometer-alt me-2" style="color: #3b82f6;"></i>
            Dashboard Administrator
        </h3>
        <p>Selamat datang, <strong>{{ auth()->user()->name }}</strong>! Kelola peminjaman alat dengan mudah.</p>
    </div>

    {{-- Overdue Loans Alert --}}
    @if($overdueLoans->count() > 0)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>PERHATIAN!</strong> Ada <strong>{{ $overdueLoans->count() }}</strong> peminjaman yang sudah melewati batas waktu pengembalian.
        <br><small>Silakan proses pengembalian segera untuk menghindari denda tambahan.</small>
        <div class="mt-2">
            <a href="{{ route('admin.loans.index') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-eye me-1"></i> Lihat Peminjaman Terlambat
            </a>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Row 1: Total Pengguna, Data Alat, Kategori --}}
    <div class="row stats-row">
        <div class="col-md-4">
            <div class="stat-card stat-card-primary">
                <div class="stat-card-header">
                    <i class="fas fa-users me-2"></i> Total Pengguna
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $totalUser }}</div>
                    <div class="stat-text">User Terdaftar</div>
                </div>
                <div class="stat-card-footer">
                    <a href="{{ route('users.index') }}" class="stat-link">Lihat Detail</a>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card stat-card-success">
                <div class="stat-card-header">
                    <i class="fas fa-tools me-2"></i> Data Alat
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $totalAlat }} <small>(Stok: {{ $totalStok }})</small></div>
                    <div class="stat-text">Jenis Alat Tersedia</div>
                </div>
                <div class="stat-card-footer">
                    <a href="{{ route('tools.index') }}" class="stat-link">Lihat Detail</a>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card stat-card-warning">
                <div class="stat-card-header">
                    <i class="fas fa-tags me-2"></i> Kategori
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $totalKategori }}</div>
                    <div class="stat-text">Kategori Alat</div>
                </div>
                <div class="stat-card-footer">
                    <a href="{{ route('categories.index') }}" class="stat-link">Lihat Detail</a>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 2: Sedang Dipinjam, Sudah Dikembalikan --}}
    <div class="row stats-row">
        <div class="col-md-6">
            <div class="stat-card stat-card-danger">
                <div class="stat-card-header">
                    <i class="fas fa-hand-holding me-2"></i> Sedang Dipinjam
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $sedangDipinjam }}</div>
                    <div class="stat-text">Transaksi Aktif</div>
                </div>
                <div class="stat-card-footer">
                    <a href="{{ route('admin.loans.index') }}" class="stat-link">Pantau</a>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="stat-card stat-card-info">
                <div class="stat-card-header">
                    <i class="fas fa-check-circle me-2"></i> Sudah Dikembalikan
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $sudahDikembalikan }}</div>
                    <div class="stat-text">Transaksi Selesai</div>
                </div>
                <div class="stat-card-footer">
                    <a href="{{ route('admin.returns.index') }}" class="stat-link">Pantau</a>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 3: Denda Pending, Total Denda --}}
    <div class="row stats-row">
        <div class="col-md-6">
            <div class="stat-card stat-card-warning">
                <div class="stat-card-header">
                    <i class="fas fa-clock me-2"></i> Denda Pending
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $pendingFines }}</div>
                    <div class="stat-text">Menunggu Pembayaran</div>
                </div>
                <div class="stat-card-footer">
                    <a href="{{ route('admin.fines.index') }}" class="stat-link">Kelola Denda</a>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="stat-card stat-card-success">
                <div class="stat-card-header">
                    <i class="fas fa-coins me-2"></i> Total Denda
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">Rp {{ number_format($totalPendingFineAmount + $totalPaidFineAmount) }}</div>
                    <div class="stat-text">Pending: Rp {{ number_format($totalPendingFineAmount) }}</div>
                </div>
                <div class="stat-card-footer">
                    <a href="{{ route('admin.fines.index') }}" class="stat-link">Lihat Detail</a>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 3: Aktivitas Sistem Terakhir --}}
    <div class="row">
        <div class="col-md-12">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-history"></i>
                    <h5>Aktivitas Sistem Terakhir</h5>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Aksi</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLogs as $log)
                            <tr>
                                <td>
                                    <span class="time-text">
                                        <i class="far fa-clock"></i>
                                        {{ $log->created_at->diffForHumans() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold" style="font-size: 13px;">{{ $log->user->name }}</div>
                                    <span class="badge-role {{ $log->user->role == 'admin' ? 'badge-admin' : 'badge-user' }}">
                                        {{ ucfirst($log->user->role) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $actionClass = match(strtolower($log->action)) {
                                            'login' => 'action-login',
                                            'create', 'tambah', 'menambah' => 'action-create',
                                            'update', 'edit', 'mengubah' => 'action-update',
                                            'delete', 'hapus', 'menghapus' => 'action-delete',
                                            default => 'action-default'
                                        };
                                        $actionIcon = match(strtolower($log->action)) {
                                            'login' => 'sign-in-alt',
                                            'create', 'tambah' => 'plus',
                                            'update', 'edit' => 'edit',
                                            'delete', 'hapus' => 'trash',
                                            default => 'bell'
                                        };
                                    @endphp
                                    <span class="action-badge {{ $actionClass }}">
                                        <i class="fas fa-{{ $actionIcon }} me-1"></i>
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ Str::limit($log->description, 55) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <div>Belum ada aktivitas tercatat.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="activity-footer">
                    <a href="{{ url('/admin/logs') }}" class="btn-modern">
                        Lihat Semua Log <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection