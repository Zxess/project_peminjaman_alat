@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    {{-- Welcome Header --}}
    <div class="welcome-header">
        <h3>
            <i class="fas fa-money-bill-wave me-2" style="color: #3b82f6;"></i>
            Manajemen Denda
        </h3>
        <p>Kelola denda dan pembayaran keterlambatan pengembalian alat.</p>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h4>{{ $fines->where('status', 'pending')->count() }}</h4>
                    <p>Denda Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h4>{{ $fines->where('status', 'paid')->count() }}</h4>
                    <p>Denda Dibayar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h4>Rp {{ number_format($fines->where('status', 'pending')->sum('amount')) }}</h4>
                    <p>Total Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-content">
                    <h4>Rp {{ number_format($fines->where('status', 'paid')->sum('amount')) }}</h4>
                    <p>Total Dibayar</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Fines Table --}}
    <div class="row">
        <div class="col-md-12">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-list"></i>
                    <h5>Daftar Denda</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th>Jumlah Denda</th>
                                <th>Status</th>
                                <th>Tgl Dibuat</th>
                                <th>Tgl Bayar</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($fines as $key => $fine)
                            <tr>
                                <td class="text-center fw-semibold">
                                    {{ $fines->firstItem() + $key }}
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $fine->loan->user->name ?? '-' }}</div>
                                    <span class="badge-role badge-user">{{ ucfirst($fine->loan->user->role ?? 'peminjam') }}</span>
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $fine->loan->tool->nama_alat ?? '-' }}</div>
                                    <small class="text-muted">{{ $fine->loan->tool->category->nama_kategori ?? '-' }}</small>
                                </td>

                                <td>
                                    <span class="fw-semibold text-danger">Rp {{ number_format($fine->amount) }}</span>
                                </td>

                                <td>
                                    @if($fine->status === 'pending')
                                        <span class="action-badge action-delete">Pending</span>
                                    @else
                                        <span class="action-badge action-login">Dibayar</span>
                                    @endif
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($fine->created_at)->format('d M Y') }}
                                </td>

                                <td>
                                    {{ $fine->payment_date ? \Carbon\Carbon::parse($fine->payment_date)->format('d M Y') : '-' }}
                                </td>

                                <td class="text-center">
                                    <a href="{{ route($routePrefix . '.fines.show', $fine->id) }}" class="btn btn-info btn-sm me-1">
                                        <i class="fas fa-eye me-1"></i>Lihat
                                    </a>

                                    @if($fine->status === 'pending')
                                        <a href="{{ route($routePrefix . '.fines.pay', $fine->id) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-credit-card me-1"></i>Bayar
                                        </a>
                                    @endif
                                </td>
                            </tr>

                            @empty
                            <tr>
                                <td colspan="8" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <div>Belum ada data denda.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="activity-footer">
                    {{ $fines->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection