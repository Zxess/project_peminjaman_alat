@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    {{-- Welcome Header --}}
    <div class="welcome-header">
        <h3>
            <i class="fas fa-tools me-2" style="color: #3b82f6;"></i>
            Kelola Data Alat
        </h3>
        <p>Kelola inventaris alat yang tersedia untuk peminjaman.</p>
    </div>

    {{-- Action Bar --}}
    <div class="d-flex justify-content-end align-items-center mb-4">
        <a href="{{ route('tools.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Alat Baru
        </a>
    </div>

    {{-- Tools Table --}}
    <div class="row">
        <div class="col-md-12">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-list"></i>
                    <h5>Daftar Alat</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Gambar</th>
                                <th>Nama Alat</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tools as $key => $tool)
                            <tr>
                                <td>{{ $tools->firstItem() + $key }}</td>
                                <td>
                                    @if($tool->gambar)
                                        <img src="{{ Storage::url($tool->gambar) }}" alt="{{ $tool->nama_alat }}" class="img-thumbnail" style="height: 60px; width: auto; object-fit: cover;">
                                    @else
                                        <span class="text-muted small">No Image</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $tool->nama_alat }}</div>
                                    <div class="small text-muted text-truncate" style="max-width: 200px;">
                                        {{ $tool->deskripsi }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-role badge-user">
                                        {{ $tool->category->nama_kategori }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $tool->stok }}</span> unit
                                </td>
                                <td>
                                    <a href="{{ route('tools.edit', $tool->id) }}" class="btn btn-warning btn-sm me-1">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>

                                    <form action="{{ route('tools.destroy', $tool->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus alat ini? Data peminjaman terkait mungkin akan error.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash me-1"></i>Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <div>Belum ada data alat. Silakan tambah data baru.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="activity-footer">
                    {{ $tools->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection