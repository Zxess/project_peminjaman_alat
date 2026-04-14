<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminLoanController;
use App\Http\Controllers\AdminReturnController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PeminjamController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FineController;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PaymentController;

Route::get('', function () { 
    if (Auth::check()) { 
        $role = Auth::user()->role; 
        if ($role == 'admin') return redirect('/admin/dashboard'); 
        if ($role == 'petugas') return redirect('/petugas/dashboard'); 
        return redirect('/peminjam/dashboard'); 
    } 
    return view('/auth/login'); 
})->name('home'); 

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login'); 
Route::post('/login', [AuthController::class, 'login']); 
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register'); 
Route::post('/register', [AuthController::class, 'register']); 
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback'); 

// ============ ROUTES ADMIN ============
Route::middleware(['auth', 'role:admin'])->group(function () { 
    Route::get('/admin/dashboard', [AdminController::class, 'index']); 
    Route::resource('users', UserController::class);  
    Route::resource('tools', ToolController::class); 
    Route::resource('categories', CategoryController::class); 
    Route::resource('admin/loans', AdminLoanController::class)->names('admin.loans'); 
    Route::resource('admin/returns', AdminReturnController::class)->names('admin.returns'); 
    Route::resource('admin/fines', FineController::class)->names('admin.fines'); 
    
    // Route payment fines untuk admin
    Route::get('admin/fines/{id}/pay', [FineController::class, 'payForm'])->name('admin.fines.pay'); 
    Route::post('admin/fines/{id}/pay', [FineController::class, 'pay'])->name('admin.fines.process'); 
    
    Route::get('/admin/logs', function() { 
        $logs = ActivityLog::with('user')->whereHas('user', function($q) { 
            $q->where('role', 'admin'); 
        })->latest()->paginate(20); 

        $stats = [ 
            'login' => ActivityLog::whereHas('user', fn($q) => $q->where('role', 'admin'))->where('action', 'Login')->count(), 
            'create' => ActivityLog::whereHas('user', fn($q) => $q->where('role', 'admin'))->whereIn('action', ['Create Loan', 'Tambah Kategori', 'Tambah Alat'])->count(), 
            'update' => ActivityLog::whereHas('user', fn($q) => $q->where('role', 'admin'))->whereIn('action', ['Update Kategori', 'Update Alat'])->count(), 
            'delete' => ActivityLog::whereHas('user', fn($q) => $q->where('role', 'admin'))->where('action', 'Hapus Kategori')->count(), 
        ]; 

        return view('admin.logs', compact('logs', 'stats')); 
    }); 
}); 

// ============ ROUTES PETUGAS ============
Route::middleware(['auth', 'role:petugas'])->group(function () { 
    Route::get('/petugas/dashboard', [PetugasController::class, 'index'])->name('petugas.dashboard'); 
    Route::post('/petugas/approve/{id}', [PetugasController::class, 'approve'])->name('petugas.approve');
    Route::post('/petugas/reject/{id}', [PetugasController::class, 'reject'])->name('petugas.reject');
    Route::post('/petugas/return/{id}', [PetugasController::class, 'processReturn'])->name('petugas.processReturn');
    Route::get('/petugas/laporan', [PetugasController::class, 'report'])->name('petugas.report');
    Route::resource('petugas/fines', FineController::class)->names('petugas.fines'); 
    Route::get('petugas/fines/{id}/pay', [FineController::class, 'payForm'])->name('petugas.fines.pay'); 
    Route::post('petugas/fines/{id}/pay', [FineController::class, 'pay'])->name('petugas.fines.process');
}); 

// ============ ROUTES PEMINJAM ============
Route::middleware(['auth', 'role:peminjam'])->group(function () { 
    Route::get('/peminjam/dashboard', [PeminjamController::class, 'index'])->name('peminjam.dashboard');
    Route::post('/peminjam/ajukan', [PeminjamController::class, 'store'])->name('peminjam.store');
    Route::get('/peminjam/riwayat', [PeminjamController::class, 'history'])->name('peminjam.riwayat');
    Route::post('/peminjam/return/{id}', [PeminjamController::class, 'requestReturn'])->name('peminjam.return');
    Route::get('/peminjam/denda', [PeminjamController::class, 'fines'])->name('peminjam.denda');
});

// ============ ROUTES MIDTRANS (AJAX & Webhook) ============
// Route untuk pembayaran via AJAX (untuk admin/petugas)
Route::middleware(['auth'])->group(function () {
    Route::post('/fines/payment/create', [FineController::class, 'createPayment'])->name('fines.payment.create');
    Route::post('/fines/payment/confirm', [FineController::class, 'confirmPayment'])->name('fines.payment.confirm');
});

// Webhook Midtrans (tanpa middleware auth - dipanggil oleh Midtrans)
Route::post('/midtrans/notification', [FineController::class, 'handleNotification'])->name('midtrans.notification');

Route::get('/test-midtrans', function() {
    return [
        'server_key' => config('midtrans.server_key'),
        'client_key' => config('midtrans.client_key'),
        'server_key_length' => strlen(config('midtrans.server_key') ?? ''),
        'client_key_length' => strlen(config('midtrans.client_key') ?? ''),
        'is_production' => config('midtrans.is_production'),
        'env_file_exists' => file_exists(base_path('.env')),
    ];
});

Route::post('/fines/update-status/{fineId}', [FineController::class, 'updateStatus'])
    ->name('fines.update.status')
    ->middleware('auth');

// Route untuk update status setelah pembayaran
Route::post('/fines/{fineId}/update-status', [FineController::class, 'updateStatusAfterPayment'])
    ->name('fines.update.status')
    ->middleware('auth');

// Route untuk cek status pembayaran
Route::get('/fines/{fineId}/check-status', [FineController::class, 'checkPaymentStatus'])
    ->name('fines.check.status')
    ->middleware('auth');
    Route::middleware(['auth'])->group(function () {
    Route::post('/fines/{fineId}/update-status', [FineController::class, 'updateStatusAfterPayment']);
    Route::get('/fines/{fineId}/check-status', [FineController::class, 'checkPaymentStatus']);
});