@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="welcome-header">
        <h3>
            <i class="fas fa-tags me-2" style="color: #3b82f6;"></i>
            Kelola Kategori Alat
        </h3>
        <p>Kelola kategori untuk mengorganisir alat peminjaman.</p>
    </div>

    <div class="d-flex justify-content-end align-items-center mb-4">
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Kategori
        </a>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-list"></i>
                    <h5>Daftar Kategori</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th>Nama Kategori</th>
                                <th width="20%">Jumlah Alat</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $key => $cat)
                            <tr>
                                <td>{{ $categories->firstItem() + $key }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $cat->nama_kategori }}</div>
                                </td>
                                <td>
                                    <span class="badge-role badge-user">
                                        {{ $cat->tools_count }} Item
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('categories.edit', $cat->id) }}" class="btn btn-warning btn-sm me-1">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>

                                    <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kategori ini?');">
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
                                <td colspan="4" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <div>Belum ada kategori.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="activity-footer">
                    {{ $categories->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection