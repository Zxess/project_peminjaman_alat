@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    {{-- Welcome Header --}}
    <div class="welcome-header">
        <h3>
            <i class="fas fa-money-bill-wave me-2" style="color: #3b82f6;"></i>
            Detail Denda
        </h3>
        <p>Informasi lengkap tentang denda ini.</p>
    </div>

    {{-- Back Button --}}
    <div class="d-flex justify-content-start align-items-center mb-4">
        <a href="{{ route($routePrefix . '.fines.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- Fine Details --}}
    <div class="row">
        <div class="col-md-8">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-info-circle"></i>
                    <h5>Informasi Denda</h5>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">ID Denda</label>
                                <p class="mb-0">#{{ $fine->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <p class="mb-0">
                                    @if($fine->status === 'pending')
                                        <span class="action-badge action-delete">Pending</span>
                                    @else
                                        <span class="action-badge action-login">Dibayar</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Jumlah Denda</label>
                                <p class="mb-0 fs-5 text-danger fw-semibold">Rp {{ number_format($fine->amount) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal Dibuat</label>
                                <p class="mb-0">{{ \Carbon\Carbon::parse($fine->created_at)->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($fine->payment_date)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal Pembayaran</label>
                                <p class="mb-0">{{ \Carbon\Carbon::parse($fine->payment_date)->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan</label>
                        <p class="mb-0">{{ $fine->reason }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-user"></i>
                    <h5>Informasi Peminjam</h5>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama</label>
                        <p class="mb-0">{{ $fine->loan->user->name ?? '-' }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <p class="mb-0">{{ $fine->loan->user->email ?? '-' }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role</label>
                        <p class="mb-0">
                            <span class="badge-role badge-user">{{ ucfirst($fine->loan->user->role ?? 'peminjam') }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="activity-card mt-3">
                <div class="activity-header">
                    <i class="fas fa-tools"></i>
                    <h5>Informasi Alat</h5>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Alat</label>
                        <p class="mb-0">{{ $fine->loan->tool->nama_alat ?? '-' }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori</label>
                        <p class="mb-0">{{ $fine->loan->tool->category->nama_kategori ?? '-' }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Alat</label>
                        <p class="mb-0">{{ $fine->loan->tool->kode_alat ?? '-' }}</p>
                    </div>
                </div>
            </div>

            @if($fine->status === 'pending')
            <div class="activity-card mt-3">
                <div class="activity-header">
                    <i class="fas fa-credit-card"></i>
                    <h5>Aksi</h5>
                </div>

                <div class="card-body">
                    <a href="{{ route($routePrefix . '.fines.pay', $fine->id) }}" class="btn btn-success w-100">
                        <i class="fas fa-credit-card me-1"></i> Proses Pembayaran
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection