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
 
 
Route::get('', function () { 
 
    if (Auth::check()) { 
        $role = Auth::user()->role; 
        if ($role == 'admin') return redirect('/admin/dashboard'); 
        if ($role == 'petugas') return redirect('/petugas/dashboard'); 
        return redirect('/peminjam/dashboard'); 
    } 
 
    return view('/auth/login'); 
 
})->name('home'); 
 
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login'); 
Route::post('/login', [AuthController::class, 'login']); 
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register'); 
Route::post('/register', [AuthController::class, 'register']); 
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback'); 

Route::middleware(['auth', 'role:admin'])->group(function () { 
    Route::get('/admin/dashboard', [AdminController::class, 'index']); 
    Route::resource('users', UserController::class);  
    Route::resource('tools', ToolController::class); 
    Route::resource('categories', CategoryController::class); 
    Route::resource('admin/loans', AdminLoanController::class)->names('admin.loans'); 
    Route::resource('admin/returns', AdminReturnController::class)->names('admin.returns'); 
    Route::resource('admin/fines', FineController::class)->names('admin.fines'); 
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
 
 
Route::middleware(['auth', 'role:petugas'])->group(function () { 
    Route::get('/petugas/dashboard', [PetugasController::class, 'index']); 
    Route::post('/petugas/approve/{id}', [PetugasController::class, 'approve']); // Menyetujui 
    Route::post('/petugas/reject/{id}', [PetugasController::class, 'reject']); // Menolak 
    Route::post('/petugas/return/{id}', [PetugasController::class, 'processReturn']); // Pengembalian 
    Route::get('/petugas/laporan', [PetugasController::class, 'report']); // Cetak Laporan 
    Route::resource('petugas/fines', FineController::class)->names('petugas.fines'); 
    Route::get('petugas/fines/{id}/pay', [FineController::class, 'payForm'])->name('petugas.fines.pay'); 
    Route::post('petugas/fines/{id}/pay', [FineController::class, 'pay'])->name('petugas.fines.process');
}); 
 
Route::middleware(['auth', 'role:peminjam'])->group(function () { 
    Route::get('/peminjam/dashboard', [PeminjamController::class, 'index'])->name('peminjam.dashboard'); // Daftar Alat 
    Route::post('/peminjam/ajukan', [PeminjamController::class, 'store'])->name('peminjam.store'); // Mengajukan 
    Route::get('/peminjam/riwayat', [PeminjamController::class, 'history'])->name('peminjam.riwayat'); // Riwayat & Kembalikan 
    Route::get('/peminjam/denda', [PeminjamController::class, 'fines'])->name('peminjam.denda'); // Denda Saya
});