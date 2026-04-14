@extends('layouts.app')

@section('content')
<div class="dashboard-container">

    <div class="welcome-header">
        <h3>
            <i class="fas fa-money-bill-wave me-2" style="color: #3b82f6;"></i>
            Manajemen Denda
        </h3>
        <p>Kelola denda dan pembayaran keterlambatan pengembalian alat.</p>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h4>{{ $fines->where('status', 'pending')->count() }}</h4>
                    <p>Denda Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h4>{{ $fines->where('status', 'paid')->count() }}</h4>
                    <p>Denda Dibayar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h4>Rp {{ number_format($fines->where('status', 'pending')->sum('amount'), 0, ',', '.') }}</h4>
                    <p>Total Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-content">
                    <h4>Rp {{ number_format($fines->where('status', 'paid')->sum('amount'), 0, ',', '.') }}</h4>
                    <p>Total Dibayar</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4>Daftar Denda</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Peminjam</th>
                                <th>Denda</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fines as $fine)
                            <tr>
                                <td>{{ $fine->id }}</td>
                                <td>{{ $fine->loan->user->name ?? '-' }}</td>
                                <td>Rp {{ number_format($fine->amount, 0, ',', '.') }}</td>
                                <td>
                                    @if($fine->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-success">Success</span>
                                    @endif
                                </td>
                                <td>
                                    @if($fine->status == 'pending')
                                        <button class="btn btn-success btn-sm" onclick="bayar({{ $fine->id }})">
                                            <i class="fas fa-credit-card"></i> BAYAR
                                        </button>
                                    @else
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            <i class="fas fa-check"></i> Lunas
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data denda</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if(method_exists($fines, 'links'))
                    <div class="mt-3">
                        {{ $fines->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Load libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
console.log('✅ Page loaded');

let paymentCheckInterval = null;
let currentOrderId = null;

function bayar(fineId) {
    console.log('🔵 Bayar dipanggil untuk fineId:', fineId);
    
    Swal.fire({
        title: 'Memproses...',
        text: 'Menyiapkan pembayaran',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    fetch('{{ route("fines.payment.create") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ fine_id: fineId })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response dari server:', data);
        Swal.close();
        
        if (data.snap_token) {
            currentOrderId = data.order_id;
            
            window.snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    console.log('✅ Pembayaran Sukses:', result);
                    // Langsung update status setelah sukses
                    updateFineStatus(fineId, result);
                },
                onError: function(result) {
                    console.log('❌ Pembayaran Error:', result);
                    Swal.fire('Gagal!', 'Pembayaran gagal. Silakan coba lagi.', 'error');
                },
                onPending: function(result) {
                    console.log('⏳ Pembayaran Pending:', result);
                    // Untuk VA, status pending, cek secara berkala
                    startPollingStatus(fineId, data.order_id);
                },
                onClose: function() {
                    console.log('Popup ditutup');
                    Swal.fire('Info', 'Pembayaran dibatalkan', 'info');
                }
            });
        } else {
            throw new Error(data.message || 'Snap token tidak ditemukan');
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
        Swal.close();
        Swal.fire('Error!', error.message, 'error');
    });
}

// Fungsi update status setelah pembayaran sukses
function updateFineStatus(fineId, paymentResult) {
    Swal.fire({
        title: 'Memperbarui Status...',
        text: 'Mohon tunggu',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    fetch(`/fines/${fineId}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            transaction_id: paymentResult.transaction_id,
            order_id: paymentResult.order_id,
            payment_type: paymentResult.payment_type,
            gross_amount: paymentResult.gross_amount,
            payment_method: paymentResult.payment_type || 'midtrans',
            status: 'paid'
        })
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        
        if (data.success) {
            Swal.fire('✅ Berhasil!', 'Status denda telah diperbarui menjadi Dibayar.', 'success')
                .then(() => location.reload());
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.close();
        Swal.fire('⚠️ Peringatan', 'Gagal update status, silakan refresh manual.', 'warning')
            .then(() => location.reload());
    });
}

// Polling status untuk pembayaran Virtual Account
function startPollingStatus(fineId, orderId) {
    let attempts = 0;
    const maxAttempts = 20; // 20 x 3 detik = 60 detik
    
    Swal.fire({
        title: 'Menunggu Pembayaran',
        html: 'Silakan selesaikan pembayaran Anda.<br>Status akan otomatis diperbarui.',
        icon: 'info',
        confirmButtonText: 'Cek Sekarang',
        showCancelButton: true,
        cancelButtonText: 'Tutup',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Manual check
            checkStatusViaMidtrans(fineId, orderId);
        }
    });
    
    // Auto polling setiap 3 detik
    if (paymentCheckInterval) clearInterval(paymentCheckInterval);
    
    paymentCheckInterval = setInterval(() => {
        attempts++;
        console.log(`🔄 Polling ke-${attempts} untuk order: ${orderId}`);
        
        // Cek status ke database dulu
        fetch(`/fines/${fineId}/check-status`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'paid') {
                clearInterval(paymentCheckInterval);
                Swal.close();
                Swal.fire('✅ Sukses!', 'Pembayaran telah dikonfirmasi!', 'success')
                    .then(() => location.reload());
            } else if (attempts >= maxAttempts) {
                clearInterval(paymentCheckInterval);
                Swal.fire('Info', 'Silakan refresh halaman untuk cek status terbaru.', 'info');
            }
        })
        .catch(err => console.error('Polling error:', err));
    }, 3000);
}

// Cek status langsung ke Midtrans API
function checkStatusViaMidtrans(fineId, orderId) {
    Swal.fire({
        title: 'Mengecek Status...',
        text: 'Menghubungi server pembayaran',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    fetch('/fines/check-midtrans-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            fine_id: fineId,
            order_id: orderId
        })
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        
        if (data.success) {
            Swal.fire('✅ Berhasil!', 'Pembayaran terkonfirmasi!', 'success')
                .then(() => location.reload());
        } else {
            Swal.fire('⏳ Pending', data.message || 'Pembayaran belum terkonfirmasi', 'info');
        }
    })
    .catch(error => {
        Swal.close();
        Swal.fire('Error', 'Gagal mengecek status', 'error');
    });
}

// Cleanup
window.addEventListener('beforeunload', () => {
    if (paymentCheckInterval) clearInterval(paymentCheckInterval);
});
</script>

<style>
.btn-success {
    background-color: #10b981;
    border-color: #10b981;
    transition: all 0.3s ease;
}
.btn-success:hover {
    background-color: #059669;
    border-color: #059669;
    transform: translateY(-1px);
}
.badge {
    padding: 0.5rem 1rem;
    font-size: 0.75rem;
    font-weight: 500;
}
.table-responsive {
    overflow-x: auto;
}
.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}
</style>
@endsection