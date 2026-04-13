@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    {{-- Welcome Header --}}
    <div class="welcome-header">
        <h3>
            <i class="fas fa-hand-holding me-2" style="color: #3b82f6;"></i>
            Kelola Data Peminjaman
        </h3>
        <p>Pantau dan kelola semua transaksi peminjaman alat.</p>
    </div>

    {{-- Action Bar --}}
    {{-- <div class="d-flex justify-content-end align-items-center mb-4">
        <a href="{{ route('admin.loans.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Peminjaman Manual
        </a>
    </div> --}}

    {{-- Loans Table --}}
    <div class="row">
        <div class="col-md-12">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-list"></i>
                    <h5>Daftar Peminjaman</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th>Tanggal Pinjam</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $key => $loan)
                            @php
                                $isOverdue = $loan->status == 'disetujui' && $loan->tanggal_kembali_rencana < now();
                            @endphp
                            <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                <td>{{ $loans->firstItem() + $key }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $loan->user->name }}</div>
                                    <span class="badge-role badge-user">{{ ucfirst($loan->user->role) }}</span>
                                    @if($isOverdue)
                                        <br><small class="text-danger fw-semibold">
                                            <i class="fas fa-exclamation-triangle me-1"></i>TERLAMBAT!
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $loan->tool->nama_alat }}</div>
                                    <small class="text-muted">{{ $loan->tool->category->nama_kategori }}</small>
                                </td>
                                <td>
                                    <div>{{ $loan->tanggal_pinjam }}</div>
                                    <small class="text-muted">Kembali: {{ $loan->tanggal_kembali_rencana }}</small>
                                    @if($isOverdue)
                                        <br><small class="text-danger fw-semibold">
                                            Terlambat: {{ \Carbon\Carbon::parse($loan->tanggal_kembali_rencana)->diffInDays(now()) }} hari
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if($loan->status == 'pending')
                                        <span class="action-badge action-update">Pending</span>
                                    @elseif($loan->status == 'disetujui')
                                        <span class="action-badge action-create">Sedang Dipinjam</span>
                                        @if($isOverdue)
                                            <br><span class="action-badge action-delete">TERLAMBAT</span>
                                        @endif
                                    @elseif($loan->status == 'dikembalikan')
                                        <span class="action-badge action-warning">Pengembalian Diajukan</span>
                                    @elseif($loan->status == 'kembali')
                                        <span class="action-badge action-login">Sudah Kembali</span>
                                    @elseif($loan->status == 'ditolak')
                                        <span class="action-badge action-delete">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.loans.edit', $loan->id) }}" class="btn btn-info btn-sm me-1">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>

                                    <form action="{{ route('admin.loans.destroy', $loan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash me-1"></i>Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <div>Tidak ada data peminjaman.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="activity-footer">
                    {{ $loans->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection