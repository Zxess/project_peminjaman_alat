@extends('layouts.app') 

@section('content') 
<div class="card col-md-8 mx-auto shadow-sm border-0"> 
    
    <div class="card-header bg-primary text-white fw-semibold">
        ✏️ Edit Peminjaman #{{ $loan->id }}
    </div> 

    <div class="card-body"> 
        <form action="{{ route('admin.loans.update', $loan->id) }}" method="POST"> 
            @csrf 
            @method('PUT') 
             
            <!-- Peminjam -->
            <div class="mb-3"> 
                <label class="form-label fw-semibold">Peminjam</label> 
                <select name="user_id" class="form-select"> 
                    @foreach($users as $user) 
                        <option value="{{ $user->id }}" 
                            {{ old('user_id', $loan->user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} 
                        </option> 
                    @endforeach 
                </select> 
            </div> 

            <!-- Alat -->
            <div class="mb-3"> 
                <label class="form-label fw-semibold">Alat</label> 
                <select name="tool_id" class="form-select"> 
                    @foreach($tools as $tool) 
                        <option value="{{ $tool->id }}" 
                            {{ old('tool_id', $loan->tool_id) == $tool->id ? 'selected' : '' }}>
                            {{ $tool->nama_alat }} 
                        </option> 
                    @endforeach 
                </select> 
            </div> 

            <!-- Tanggal -->
            <div class="row mb-3"> 
                <div class="col-md-6"> 
                    <label class="form-label fw-semibold">Tgl Pinjam</label>  
                    <input type="date" name="tanggal_pinjam" class="form-control" 
                        value="{{ old('tanggal_pinjam', $loan->tanggal_pinjam) }}"> 
                </div> 

                <div class="col-md-6"> 
                    <label class="form-label fw-semibold">Rencana Kembali</label> 
                    <input type="date" name="tanggal_kembali_rencana" class="form-control" 
                        value="{{ old('tanggal_kembali_rencana', $loan->tanggal_kembali_rencana) }}"> 
                </div> 
            </div> 

            {{-- <!-- Status -->
            <div class="mb-3"> 
                <label class="form-label fw-semibold">Status</label> 
                <select name="status" class="form-select"> 
                    <option value="pending" {{ old('status', $loan->status) == 'pending' ? 'selected' : '' }}>Pending</option> 
                    <option value="disetujui" {{ old('status', $loan->status) == 'disetujui' ? 'selected' : '' }}>Disetujui</option> 
                    <option value="kembali" {{ old('status', $loan->status) == 'kembali' ? 'selected' : '' }}>Kembali</option> 
                    <option value="ditolak" {{ old('status', $loan->status) == 'ditolak' ? 'selected' : '' }}>Ditolak</option> 
                </select>  --}}

                {{-- <small class="text-danger">
                    *Jika status diubah ke <b>Kembali</b>, stok alat akan otomatis bertambah.
                </small>  --}}
            </div> 

            <!-- Tombol -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary">← Batal</a> 
                <button class="btn btn-success px-4">Update</button> 
            </div> 

        </form> 
    </div> 
</div> 
@endsection 