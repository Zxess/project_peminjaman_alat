@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Pembayaran Denda</h4>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>ID Denda</th>
                            <td>#{{ $fine->id }}</td>
                        </tr>
                        <tr>
                            <th>Peminjam</th>
                            <td>{{ $fine->loan->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Denda</th>
                            <td class="text-danger fw-bold">Rp {{ number_format($fine->amount, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                    
                    <button id="pay-button" class="btn btn-success w-100">
                        <i class="fas fa-credit-card"></i> Bayar Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" 
        data-client-key="{{ config('midtrans.client_key') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const fineId = {{ $fine->id }};
const payButton = document.getElementById('pay-button');

payButton.addEventListener('click', function() {
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
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ fine_id: fineId })
    })
    .then(res => res.json())
    .then(data => {
        Swal.close();
        
        if (data.success && data.snap_token) {
            window.snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    confirmPayment('success', result);
                },
                onPending: function(result) {
                    confirmPayment('pending', result);
                },
                onError: function(result) {
                    confirmPayment('error', result);
                }
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        Swal.close();
        Swal.fire('Error', 'Terjadi kesalahan: ' + error.message, 'error');
    });
});

function confirmPayment(status, result) {
    fetch('{{ route("fines.payment.confirm") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            fine_id: fineId,
            transaction_status: status
        })
    })
    .then(res => res.json())
    .then(() => {
        if (status === 'success') {
            Swal.fire('Berhasil!', 'Pembayaran berhasil', 'success')
                .then(() => window.location.href = '{{ route("admin.fines.index") }}');
        } else {
            Swal.fire('Info', 'Status: ' + status, 'info')
                .then(() => window.location.href = '{{ route("admin.fines.index") }}');
        }
    });
}
</script>
@endsection