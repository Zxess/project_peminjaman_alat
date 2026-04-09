@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    {{-- Welcome Header --}}
    <div class="welcome-header">
        <h3>
            <i class="fas fa-check-circle me-2" style="color: #3b82f6;"></i>
            Data Pengembalian Alat
        </h3>
        <p>Pantau dan kelola semua transaksi pengembalian alat.</p>
    </div>

    {{-- Action Bar --}}
    <div class="d-flex justify-content-end align-items-center mb-4">
        <a href="{{ route('admin.returns.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-1"></i> Proses Pengembalian
        </a>
    </div>

    {{-- Returns Table --}}
    <div class="row">
        <div class="col-md-12">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-list"></i>
                    <h5>Riwayat Pengembalian</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Petugas</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($returns as $key => $r)
                            <tr class="{{ $r->tanggal_kembali_aktual > $r->tanggal_kembali_rencana ? 'table-danger' : '' }}">

                                <td class="text-center fw-semibold">
                                    {{ $returns->firstItem() + $key }}
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $r->user->name ?? '-' }}</div>
                                    <span class="badge-role badge-user">{{ ucfirst($r->user->role ?? 'peminjam') }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $r->tool->nama_alat ?? '-' }}</div>
                                    <small class="text-muted">{{ $r->tool->category->nama_kategori ?? '-' }}</small>
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($r->tanggal_pinjam)->format('d M Y') }}
                                </td>

                                <td>
                                    <div>{{ \Carbon\Carbon::parse($r->tanggal_kembali_aktual)->format('d M Y') }}</div>
                                    @if($r->tanggal_kembali_aktual > $r->tanggal_kembali_rencana)
                                        <span class="action-badge action-delete">Telat</span>
                                    @else
                                        <span class="action-badge action-login">Tepat</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="fw-semibold">{{ $r->petugas->name ?? 'Admin' }}</div>
                                    <span class="badge-role badge-admin">{{ ucfirst($r->petugas->role ?? 'admin') }}</span>
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('admin.returns.edit', $r->id) }}" class="btn btn-warning btn-sm me-1">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>

                                    <form action="{{ route('admin.returns.destroy', $r->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus riwayat ini?');">
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
                                <td colspan="7" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <div>Belum ada data pengembalian.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="activity-footer">
                    {{ $returns->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 