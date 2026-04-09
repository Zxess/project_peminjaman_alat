@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    {{-- Welcome Header --}}
    <div class="welcome-header">
        <h3>
            <i class="fas fa-credit-card me-2" style="color: #3b82f6;"></i>
            Proses Pembayaran Denda
        </h3>
        <p>Konfirmasi pembayaran denda keterlambatan.</p>
    </div>

    {{-- Back Button --}}
    <div class="d-flex justify-content-start align-items-center mb-4">
        <a href="{{ route('admin.fines.show', $fine->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- Payment Form --}}
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-money-bill-wave"></i>
                    <h5>Konfirmasi Pembayaran</h5>
                </div>

                <div class="card-body">
                    {{-- Fine Summary --}}
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Ringkasan Denda</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Peminjam:</strong> {{ $fine->loan->user->name ?? '-' }}<br>
                                <strong>Alat:</strong> {{ $fine->loan->tool->nama_alat ?? '-' }}
                            </div>
                            <div class="col-md-6">
                                <strong>Jumlah Denda:</strong> <span class="text-danger fw-semibold">Rp {{ number_format($fine->amount) }}</span><br>
                                <strong>Alasan:</strong> {{ $fine->reason }}
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.fines.process', $fine->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="payment_method" class="form-label fw-semibold">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select class="form-control" id="payment_method" name="payment_method" required>
                                <option value="">Pilih metode pembayaran</option>
                                <option value="cash">Tunai</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="e-wallet">E-Wallet</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label fw-semibold">Catatan</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Tambahkan catatan pembayaran (opsional)"></textarea>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian:</strong> Pastikan pembayaran telah diterima sebelum mengkonfirmasi. Tindakan ini tidak dapat dibatalkan.
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.fines.show', $fine->id) }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-1"></i> Konfirmasi Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection