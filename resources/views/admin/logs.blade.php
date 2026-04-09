@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    {{-- Welcome Header --}}
    <div class="welcome-header">
        <h3>
            <i class="fas fa-history me-2" style="color: #3b82f6;"></i>
            Log Aktivitas Sistem
        </h3>
        <p>Pantau semua aktivitas yang terjadi di sistem peminjaman alat.</p>
    </div>

    {{-- Stats Row --}}
    <div class="row stats-row">
        <div class="col-md-3">
            <div class="stat-card stat-card-info">
                <div class="stat-card-header">
                    <i class="fas fa-sign-in-alt me-2"></i> Login
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $logs->where('action', 'login')->count() }}</div>
                    <div class="stat-text">Aktivitas Login</div>
                </div>
                <div class="stat-card-footer">
                    <span class="text-muted">Total</span>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card stat-card-success">
                <div class="stat-card-header">
                    <i class="fas fa-plus me-2"></i> Create
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $logs->where('action', 'create')->orWhere('action', 'tambah')->count() }}</div>
                    <div class="stat-text">Data Ditambah</div>
                </div>
                <div class="stat-card-footer">
                    <span class="text-muted">Total</span>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card stat-card-warning">
                <div class="stat-card-header">
                    <i class="fas fa-edit me-2"></i> Update
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $logs->where('action', 'update')->orWhere('action', 'edit')->count() }}</div>
                    <div class="stat-text">Data Diubah</div>
                </div>
                <div class="stat-card-footer">
                    <span class="text-muted">Total</span>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card stat-card-danger">
                <div class="stat-card-header">
                    <i class="fas fa-trash me-2"></i> Delete
                </div>
                <div class="stat-card-body">
                    <div class="stat-number">{{ $logs->where('action', 'delete')->orWhere('action', 'hapus')->count() }}</div>
                    <div class="stat-text">Data Dihapus</div>
                </div>
                <div class="stat-card-footer">
                    <span class="text-muted">Total</span>
                    <span class="arrow-icon"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Logs Table --}}
    <div class="row">
        <div class="col-md-12">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-list"></i>
                    <h5>Semua Aktivitas Sistem</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Aksi</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td>
                                    <span class="time-text">
                                        <i class="far fa-clock"></i>
                                        {{ $log->created_at->diffForHumans() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold" style="font-size: 13px;">{{ $log->user->name }}</div>
                                    <span class="badge-role {{ $log->user->role == 'admin' ? 'badge-admin' : 'badge-user' }}">
                                        {{ ucfirst($log->user->role) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $actionClass = match(strtolower($log->action)) {
                                            'login' => 'action-login',
                                            'create', 'tambah', 'menambah' => 'action-create',
                                            'update', 'edit', 'mengubah' => 'action-update',
                                            'delete', 'hapus', 'menghapus' => 'action-delete',
                                            default => 'action-default'
                                        };
                                        $actionIcon = match(strtolower($log->action)) {
                                            'login' => 'sign-in-alt',
                                            'create', 'tambah' => 'plus',
                                            'update', 'edit' => 'edit',
                                            'delete', 'hapus' => 'trash',
                                            default => 'bell'
                                        };
                                    @endphp
                                    <span class="action-badge {{ $actionClass }}">
                                        <i class="fas fa-{{ $actionIcon }} me-1"></i>
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ Str::limit($log->description, 60) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <div>Belum ada aktivitas tercatat.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="activity-footer">
                    {{ $logs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection