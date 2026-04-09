@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    {{-- Welcome Header --}}
    <div class="welcome-header">
        <h3>
            <i class="fas fa-users me-2" style="color: #3b82f6;"></i>
            Kelola Data Pengguna
        </h3>
        <p>Kelola semua pengguna sistem peminjaman alat.</p>
    </div>

    {{-- Action Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex gap-2" style="max-width: 400px;">
            <form action="{{ route('users.index') }}" method="GET" class="d-flex gap-2 flex-grow-1">
                <input type="text" name="search" class="form-control" placeholder="Cari Nama atau Email..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-secondary">Cari</button>
            </form>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah User Baru
        </a>
    </div>

    {{-- Users Table --}}
    <div class="row">
        <div class="col-md-12">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-list"></i>
                    <h5>Daftar Pengguna</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $key => $user)
                            <tr>
                                <td>{{ $users->firstItem() + $key }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role == 'admin')
                                        <span class="badge-role badge-admin">Admin</span>
                                    @elseif($user->role == 'petugas')
                                        <span class="badge-role badge-user">Petugas</span>
                                    @else
                                        <span class="badge-role badge-user">Peminjam</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm me-1">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>

                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            {{ $user->id == auth()->id() ? 'disabled' : '' }}>
                                            <i class="fas fa-trash me-1"></i>Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <div>Data user tidak ditemukan.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="activity-footer">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection