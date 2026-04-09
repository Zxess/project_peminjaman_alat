@extends('layouts.app') 

@section('content') 
<div class="d-flex justify-content-between align-items-center mb-4"> 
    <h3 class="fw-bold">📦 Data Pengembalian Alat</h3> 
    <a href="{{ route('admin.returns.create') }}" class="btn btn-success shadow-sm"> 
        + Proses Pengembalian 
    </a> 
</div> 

<div class="card shadow-sm border-0"> 
    <div class="card-body"> 

        <div class="table-responsive">
            <table class="table table-hover align-middle"> 
                <thead class="table-primary text-center"> 
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

                        <td>{{ $r->user->name ?? '-' }}</td> 
                        <td>{{ $r->tool->nama_alat ?? '-' }}</td> 

                        <td>
                            {{ \Carbon\Carbon::parse($r->tanggal_pinjam)->format('d M Y') }}
                        </td> 

                        <td>
                            {{ \Carbon\Carbon::parse($r->tanggal_kembali_aktual)->format('d M Y') }} 

                            @if($r->tanggal_kembali_aktual > $r->tanggal_kembali_rencana) 
                                <span class="badge bg-danger ms-1">Telat</span> 
                            @else 
                                <span class="badge bg-success ms-1">Tepat</span> 
                            @endif 
                        </td> 

                        <td class="text-center">
                            {{ $r->petugas->name ?? 'Admin' }}
                        </td> 

                        <td class="text-center"> 
                            <a href="{{ route('admin.returns.edit', $r->id) }}" class="btn btn-warning btn-sm me-1">
                                ✏️
                            </a> 
                             
                            <form action="{{ route('admin.returns.destroy', $r->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus riwayat ini?');"> 
                                @csrf 
                                @method('DELETE') 
                                <button class="btn btn-danger btn-sm">🗑️</button> 
                            </form> 
                        </td> 
                    </tr> 

                    @empty 
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            📭 Belum ada data pengembalian
                        </td>
                    </tr> 
                    @endforelse 
                </tbody> 
            </table> 
        </div>

        <div class="mt-3 d-flex justify-content-center">
            {{ $returns->links('pagination::bootstrap-5') }}
        </div> 

    </div> 
</div> 
@endsection 