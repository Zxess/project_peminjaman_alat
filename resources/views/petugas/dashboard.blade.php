@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    {{-- Welcome Header --}}
    <div class="welcome-header">
        <h3>
            <i class="fas fa-check-double me-2" style="color: #3b82f6;"></i>
            Dashboard Petugas
        </h3>
        <p>Selamat datang, <strong>{{ auth()->user()->name }}</strong>! Kelola permintaan peminjaman alat.</p>
    </div>

    {{-- Overdue Loans Alert --}}
    @if($overdueLoans->count() > 0)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>PERHATIAN!</strong> Ada <strong>{{ $overdueLoans->count() }}</strong> peminjaman yang sudah melewati batas waktu pengembalian.
        <br><small>Segera hubungi peminjam untuk mengembalikan alat dan hindari denda tambahan.</small>
        <div class="mt-2">
            <a href="#active-loans" class="btn btn-danger btn-sm">
                <i class="fas fa-eye me-1"></i> Lihat Peminjaman Aktif
            </a>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Stats Row --}}
    <div class="row stats-row">
        <div class="col-md-4">
            <div class="stat-card stat-card-warning">
                <div class="stat-card-header">
                    <i class="fas fa-clock me-2"></i> Menunggu Persetujuan
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $loans->count() }}</div>
                    <div class="stat-text">Permintaan Baru</div>
                </div>
                <div class="stat-card-footer">
                    <span class="text-muted">Perlu Tindakan</span>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card stat-card-info">
                <div class="stat-card-header">
                    <i class="fas fa-hand-holding me-2"></i> Sedang Dipinjam
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $activeLoans->count() }}</div>
                    <div class="stat-text">Transaksi Aktif</div>
                </div>
                <div class="stat-card-footer">
                    <span class="text-muted">Monitor</span>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card stat-card-success">
                <div class="stat-card-header">
                    <i class="fas fa-check-circle me-2"></i> Sudah Dikembalikan
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $sudahDikembalikan->count() }}</div>
                    <div class="stat-text">Transaksi Selesai</div>
                </div>
                <div class="stat-card-footer">
                    <span class="text-muted">Riwayat</span>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Permintaan Peminjaman Masuk --}}
    <div class="row">
        <div class="col-md-12">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-clock"></i>
                    <h5>Permintaan Peminjaman Masuk</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th>Tgl Pinjam</th>
                                <th>Rencana Kembali</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $loan->user->name }}</div>
                                    <span class="badge-role badge-user">{{ ucfirst($loan->user->role) }}</span>
                                </td>
                                <td>{{ $loan->tool->nama_alat }}</td>
                                <td>{{ $loan->tanggal_pinjam }}</td>
                                <td>{{ $loan->tanggal_kembali_rencana }}</td>
                                <td>
                                    <form action="{{ url('/petugas/approve/'.$loan->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-success btn-sm me-1">Setujui</button>
                                    </form>
                                    <form action="{{ url('/petugas/reject/'.$loan->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-danger btn-sm">Tolak</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <div>Tidak ada permintaan baru.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Sedang Dipinjam --}}
    <div class="row">
        <div class="col-md-12">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-hand-holding"></i>
                    <h5>Daftar Sedang Dipinjam (Belum Kembali)</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeLoans as $active)
                            @php
                                $isOverdue = $active->tanggal_kembali_rencana < now();
                            @endphp
                            <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                <td>
                                    <div class="fw-semibold">{{ $active->user->name }}</div>
                                    <span class="badge-role badge-user">{{ ucfirst($active->user->role) }}</span>
                                    @if($isOverdue)
                                        <br><small class="text-danger fw-semibold">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Terlambat!
                                        </small>
                                    @endif
                                </td>
                                <td>{{ $active->tool->nama_alat }}</td>
                                <td>
                                    <span class="action-badge action-create">{{ $active->status }}</span>
                                    @if($isOverdue)
                                        <br><small class="text-danger">Batas: {{ $active->tanggal_kembali_rencana }}</small>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ url('/petugas/return/'.$active->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-primary btn-sm {{ $isOverdue ? 'btn-danger' : '' }}">
                                            @if($isOverdue)
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                            @endif
                                            Proses Pengembalian
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Sudah Dikembalikan --}}
    <div class="row">
        <div class="col-md-12">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-check-circle"></i>
                    <h5>Daftar Sudah Dikembalikan</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sudahDikembalikan as $sudah)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $sudah->user->name }}</div>
                                    <span class="badge-role badge-user">{{ ucfirst($sudah->user->role) }}</span>
                                </td>
                                <td>{{ $sudah->tool->nama_alat }}</td>
                                <td><span class="action-badge action-login">{{ $sudah->status }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection