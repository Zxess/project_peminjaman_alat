@extends('layouts.app')
@section('content')
<div class="dashboard-container">
    {{-- Welcome Header --}}
    <div class="welcome-header">
        <h3>
            <i class="fas fa-history me-2" style="color: #3b82f6;"></i>
            Riwayat Peminjaman
        </h3>
        <p><strong>{{ auth()->user()->name }}</strong>, lihat status peminjaman alat Anda.</p>
    </div>

    {{-- Fine Alert --}}
    @if($totalPendingFine > 0)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Perhatian!</strong> Anda memiliki denda yang belum dibayar sebesar 
        <strong>Rp {{ number_format($totalPendingFine) }}</strong> 
        dari {{ $pendingFines->count() }} transaksi keterlambatan.
        <br><small>Silakan hubungi administrator untuk informasi pembayaran.</small>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Overdue Loans Alert --}}
    @if($overdueLoans->count() > 0)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-clock me-2"></i>
        <strong>Pengingat!</strong> Anda memiliki <strong>{{ $overdueLoans->count() }}</strong> alat yang sudah melewati batas waktu pengembalian.
        <br><small>Mohon segera kembalikan alat ke petugas untuk menghindari denda tambahan.</small>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Stats Row --}}
    <div class="row stats-row">
        <div class="col-md-3">
            <div class="stat-card stat-card-warning">
                <div class="stat-card-header">
                    <i class="fas fa-clock me-2"></i> Menunggu
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $loans->where('status', 'pending')->count() }}</div>
                    <div class="stat-text">Persetujuan</div>
                </div>
                <div class="stat-card-footer">
                    <span class="text-muted">Dalam Proses</span>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card stat-card-info">
                <div class="stat-card-header">
                    <i class="fas fa-hand-holding me-2"></i> Dipinjam
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $loans->where('status', 'disetujui')->count() }}</div>
                    <div class="stat-text">Aktif</div>
                </div>
                <div class="stat-card-footer">
                    <span class="text-muted">Sedang Digunakan</span>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card stat-card-success">
                <div class="stat-card-header">
                    <i class="fas fa-check-circle me-2"></i> Dikembalikan
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $loans->where('status', 'kembali')->count() }}</div>
                    <div class="stat-text">Selesai</div>
                </div>
                <div class="stat-card-footer">
                    <span class="text-muted">Berhasil</span>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card stat-card-danger">
                <div class="stat-card-header">
                    <i class="fas fa-times-circle me-2"></i> Ditolak
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $loans->where('status', 'ditolak')->count() }}</div>
                    <div class="stat-text">Permintaan</div>
                </div>
                <div class="stat-card-footer">
                    <span class="text-muted">Tidak Disetujui</span>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Peminjaman --}}
    <div class="row">
        <div class="col-md-12">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-list"></i>
                    <h5>Detail Riwayat Peminjaman</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Alat</th>
                                <th>Tgl Pinjam</th>
                                <th>Rencana Kembali</th>
                                <th>Status</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                            @php
                                $isOverdue = $loan->status == 'disetujui' && $loan->tanggal_kembali_rencana < now();
                            @endphp
                            <tr class="{{ $isOverdue ? 'table-warning' : '' }}">
                                <td>
                                    <div class="fw-semibold">{{ $loan->tool->nama_alat }}</div>
                                    <span class="badge-role badge-user">{{ $loan->tool->category->nama_kategori }}</span>
                                    @if($isOverdue)
                                        <br><small class="text-warning fw-semibold">
                                            <i class="fas fa-clock me-1"></i>TERLAMBAT!
                                        </small>
                                    @endif
                                </td>
                                <td>{{ $loan->tanggal_pinjam }}</td>
                                <td>
                                    {{ $loan->tanggal_kembali_rencana }}
                                    @if($isOverdue)
                                        <br><small class="text-warning fw-semibold">
                                            Terlambat: {{ $loan->late_duration ?? '0 hari' }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if($loan->status == 'pending')
                                        <span class="action-badge action-update">Menunggu Persetujuan</span>
                                    @elseif($loan->status == 'disetujui')
                                        <span class="action-badge action-create">Sedang Dipinjam</span>
                                        @if($isOverdue)
                                            <br><span class="action-badge action-delete">TERLAMBAT</span>
                                        @endif
                                    @elseif($loan->status == 'dikembalikan')
                                        <span class="action-badge action-warning">Pengembalian Diajukan</span>
                                    @elseif($loan->status == 'kembali')
                                        <span class="action-badge action-login">Sudah Dikembalikan</span>
                                    @elseif($loan->status == 'ditolak')
                                        <span class="action-badge action-delete">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    @if($loan->status == 'disetujui')
                                        <div class="mb-3">
                                            <form action="{{ route('peminjam.return', $loan->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-check me-1"></i>Ajukan Pengembalian
                                                </button>
                                            </form>
                                        </div>
                                        <small class="text-muted">Harap kembalikan ke petugas sebelum tanggal rencana.</small>
                                    @elseif($loan->status == 'dikembalikan')
                                        <span class="badge bg-info">
                                            <i class="fas fa-hourglass-end me-1"></i>Menunggu Verifikasi
                                        </span>
                                        <br><small class="text-info mt-2 d-block">Permintaan pengembalian sudah dikirim. Petugas akan mengupload bukti foto segera.</small>
                                    @elseif($loan->status == 'kembali')
                                        <span class="badge bg-success mb-2 d-block">
                                            <i class="fas fa-check-circle me-1"></i>Pengembalian Disetujui
                                        </span>
                                        <small class="text-success">Diterima tanggal {{ \Carbon\Carbon::parse($loan->tanggal_kembali_aktual)->format('d/m/Y') }}</small>
                                        @if($loan->return_photo_path)
                                            <br><a href="{{ asset('storage/' . $loan->return_photo_path) }}" target="_blank" class="btn btn-link btn-sm mt-1">
                                                <i class="fas fa-image me-1"></i>Lihat Bukti Foto
                                            </a>
                                        @endif
                                        @if($loan->fines->where('status', 'pending')->count() > 0)
                                            <br><small class="text-danger fw-semibold mt-2 d-block">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Denda: Rp {{ number_format($loan->fines->where('status', 'pending')->sum('amount')) }}
                                            </small>
                                        @endif
                                    @elseif($loan->status == 'ditolak')
                                        <span class="badge bg-danger">Permintaan Ditolak</span>
                                        <br><small class="text-danger mt-2 d-block">Persyaratan belum memenuhi</small>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <div>Belum ada riwayat peminjaman.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection