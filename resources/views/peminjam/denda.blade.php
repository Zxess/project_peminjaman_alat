@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    {{-- Welcome Header --}}
    <div class="welcome-header">
        <h3>
            <i class="fas fa-money-bill-wave me-2" style="color: #3b82f6;"></i>
            Denda Saya
        </h3>
        <p><strong>{{ auth()->user()->name }}</strong>, lihat status denda peminjaman alat Anda.</p>
    </div>

    {{-- Stats Row --}}
    <div class="row stats-row">
        <div class="col-md-4">
            <div class="stat-card stat-card-danger">
                <div class="stat-card-header">
                    <i class="fas fa-clock me-2"></i> Denda Pending
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $pendingFines->count() }}</div>
                    <div class="stat-text">Belum Dibayar</div>
                </div>
                <div class="stat-card-footer">
                    <span class="text-muted">Rp {{ number_format($totalPendingAmount) }}</span>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card stat-card-success">
                <div class="stat-card-header">
                    <i class="fas fa-check-circle me-2"></i> Denda Dibayar
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $paidFines->count() }}</div>
                    <div class="stat-text">Sudah Lunas</div>
                </div>
                <div class="stat-card-footer">
                    <span class="text-muted">Rp {{ number_format($totalPaidAmount) }}</span>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card stat-card-info">
                <div class="stat-card-header">
                    <i class="fas fa-coins me-2"></i> Total Denda
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $fines->count() }}</div>
                    <div class="stat-text">Semua Transaksi</div>
                </div>
                <div class="stat-card-footer">
                    <span class="text-muted">Rp {{ number_format($totalPendingAmount + $totalPaidAmount) }}</span>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Important Notice --}}
    @if($totalPendingAmount > 0)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>PENTING!</strong> Anda memiliki denda yang belum dibayar sebesar
        <strong>Rp {{ number_format($totalPendingAmount) }}</strong>.
        <br><small>Silakan hubungi administrator untuk informasi pembayaran denda.</small>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Fines Table --}}
    <div class="row">
        <div class="col-md-12">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-list"></i>
                    <h5>Riwayat Denda</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Alat</th>
                                <th>Jumlah Denda</th>
                                <th>Status</th>
                                <th>Tanggal Dibuat</th>
                                <th>Tanggal Bayar</th>
                                <th>Alasan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($fines as $key => $fine)
                            <tr class="{{ $fine->status === 'pending' ? 'table-danger' : 'table-success' }}">
                                <td class="text-center fw-semibold">
                                    {{ $fines->firstItem() + $key }}
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $fine->loan->tool->nama_alat ?? '-' }}</div>
                                    <span class="badge-role badge-user">{{ $fine->loan->tool->category->nama_kategori ?? '-' }}</span>
                                </td>

                                <td>
                                    <span class="fw-semibold text-danger">Rp {{ number_format($fine->amount) }}</span>
                                </td>

                                <td>
                                    @if($fine->status === 'pending')
                                        <span class="action-badge action-delete">
                                            <i class="fas fa-clock me-1"></i>Belum Dibayar
                                        </span>
                                    @else
                                        <span class="action-badge action-login">
                                            <i class="fas fa-check-circle me-1"></i>Sudah Dibayar
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($fine->created_at)->format('d M Y') }}
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($fine->created_at)->format('H:i') }}</small>
                                </td>

                                <td>
                                    @if($fine->payment_date)
                                        {{ \Carbon\Carbon::parse($fine->payment_date)->format('d M Y') }}
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($fine->payment_date)->format('H:i') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    <small>{{ $fine->reason }}</small>
                                </td>
                            </tr>

                            @empty
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <i class="fas fa-check-circle"></i>
                                    <div>Bagus! Anda tidak memiliki denda.</div>
                                    <small class="text-muted">Pastikan selalu mengembalikan alat tepat waktu.</small>
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

    {{-- Payment Instructions --}}
    @if($totalPendingAmount > 0)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-info-circle"></i>
                    <h5>Cara Pembayaran Denda</h5>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-credit-card text-primary me-2"></i>Metode Pembayaran</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Tunai</li>
                                <li><i class="fas fa-check text-success me-2"></i>Transfer Bank</li>
                                <li><i class="fas fa-check text-success me-2"></i>E-Wallet</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-user-tie text-primary me-2"></i>Hubungi Administrator</h6>
                            <p class="mb-1">Untuk informasi lebih lanjut tentang pembayaran denda, silakan hubungi:</p>
                            <p class="mb-0"><strong>Administrator Sistem</strong></p>
                            <small class="text-muted">Pastikan membawa bukti identitas saat melakukan pembayaran.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection