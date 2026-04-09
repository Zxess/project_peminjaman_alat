@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    {{-- Welcome Header --}}
    <div class="welcome-header">
        <h3>
            <i class="fas fa-toolbox me-2" style="color: #3b82f6;"></i>
            Tambah Alat Baru
        </h3>
        <p>Tambah alat baru ke dalam inventaris.</p>
    </div>

    {{-- Form Card --}}
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="activity-card">
                <div class="activity-header">
                    <i class="fas fa-edit"></i>
                    <h5>Form Tambah Alat</h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('tools.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Nama Alat</label>
                            <input type="text" name="nama_alat" class="form-control @error('nama_alat') is-invalid @enderror" value="{{ old('nama_alat') }}" required>
                            @error('nama_alat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Jumlah Stok</label>
                                <input type="number" name="stok" class="form-control @error('stok') is-invalid @enderror" value="{{ old('stok', 1) }}" min="0" required>
                                @error('stok')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Gambar Alat (Opsional)</label>
                                <input type="file" name="gambar" class="form-control @error('gambar') is-invalid @enderror" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG. Maks: 2MB</small>
                                @error('gambar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Deskripsi / Spesifikasi</label>
                            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tools.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Simpan Alat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 