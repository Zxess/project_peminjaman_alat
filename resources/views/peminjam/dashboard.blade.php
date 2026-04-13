@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    {{-- Welcome Header --}}
    <div class="welcome-header">
        <h3>
            <i class="fas fa-boxes me-2" style="color: #3b82f6;"></i>
            Dashboard Peminjam
        </h3>
        <p>Selamat datang, <strong>{{ auth()->user()->name }}</strong>! Pilih alat yang ingin dipinjam.</p>
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
        <div class="mt-2">
            <a href="{{ route('peminjam.riwayat') }}" class="btn btn-warning btn-sm">
                <i class="fas fa-eye me-1"></i> Lihat Riwayat & Kembalikan
            </a>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Pengembalian Alert/Info --}}
    @php
        $activeLoans = \App\Models\Loan::where('user_id', auth()->id())
                                       ->where('status', 'disetujui')
                                       ->with('tool')
                                       ->get();
        $pendingReturns = \App\Models\Loan::where('user_id', auth()->id())
                                          ->where('status', 'dikembalikan')
                                          ->with('tool')
                                          ->get();
    @endphp

    @if($activeLoans->count() > 0)
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Catatan:</strong> Anda memiliki {{ $activeLoans->count() }} alat yang sedang dipinjam. 
        <br><small>Kunjungi halaman <a href="{{ route('peminjam.riwayat') }}" class="alert-link">Riwayat Peminjaman</a> untuk mengajukan pengembalian.</small>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($pendingReturns->count() > 0)
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-hourglass-end me-2"></i>
        <strong>Status Pengembalian:</strong> 
        <br>{{ $pendingReturns->count() }} permintaan pengembalian sedang menunggu verifikasi petugas.
        <br><small>Petugas akan mengupload bukti foto pengembalian alat Anda.</small>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Stats Row --}}
    <div class="row stats-row">
        <div class="col-md-4">
            <div class="stat-card stat-card-success">
                <div class="stat-card-header">
                    <i class="fas fa-tools me-2"></i> Alat Tersedia
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $tools->where('stok', '>', 0)->count() }}</div>
                    <div class="stat-text">Jenis Alat</div>
                </div>
                <div class="stat-card-footer">
                    <span class="text-muted">Dapat Dipinjam</span>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card stat-card-warning">
                <div class="stat-card-header">
                    <i class="fas fa-exclamation-triangle me-2"></i> Stok Terbatas
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $tools->where('stok', '<=', 5)->where('stok', '>', 0)->count() }}</div>
                    <div class="stat-text">Alat dengan Stok ≤5</div>
                </div>
                <div class="stat-card-footer">
                    <span class="text-muted">Perhatian</span>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card stat-card-danger">
                <div class="stat-card-header">
                    <i class="fas fa-times-circle me-2"></i> Stok Habis
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $tools->where('stok', '=', 0)->count() }}</div>
                    <div class="stat-text">Alat Tidak Tersedia</div>
                </div>
                <div class="stat-card-footer">
                    <span class="text-muted">Tidak Dapat Dipinjam</span>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Alat Tersedia --}}
    <div class="row">
        <div class="col-md-12">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-tools"></i>
                    <h5>Daftar Alat Tersedia</h5>
                </div>

                <div class="row">
                    @forelse($tools as $tool)
                    <div class="col-md-4 mb-4">
                        <div class="stat-card h-100">
                            <div class="stat-card-header">
                                <i class="fas fa-toolbox me-2"></i> {{ $tool->nama_alat }}
                            </div>
                            <div class="stat-card-body">
                                <span class="badge-role badge-user mb-2">{{ $tool->category->nama_kategori }}</span>
                                <p class="stat-text mb-3">{{ $tool->deskripsi }}</p>
                                <div class="stat-number small mb-3">Sisa Stok: {{ $tool->stok }}</div>

                                @if($tool->stok > 0)
                                    <form action="{{ url('/peminjam/ajukan') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="tool_id" value="{{ $tool->id }}">
                                        <div class="mb-3">
                                            <label class="form-label small">Tgl Rencana Kembali</label>
                                            <input type="date" name="tanggal_kembali" class="form-control form-control-sm" required min="{{ date('Y-m-d') }}">
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100 btn-sm">Pinjam Alat</button>
                                    </form>
                                @else
                                    <button class="btn btn-secondary w-100 btn-sm" disabled>Stok Habis</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <div>Tidak ada alat tersedia.</div>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection