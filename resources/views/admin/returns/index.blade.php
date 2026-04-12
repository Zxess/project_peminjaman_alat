@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    {{-- Welcome Header --}}
    <div class="welcome-header">
        <h3>
            <i class="fas fa-check-circle me-2" style="color: #3b82f6;"></i>
            Data Pengembalian Alat
        </h3>
        <p>Pantau dan kelola semua transaksi pengembalian alat dengan mudah.</p>
    </div>

    {{-- Action Bar --}}
    <div class="d-flex justify-content-end align-items-center mb-4">
        <a href="{{ route('admin.returns.create') }}" class="btn btn-primary btn-modern">
            <i class="fas fa-plus me-2"></i> Proses Pengembalian
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <h4>{{ $returns->total() }}</h4>
                    <p>Total Transaksi</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h4>{{ $returns->where('tanggal_kembali_aktual', '<=', 'tanggal_kembali_rencana')->count() }}</h4>
                    <p>Tepat Waktu</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <h4>{{ $returns->where('tanggal_kembali_aktual', '>', 'tanggal_kembali_rencana')->count() }}</h4>
                    <p>Terlambat</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h4>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</h4>
                    <p>Hari Ini</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Returns Table --}}
    <div class="row">
        <div class="col-md-12">
            <div class="activity-card">
                <div class="activity-header">
                    <div>
                        <i class="fas fa-list me-2"></i>
                        <h5>Riwayat Pengembalian</h5>
                    </div>

                </div>

                <div class="table-responsive">
                    <table class="table table-modern" id="returnsTable">
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
                            <tr class="{{ $r->tanggal_kembali_aktual > $r->tanggal_kembali_rencana ? 'late-return' : '' }}">
                                <td class="text-center fw-semibold">
                                    <span class="badge-number">{{ $returns->firstItem() + $key }}</span>
                                </td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <i class="fas fa-user-circle"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $r->user->name ?? '-' }}</div>
                                            <span class="badge-role {{ $r->user->role ?? 'peminjam' == 'admin' ? 'badge-admin' : 'badge-user' }}">
                                                {{ ucfirst($r->user->role ?? 'peminjam') }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tool-info">
                                        <div class="fw-semibold">{{ $r->tool->nama_alat ?? '-' }}</div>
                                        <small class="text-muted">
                                            <i class="fas fa-tag me-1"></i>
                                            {{ $r->tool->category->nama_kategori ?? '-' }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="date-info">
                                        <i class="fas fa-calendar me-1 text-primary"></i>
                                        {{ \Carbon\Carbon::parse($r->tanggal_pinjam)->isoFormat('DD MMM YYYY') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="return-info">
                                        <div class="date-info">
                                            <i class="fas fa-calendar-check me-1 text-success"></i>
                                            {{ \Carbon\Carbon::parse($r->tanggal_kembali_aktual)->isoFormat('DD MMM YYYY') }}
                                        </div>
                                        @if($r->tanggal_kembali_aktual > $r->tanggal_kembali_rencana)
                                            @php
                                                $lateDays = \Carbon\Carbon::parse($r->tanggal_kembali_aktual)->diffInDays(\Carbon\Carbon::parse($r->tanggal_kembali_rencana));
                                            @endphp
                                            <span class="status-badge status-late">
                                                <i class="fas fa-clock me-1"></i>Terlambat {{ $lateDays }} hari
                                            </span>
                                        @else
                                            <span class="status-badge status-ontime">
                                                <i class="fas fa-check me-1"></i>Tepat Waktu
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="officer-info">
                                        <div class="officer-avatar">
                                            <i class="fas fa-user-check"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $r->petugas->name ?? 'Admin' }}</div>
                                            <span class="badge-role badge-admin">
                                                {{ ucfirst($r->petugas->role ?? 'admin') }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.returns.edit', $r->id) }}" class="btn-action btn-edit" data-bs-toggle="tooltip" title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.returns.destroy', $r->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat pengembalian ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete" data-bs-toggle="tooltip" title="Hapus Data">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <div class="empty-state-content">
                                        <i class="fas fa-inbox"></i>
                                        <h4>Belum Ada Data Pengembalian</h4>
                                        <p>Silakan proses pengembalian alat terlebih dahulu</p>
                                        <a href="{{ route('admin.returns.create') }}" class="btn btn-primary btn-sm mt-3">
                                            <i class="fas fa-plus me-2"></i>Proses Pengembalian
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($returns->hasPages())
                <div class="activity-footer">
                    <div class="pagination-info">
                        Menampilkan {{ $returns->firstItem() }} - {{ $returns->lastItem() }} dari {{ $returns->total() }} data
                    </div>
                    {{ $returns->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    :root {
        --primary-color: #3b82f6;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #06b6d4;
        --dark-color: #1f2937;
        --light-bg: #f9fafb;
        --border-color: #e5e7eb;
    }

    .dashboard-container {
        padding: 20px;
        background: #f3f4f6;
        min-height: 100vh;
    }

    /* Welcome Header */
    .welcome-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    .welcome-header h3 {
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .welcome-header p {
        margin-bottom: 0;
        opacity: 0.9;
    }

    /* Statistics Cards */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
    }

    .stat-icon.bg-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-icon.bg-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .stat-icon.bg-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stat-icon.bg-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }

    .stat-info h4 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 5px 0;
        color: var(--dark-color);
    }

    .stat-info p {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
    }

    /* Activity Card */
    .activity-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    .activity-header {
        padding: 20px 25px;
        border-bottom: 2px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
    }

    .activity-header > div {
        display: flex;
        align-items: center;
    }

    .activity-header i {
        font-size: 24px;
        color: var(--primary-color);
    }

    .activity-header h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: var(--dark-color);
    }


    .btn-icon {
        background: var(--light-bg);
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 8px;
        color: var(--dark-color);
        transition: all 0.3s;
    }

    .btn-icon:hover {
        background: var(--primary-color);
        color: white;
        transform: scale(1.05);
    }

    /* Modern Button */
    .btn-modern {
        padding: 12px 25px;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    /* Table Styles */
    .table-modern {
        margin-bottom: 0;
    }

    .table-modern thead th {
        background: var(--light-bg);
        padding: 15px;
        font-weight: 600;
        font-size: 14px;
        color: var(--dark-color);
        border-bottom: 2px solid var(--border-color);
    }

    .table-modern tbody td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
    }

    .table-modern tbody tr {
        transition: all 0.3s;
    }

    .table-modern tbody tr:hover {
        background: var(--light-bg);
    }

    .late-return {
        background: #fef2f2;
        border-left: 4px solid var(--danger-color);
    }

    /* Badge Number */
    .badge-number {
        display: inline-block;
        width: 30px;
        height: 30px;
        background: var(--primary-color);
        color: white;
        border-radius: 8px;
        line-height: 30px;
        text-align: center;
        font-weight: 600;
        font-size: 13px;
    }

    /* User & Officer Info */
    .user-info, .officer-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-avatar, .officer-avatar {
        width: 35px;
        height: 35px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .badge-role {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 600;
        margin-top: 4px;
    }

    .badge-user {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-admin {
        background: #fef3c7;
        color: #92400e;
    }

    /* Status Badges */
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-top: 5px;
    }

    .status-ontime {
        background: #d1fae5;
        color: #065f46;
    }

    .status-late {
        background: #fee2e2;
        color: #991b1b;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        cursor: pointer;
        border: none;
    }

    .btn-edit {
        background: #fef3c7;
        color: #d97706;
    }

    .btn-edit:hover {
        background: #d97706;
        color: white;
        transform: scale(1.1);
    }

    .btn-delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-delete:hover {
        background: #dc2626;
        color: white;
        transform: scale(1.1);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px !important;
    }

    .empty-state-content i {
        font-size: 80px;
        color: #d1d5db;
        margin-bottom: 20px;
    }

    .empty-state-content h4 {
        font-size: 20px;
        color: var(--dark-color);
        margin-bottom: 10px;
    }

    .empty-state-content p {
        color: #6b7280;
    }

    /* Pagination */
    .activity-footer {
        padding: 20px 25px;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .pagination-info {
        font-size: 14px;
        color: #6b7280;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-container {
            padding: 15px;
        }

        .welcome-header {
            padding: 20px;
        }

        .welcome-header h3 {
            font-size: 1.25rem;
        }

        .activity-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .activity-footer {
            flex-direction: column;
            align-items: center;
        }

        .table-modern {
            font-size: 12px;
        }

        .action-buttons {
            flex-direction: column;
            gap: 5px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Tooltip initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Export functionality
    document.getElementById('exportBtn')?.addEventListener('click', function() {
        const table = document.getElementById('returnsTable');
        const rows = table.querySelectorAll('tr');
        let csv = [];
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('th, td');
            const rowData = Array.from(cells).map(cell => {
                return cell.innerText.replace(/,/g, ';');
            });
            csv.push(rowData.join(','));
        });
        
        const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `returns_${new Date().toISOString().split('T')[0]}.csv`;
        a.click();
        URL.revokeObjectURL(url);
    });
</script>
@endpush