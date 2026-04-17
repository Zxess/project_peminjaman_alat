# Dokumentasi Debugging - Sistem Peminjaman Alat

**Tanggal:** 17 April 2026  
**Aplikasi:** Tool Borrowing System (Laravel 12)  
**Database:** MySQL 5.7+  
**Environment:** Local Development (XAMPP)

---

## 📋 Daftar Isi

1. [5 Skenario Debugging Praktis](#5-skenario-debugging-praktis)
2. [Setup Debugging](#setup-debugging)
3. [Common Issues & Solutions](#common-issues--solutions)
4. [Logging Strategies](#logging-strategies)
5. [Database Debugging](#database-debugging)
6. [Frontend Debugging](#frontend-debugging)
7. [API/Payment Gateway Debugging](#apipayment-gateway-debugging)
8. [Performance Debugging](#performance-debugging)
9. [Tools & Commands](#tools--commands)

---

## 🎯 5 Skenario Debugging Praktis

### Scenario 1: User Tidak Bisa Login (Authentication Error)

**Gejala:** User login dengan email & password yang benar, tapi masih redirect ke login page

**Debugging Steps:**

```bash
# 1. Cek database user ada
php artisan tinker
> App\Models\User::where('email', 'siswa1@sekolah.com')->first()

# 2. Cek passwords di database
> $user = App\Models\User::find(2)
> \Illuminate\Support\Facades\Hash::check('password123', $user->password)

# 3. Cek session config
> config('session.driver')
> config('session.lifetime')
```

**Kode Debug di Controller:**

```php
// app/Http/Controllers/AuthController.php
public function login(Request $request)
{
    Log::info('Login attempt', [
        'email' => $request->email,
        'timestamp' => now()
    ]);
    
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);
    
    if (Auth::attempt($credentials)) {
        Log::info('Login successful', ['user_id' => Auth::id()]);
        return redirect()->intended(route('dashboard'));
    }
    
    // Debug: Log why authentication failed
    $user = User::where('email', $request->email)->first();
    Log::warning('Login failed', [
        'email' => $request->email,
        'user_exists' => !!$user,
        'password_match' => $user ? Hash::check($request->password, $user->password) : false
    ]);
    
    return back()->withErrors(['email' => 'Email atau password salah']);
}
```

**Solusi Cepat:**
```bash
# Clear session cache
php artisan session:table
php artisan migrate

# Reset session
php artisan cache:clear
php artisan session:cleanup

# Test login ulang
```

---

### Scenario 2: Alat Tidak Muncul di Halaman (Query Returns Empty)

**Gejala:** Halaman dashboard kosong, tidak ada alat yang ditampilkan padahal database punya data

**Debugging Steps:**

```bash
# 1. Check database content
php artisan tinker
> App\Models\Tool::count()    # Should > 0
> App\Models\Tool::all()

# 2. Check category exists
> App\Models\Category::count()
> App\Models\Tool::with('category')->get()

# 3. Check stock > 0
> App\Models\Tool::where('stock', '>', 0)->count()
```

**Kode Debug di Controller:**

```php
// app/Http/Controllers/DashboardController.php
public function index()
{
    Log::debug('Dashboard access', ['user_id' => auth()->id()]);
    
    $tools = Tool::with(['category', 'loans'])
                  ->where('stock', '>', 0)
                  ->get();
    
    Log::debug('Tools fetched', [
        'count' => $tools->count(),
        'query' => Tool::where('stock', '>', 0)->toSql(),
        'total_in_db' => Tool::count()
    ]);
    
    if ($tools->isEmpty()) {
        Log::warning('No tools available to display');
    }
    
    return view('dashboard', compact('tools'));
}
```

**Blade Debug:**

```blade
<!-- resources/views/dashboard.blade.php -->
@if($tools->isEmpty())
    <div class="alert alert-warning">
        <!-- Debug info -->
        <p>Tidak ada alat tersedia</p>
        @if(auth()->user()->isAdmin())
            <p class="text-muted">
                Total tools in DB: {{ App\Models\Tool::count() }}<br>
                Tools with stock: {{ App\Models\Tool::where('stock', '>', 0)->count() }}
            </p>
        @endif
    </div>
@else
    <!-- Display tools -->
    @foreach($tools as $tool)
        <div class="card">{{ $tool->name }}</div>
    @endforeach
@endif
```

---

### Scenario 3: Denda Tidak Tersimpan (Create Fails Silently)

**Gejala:** Form submit berhasil tapi data denda tidak ada di database

**Debugging Steps:**

```bash
# 1. Check database
php artisan tinker
> App\Models\Fine::count()
> App\Models\Fine::latest()->first()

# 2. Check validation errors
# Jalankan form dengan data invalid, lihat apakah error muncul

# 3. Check transactions
> DB::table('fines')->get()
```

**Kode Debug di Controller:**

```php
// app/Http/Controllers/FineController.php
public function store(Request $request)
{
    Log::info('Creating fine', $request->all());
    
    $validated = $request->validate([
        'loan_id' => 'required|exists:loans,id',
        'amount' => 'required|numeric|min:1000',
        'reason' => 'required'
    ]);
    
    Log::debug('Validation passed', $validated);
    
    try {
        $fine = Fine::create($validated);
        
        Log::info('Fine created successfully', [
            'fine_id' => $fine->id,
            'amount' => $fine->amount,
            'loan_id' => $fine->loan_id
        ]);
        
        return response()->json(['success' => true, 'fine_id' => $fine->id]);
        
    } catch (\Exception $e) {
        Log::error('Fine creation failed', [
            'error' => $e->getMessage(),
            'data' => $validated,
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
```

**Solusi Cepat:**
```bash
# Cek database connection
php artisan migrate:status

# Seed test data
php artisan db:seed

# Test create manually
php artisan tinker
> App\Models\Fine::create(['loan_id' => 1, 'amount' => 50000, 'reason' => 'late'])
```

---

### Scenario 4: Notifikasi Email Tidak Terkirim (Mail Not Sending)

**Gejala:** User tidak menerima email notifikasi setelah return alat atau pembayaran

**Debugging Steps:**

```bash
# 1. Check mail config
php artisan tinker
> config('mail.mailer')
> config('mail.from')

# 2. Check queued mails
> DB::table('jobs')->where('queue', 'mails')->get()

# 3. Test send email
> Mail::raw('Test', function($m) { $m->to('test@example.com'); })
```

**Kode Debug di Mailable:**

```php
// app/Mail/LoanReturnNotification.php
namespace App\Mail;

use Illuminate\Mail\Mailable;

class LoanReturnNotification extends Mailable
{
    public function build()
    {
        Log::info('Building email', [
            'to' => $this->to,
            'subject' => 'Pengembalian Alat'
        ]);
        
        return $this->subject('Pengembalian Alat Berhasil')
                    ->view('emails.loan-return')
                    ->with([
                        'user_name' => $this->loan->user->name,
                        'tool_name' => $this->loan->tool->name
                    ]);
    }
}

// app/Http/Controllers/LoanController.php
public function markAsReturned($id)
{
    $loan = Loan::findOrFail($id);
    $loan->update(['status' => 'returned']);
    
    try {
        Mail::to($loan->user->email)->send(
            new LoanReturnNotification($loan)
        );
        Log::info('Return notification sent', ['user_id' => $loan->user_id]);
    } catch (\Exception $e) {
        Log::error('Failed to send return notification', [
            'error' => $e->getMessage(),
            'user_email' => $loan->user->email
        ]);
    }
    
    return back()->with('success', 'Alat berhasil dikembalikan');
}
```

**Solusi Cepat:**
```bash
# Test email setup
php artisan tinker
> Mail::raw('Hello World', function($m) { 
    $m->to('admin@example.com'); 
    $m->subject('Test'); 
  })

# Check logs untuk output
Get-Content storage/logs/laravel.log | Select-Object -Last 20

# Configure mail di .env
# MAIL_MAILER=log (untuk development, output ke logs)
```

---

### Scenario 5: Midtrans Payment Token Gagal (Payment Gateway Error)

**Gejala:** Button "Bayar Denda" tidak jalan, atau Snap token tidak generate

**Debugging Steps:**

```bash
# 1. Check Midtrans config
php artisan tinker
> config('midtrans')
> strlen(config('midtrans.server_key'))

# 2. Test API call
> Midtrans\Config::$serverKey = config('midtrans.server_key')
> Midtrans\Config::$isProduction = false
> Midtrans\Snap::getSnapToken(['transaction_details' => ['order_id' => 'TEST-1', 'gross_amount' => 50000]])

# 3. Check logs
> tail -f storage/logs/laravel.log | grep Midtrans
```

**Kode Debug di Controller:**

```php
// app/Http/Controllers/FineController.php
public function createPayment(Request $request)
{
    $fine = Fine::findOrFail($request->fine_id);
    
    Log::debug('Midtrans Payment Debug', [
        'fine_id' => $fine->id,
        'amount' => $fine->amount,
        'merchant_id' => config('midtrans.merchant_id'),
        'server_key_exists' => !empty(config('midtrans.server_key')),
        'is_production' => config('midtrans.is_production')
    ]);
    
    try {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
        
        $orderId = 'FINE-' . $fine->id . '-' . time();
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $fine->amount,
            ],
            'customer_details' => [
                'email' => $fine->loan->user->email,
                'first_name' => $fine->loan->user->name,
            ]
        ];
        
        Log::debug('Midtrans API request', [
            'params' => json_encode($params)
        ]);
        
        $snapToken = Snap::getSnapToken($params);
        
        Log::info('Snap token generated successfully', [
            'order_id' => $orderId,
            'token_length' => strlen($snapToken)
        ]);
        
        return response()->json([
            'success' => true,
            'snap_token' => $snapToken,
            'order_id' => $orderId
        ]);
        
    } catch (\Exception $e) {
        Log::error('Midtrans Payment Error', [
            'error_message' => $e->getMessage(),
            'error_code' => $e->getCode(),
            'fine_id' => $fine->id,
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => 'Gagal membuat payment token: ' . $e->getMessage()
        ], 500);
    }
}
```

**Frontend Debug:**

```javascript
// resources/js/payment.js
document.getElementById('paymentBtn').addEventListener('click', async function() {
    console.log('Payment button clicked');
    
    try {
        const response = await fetch('/api/payment/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ fine_id: this.dataset.fineId })
        });
        
        console.log('Response status:', response.status);
        const data = await response.json();
        
        if (!data.success) {
            console.error('API Error:', data.error);
            alert('Gagal: ' + data.error);
            return;
        }
        
        console.log('Snap token received:', data.snap_token?.substring(0, 20));
        
        // Initialize Snap
        snap.pay(data.snap_token, {
            onSuccess: (result) => {
                console.log('Payment success:', result);
                location.href = '/success';
            },
            onError: (error) => {
                console.error('Payment error:', error);
            },
            onPending: (result) => {
                console.log('Payment pending:', result);
            }
        });
        
    } catch (error) {
        console.error('Fetch error:', error);
    }
});
```

**Solusi Cepat:**
```bash
# Verify credentials di .env
cat .env | grep MIDTRANS

# Clear cache
php artisan config:clear

# Test sandbox mode
# At least test yang terintegrasi

# Check network tab di DevTools saat click payment button
```

---



## 🔧 Setup Debugging

### 1. Konfigurasi Environment untuk Development

**File `.env`:**
```bash
APP_DEBUG=true              # Enable debug mode
APP_ENV=local               # Set environment to local
LOG_LEVEL=debug             # Capture all log levels
LOG_CHANNEL=stack           # Use stack logging (file + single)
```

**Periksa status debug:**
```bash
php artisan tinker
# Di dalam tinker shell:
> config('app.debug')
> true
```

### 2. Mengaktifkan Query Debugging

**File `bootstrap/app.php`:**
```php
// Enable query logging untuk development
if (!app()->isProduction()) {
    DB::listen(function ($query) {
        Log::debug('SQL Query', [
            'query' => $query->sql,
            'bindings' => $query->bindings,
            'time' => $query->time . 'ms'
        ]);
    });
}
```

### 3. Laravel Debugbar (Optional)

Untuk debugging yang lebih visual, install Laravel Debugbar:
```bash
composer require barryvdh/laravel-debugbar --dev
php artisan vendor:publish --provider="Barryvdh\Debugbar\ServiceProvider"
```

---

## 🐛 Common Issues & Solutions

### Issue #1: Data Riwayat Tidak Sesuai (History Data Mismatch)

#### Gejala:
- Data history peminjaman menampilkan semua record, bukan hanya milik user
- Filter tidak berfungsi dengan baik
- NIS (Nomor Induk Siswa) tidak ter-filter

#### Root Cause:
Pada awal implementasi, query history tidak memfilter berdasarkan user yang login.

#### ❌ Kode Lama (SALAH):
```php
// app/Http/Controllers/LoanController.php
public function userHistory()
{
    // Mengambil SEMUA loans tanpa filter
    $loans = Loan::with(['tool', 'user'])
                  ->orderBy('created_at', 'desc')
                  ->paginate(10);
    
    return view('peminjam.history', compact('loans'));
}
```

#### ✅ Kode Baru (BENAR):
```php
public function userHistory()
{
    // Filter berdasarkan NIS (auth user)
    $loans = Loan::with(['tool', 'user'])
                  ->where('user_id', auth()->id())  // Filter by logged-in user
                  ->orderBy('created_at', 'desc')
                  ->paginate(10);
    
    return view('peminjam.history', compact('loans'));
}
```

#### Testing:
```bash
# Via Tinker
php artisan tinker
> auth()->loginUsingId(2)
> App\Models\Loan::where('user_id', auth()->id())->count()
# Verifikasi hasilnya sesuai NIS user

# Via Test
php artisan test tests/Feature/LoanHistoryTest.php
```

#### Debug Checklist:
- [ ] User login dengan NIS yang benar
- [ ] `auth()->id()` mengembalikan ID user yang tepat
- [ ] Query memfilter `where('user_id', auth()->id())`
- [ ] Pagination menampilkan data user saja

---

### Issue #2: Carousel Tidak Bisa Slide (Carousel Not Sliding)

#### Gejala:
- Tombol Next/Prev tidak berfungsi
- Slide tidak bergerak otomatis
- JavaScript error di console

#### Root Cause:
Loop Blade `foreach` tidak menghasilkan struktur HTML yang benar untuk Carousel Bootstrap.

#### ❌ Kode Lama (SALAH):
```blade
<!-- resources/views/peminjam/dashboard.blade.php -->
<div class="carousel slide" id="toolCarousel" data-bs-ride="carousel">
    <div class="carousel-inner">
        @foreach($tools as $index => $tool)
            <!-- MASALAH: Semua items memiliki class `active` atau tidak ada -->
            <div class="carousel-item active">
                <img src="{{ Storage::url($tool->image) }}" class="d-block w-100">
                <h5>{{ $tool->name }}</h5>
            </div>
        @endforeach
    </div>
    <!-- MASALAH: Tombol tidak di-generate dengan benar -->
    <button class="carousel-control-prev" type="button" data-bs-target="#toolCarousel">
        <span class="carousel-control-prev-icon"></span>
    </button>
</div>
```

#### ✅ Kode Baru (BENAR):
```blade
<!-- resources/views/peminjam/dashboard.blade.php -->
<div class="carousel slide" id="toolCarousel" data-bs-ride="carousel">
    <div class="carousel-inner">
        @foreach($tools as $index => $tool)
            <!-- Hanya item pertama yang active -->
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <img src="{{ Storage::url($tool->image) }}" class="d-block w-100" alt="{{ $tool->name }}">
                <div class="carousel-caption">
                    <h5 class="text-dark">{{ $tool->name }}</h5>
                    <p class="text-muted">Stok: {{ $tool->stock }}</p>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Indicators -->
    <div class="carousel-indicators">
        @foreach($tools as $index => $tool)
            <button type="button" data-bs-target="#toolCarousel" data-bs-slide-to="{{ $index }}"
                    class="{{ $index === 0 ? 'active' : '' }}"></button>
        @endforeach
    </div>
    
    <!-- Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#toolCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#toolCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>
```

#### Debugging di Browser:
```javascript
// Buka Console (F12) dan jalankan:

// 1. Cek apakah Bootstrap Carousel ter-initialize
const carousel = new bootstrap.Carousel(document.getElementById('toolCarousel'));
console.log(carousel);

// 2. Cek struktur HTML
const items = document.querySelectorAll('.carousel-item');
console.log('Total items:', items.length);
console.log('Active items:', document.querySelectorAll('.carousel-item.active').length);

// 3. Manual test slide
carousel.next();  // ke slide berikutnya
carousel.prev();  // ke slide sebelumnya

// 4. Cek event listeners
carousel._config  // lihat konfigurasi carousel
```

#### Debug Checklist:
- [ ] Hanya 1 item yang memiliki class `active`
- [ ] Semua items ada dalam `.carousel-inner`
- [ ] Controls (prev/next) memiliki atribut `data-bs-target` dan `data-bs-slide`
- [ ] JavaScript Bootstrap v5+ ter-load dengan baik
- [ ] Tidak ada error di Console

#### Blade Debugging Tips:
```blade
<!-- Tampilkan struktur HTML untuk debugging -->
<div class="carousel-inner">
    @foreach($tools as $index => $tool)
        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
            <!-- Debug: Tampilkan index dan status -->
            <span style="position: absolute; top:10px; left:10px; background: red; color:white;">
                Item {{ $index + 1 }} ({{ $index === 0 ? 'ACTIVE' : 'inactive' }})
            </span>
        </div>
    @endforeach
</div>
```

---

### Issue #3: Modal Tidak Bekerja (Modal Not Working)

#### Gejala:
- Tombol trigger modal tidak merespons
- Modal muncul tapi tidak bisa ditutup
- Multiple modals bentrok (hanya satu yang berfungsi)
- Konten modal kosong atau salah

#### Root Cause:
ID modal tidak unik ketika menggunakan loop, atau atribut `data-bs-target` tidak sesuai.

#### ❌ Kode Lama (SALAH):
```blade
<!-- resources/views/admin/tools/index.blade.php -->
@foreach($tools as $tool)
    <tr>
        <td>{{ $tool->name }}</td>
        <td>
            <!-- MASALAH: Semua button memiliki id yang sama -->
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">
                Edit
            </button>
        </td>
    </tr>
    
    <!-- MASALAH: Modal ID tidak unik, konten salah -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Alat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Konten tidak ter-update per item -->
                    <form action="#" method="POST">
                        <input type="hidden" name="tool_id" value="">
                        <input type="text" name="name" placeholder="Nama Alat">
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
```

#### ✅ Kode Baru (BENAR):
```blade
<!-- resources/views/admin/tools/index.blade.php -->
@foreach($tools as $tool)
    <tr>
        <td>{{ $tool->name }}</td>
        <td>
            <!-- PERBAIKAN: ID modal unik per item -->
            <button class="btn btn-sm btn-primary" 
                    data-bs-toggle="modal" 
                    data-bs-target="#editModal-{{ $tool->id }}">
                Edit
            </button>
        </td>
    </tr>
@endforeach

<!-- Modal di luar loop untuk performa lebih baik -->
@foreach($tools as $tool)
    <div class="modal fade" id="editModal-{{ $tool->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Alat: {{ $tool->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.tools.update', $tool->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tool_id" value="{{ $tool->id }}">
                        <div class="mb-3">
                            <label class="form-label">Nama Alat</label>
                            <input type="text" name="name" class="form-control" 
                                   value="{{ $tool->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-control" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ $tool->category_id === $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
```

#### Alternative: AJAX Modal (Recommended)
```blade
<!-- Button -->
<button class="btn btn-sm btn-primary edit-tool-btn" data-tool-id="{{ $tool->id }}">
    Edit
</button>

<!-- Generic Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" id="modalContent">
            <!-- Content akan di-load via AJAX -->
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
document.querySelectorAll('.edit-tool-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const toolId = this.dataset.toolId;
        
        // Fetch modal content via AJAX
        const response = await fetch(`/admin/tools/${toolId}/edit`);
        const html = await response.text();
        
        document.getElementById('modalContent').innerHTML = html;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();
    });
});
</script>
```

#### Debugging di Browser:
```javascript
// Buka Console (F12)

// 1. Cek apakah modal ada di DOM
const modal = document.getElementById('editModal-1');
console.log('Modal exists:', !!modal);

// 2. Cek struktur modal
console.log(modal.innerHTML);

// 3. Manual trigger modal
const bsModal = new bootstrap.Modal(modal);
bsModal.show();   // Buka modal
bsModal.hide();   // Tutup modal

// 4. Cek button trigger
const btn = document.querySelector('[data-bs-target="#editModal-1"]');
console.log('Button found:', !!btn);
console.log('Button target:', btn?.dataset.bsTarget);

// 5. Monitor modal events
modal.addEventListener('show.bs.modal', () => console.log('Modal showing'));
modal.addEventListener('shown.bs.modal', () => console.log('Modal shown'));
modal.addEventListener('hide.bs.modal', () => console.log('Modal hiding'));
modal.addEventListener('hidden.bs.modal', () => console.log('Modal hidden'));
```

#### Debug Checklist:
- [ ] Setiap modal memiliki ID unik (misal: `editModal-{{ $tool->id }}`)
- [ ] Button trigger memiliki `data-bs-target="#editModal-UNIQUE_ID"`
- [ ] Modal memiliki `tabindex="-1"`
- [ ] Tombol close memiliki `data-bs-dismiss="modal"`
- [ ] Tidak ada duplikat ID di halaman
- [ ] Modal di-load sebelum JavaScript Bootstrap
- [ ] Tidak ada JavaScript error di Console

#### Validasi HTML:
```html
<!-- Gunakan validasi online: https://validator.w3.org/ -->
<!-- Cek untuk ID duplikat, struktur form benar, dll -->
```

---

## 📝 Logging Strategies

### 1. Menggunakan Log Facade

```php
// app/Http/Controllers/LoanController.php

use Illuminate\Support\Facades\Log;

public function store(Request $request)
{
    Log::info('User attempting to borrow tool', [
        'user_id' => auth()->id(),
        'tool_id' => $request->tool_id,
        'timestamp' => now()
    ]);
    
    try {
        $loan = Loan::create($request->validated());
        
        Log::info('Loan created successfully', [
            'loan_id' => $loan->id,
            'user_id' => $loan->user_id
        ]);
        
        return redirect()->route('peminjam.loans')->with('success', 'Berhasil meminjam alat');
        
    } catch (\Exception $e) {
        Log::error('Failed to create loan', [
            'error' => $e->getMessage(),
            'user_id' => auth()->id(),
            'stack_trace' => $e->getTraceAsString()
        ]);
        
        return back()->with('error', 'Gagal meminjam alat');
    }
}
```

### 2. Membaca Logs

```bash
# Tail logs real-time (Linux/Mac)
tail -f storage/logs/laravel.log

# Tail logs Windows PowerShell
Get-Content storage/logs/laravel.log -Wait

# Cari specific error
grep "error" storage/logs/laravel.log

# Filter by date
grep "2026-04-17" storage/logs/laravel.log

# Lihat logs terbaru
Get-Content storage/logs/laravel.log | Select-Object -Last 100
```

### 3. Query Logging

```php
// Enable dalam config atau runtime
DB::listen(function ($query) {
    if ($query->time > 1000) { // Log slow queries > 1 second
        Log::warning('Slow Query', [
            'sql' => $query->sql,
            'bindings' => $query->bindings,
            'time' => $query->time . 'ms'
        ]);
    }
});
```

---

## 🗄️ Database Debugging

### 1. Check Database Connection

```bash
php artisan tinker
> DB::connection()->getPdo()
> DB::select('SELECT VERSION()')
```

### 2. Verify Migrations

```bash
# Lihat status migrasi
php artisan migrate:status

# Jalankan migrasi tertentu
php artisan migrate --path=database/migrations/2026_04_06_045351_create_loans_table.php

# Rollback last batch
php artisan migrate:rollback

# Reset database
php artisan migrate:refresh

# Fresh + seed
php artisan migrate:fresh --seed
```

### 3. Test Queries dengan Tinker

```bash
php artisan tinker

# Cek user tertentu
> $user = App\Models\User::find(2)
> $user->name

# Cek loans user
> $user->loans()->count()
> $user->loans()->get()

# Cek dengan filter
> App\Models\Loan::where('status', 'pending')->count()
> App\Models\Fine::whereDate('created_at', '2026-04-17')->sum('amount')
```

### 4. Debug Query Building

```php
// Dalam controller, tempel query ke log
$query = Loan::where('user_id', auth()->id());
Log::debug('Query: ' . $query->toSql());
Log::debug('Bindings: ' . json_encode($query->getBindings()));
$loans = $query->get();
```

---

## 🎨 Frontend Debugging

### 1. Browser Console Debugging

**Keyboard Shortcuts:**
- Windows/Linux: `F12` atau `Ctrl+Shift+I`
- Mac: `Cmd+Option+I`

**Console Commands:**
```javascript
// 1. Cek data dari server
console.log(window.initialData);  // Check passed data from blade

// 2. Monitor network requests
// Buka Tab "Network" di DevTools

// 3. Check localStorage/sessionStorage
localStorage.getItem('key');
sessionStorage.getItem('key');

// 4. Debug form validation
const form = document.querySelector('form');
console.log(new FormData(form));

// 5. Monitor AJAX calls
document.addEventListener('ajaxSuccess', function(e) {
    console.log('AJAX Success:', e.detail);
});
```

### 2. Vue/JavaScript Debugging (Jika Ada)

```javascript
// Set breakpoint di code
debugger;

// Watch expression
console.watch('variable', function(prop, oldval, newval) {
    console.log(`${prop} changed from ${oldval} to ${newval}`);
});
```

### 3. Blade Template Debugging

```blade
<!-- Tampilkan variable -->
<pre>{{ dump($variable) }}</pre>

<!-- Cek kondisi -->
@if($condition)
    <p>Condition is TRUE</p>
@else
    <p>Condition is FALSE</p>
@endif

<!-- Debug data dalam loop -->
@foreach($items as $item)
    <!-- Show structure -->
    <pre>{{ dump($item) }}</pre>
@endforeach
```

---

## 💳 API/Payment Gateway Debugging

### Midtrans (Payment Gateway) Issues

#### Problem: 401 Unauthorized

```php
// File: app/Http/Controllers/FineController.php

public function createPayment(Request $request)
{
    Log::debug('Midtrans Config Debug', [
        'merchant_id' => config('midtrans.merchant_id'),
        'client_key_length' => strlen(config('midtrans.client_key')),
        'server_key_length' => strlen(config('midtrans.server_key')),
        'is_production' => config('midtrans.is_production'),
    ]);
    
    try {
        // Set config
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        
        // Log sebelum request
        Log::info('Creating Midtrans payment', [
            'amount' => $request->amount,
            'order_id' => $request->order_id
        ]);
        
        $snapToken = Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $request->order_id,
                'gross_amount' => (int) $request->amount,
            ]
        ]);
        
        Log::info('Snap token generated', ['token' => substr($snapToken, 0, 20) . '...']);
        
        return response()->json(['snap_token' => $snapToken]);
        
    } catch (\Exception $e) {
        Log::error('Midtrans Error', [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ]);
        
        throw $e;
    }
}
```

**Debugging Steps:**
```bash
# 1. Verify credentials
php artisan tinker
> config('midtrans.merchant_id')
> config('midtrans.server_key')
> config('midtrans.client_key')

# 2. Clear cache
php artisan config:clear

# 3. Check logs
Get-Content storage/logs/laravel.log | Select-Object -Last 50 | grep Midtrans

# 4. Verify .env
cat .env | grep MIDTRANS
```

---

## ⚡ Performance Debugging

### 1. Query Optimization

```php
// ❌ N+1 Problem
$loans = Loan::all();
foreach ($loans as $loan) {
    echo $loan->tool->name;  // Extra query per loan!
}

// ✅ Optimized with Eager Loading
$loans = Loan::with('tool')->get();
foreach ($loans as $loan) {
    echo $loan->tool->name;  // Single query
}
```

### 2. Database Profiling

```bash
php artisan tinker

# Enable query log
> DB::enableQueryLog()
> App\Models\Loan::with('tool', 'user')->get()
> dd(DB::getQueryLog())
```

### 3. Slow Query Log

```sql
-- MySQL config
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2;

-- Check slow queries
SHOW VARIABLES LIKE 'slow_query_log%';
```

---

## 🛠️ Tools & Commands

### Essential Commands

```bash
# Database
php artisan migrate                 # Run migrations
php artisan migrate:refresh       # Reset & migrate
php artisan migrate:fresh --seed  # Clean slate with seeding
php artisan tinker                # Interactive PHP shell

# Cache
php artisan config:clear          # Clear config cache
php artisan cache:clear           # Clear app cache
php artisan view:clear            # Clear Blade cache
php artisan route:clear           # Clear route cache

# Testing & Quality
php artisan test                  # Run unit tests
php artisan test --filter=AuthTest
php artisan test tests/Feature/   # Run specific test suite

# Logs
tail -f storage/logs/laravel.log  # Real-time logs (Linux)
Get-Content storage/logs/laravel.log -Wait  # Real-time logs (Windows)

# Server
php artisan serve                 # Start development server
php artisan serve --host=0.0.0.0 --port=8000
```

### Useful PHP Functions

```php
// Log data
Log::info('Message', ['key' => 'value']);
Log::debug($variable);
dd($variable);                    // Dump and die
dump($variable);                  // Dump without die
var_dump($variable);              // PHP built-in

// Database debugging
DB::enableQueryLog();
$results = Model::get();
dd(DB::getQueryLog());

// Timing
microtime(true);
Memory: memory_get_usage(true);
```

---

## 📊 Debugging Checklist

Sebelum melaporkan bug, pastikan:

- [ ] **Restart Laravel:** `php artisan serve`
- [ ] **Clear Cache:** `php artisan config:clear && php artisan cache:clear`
- [ ] **Check Logs:** `storage/logs/laravel.log`
- [ ] **Check Database:** Verify data konsisten di MySQL
- [ ] **Check Browser Console:** F12 → Console tab
- [ ] **Verify Environment:** `.env` file benar
- [ ] **Test Authentication:** Login dengan user yang tepat
- [ ] **Check Permissions:** User punya role yang sesuai
- [ ] **Mobile Testing:** Cek responsiveness dengan F12 → Device Mode
- [ ] **Clear Browser Cache:** Ctrl+Shift+Delete

---

## 🎯 Troubleshooting Flowchart

```
┌─ Is Laravel serving?
│  ├─ No → php artisan serve
│  └─ Yes → next
├─ Are there console errors?
│  ├─ Yes → Check storage/logs/laravel.log
│  └─ No → next
├─ Is data showing correctly?
│  ├─ No → Check Blade template, DB query
│  └─ Yes → next
├─ Are interactions working?
│  ├─ No → Check JavaScript, Browser DevTools
│  └─ Yes → ✅ OK
```

---

## 📚 Reference Links

- **Laravel Documentation:** https://laravel.com/docs
- **Bootstrap 5 Docs:** https://getbootstrap.com/docs/5.0/
- **Midtrans Integration:** https://docs.midtrans.com
- **MySQL Reference:** https://dev.mysql.com/doc/
- **PHP Documentation:** https://www.php.net/docs.php

---

**Setup Terakhir Update:** 17 April 2026  
**Status:** ✅ Debugging guide lengkap dan siap digunakan
