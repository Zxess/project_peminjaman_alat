# 🔐 Google OAuth Setup Guide - MINJEM?

## Langkah 1: Setup Google Cloud Console

### 1.1 Buat Project di Google Cloud Console
1. Kunjungi [Google Cloud Console](https://console.cloud.google.com)
2. Login dengan akun Google Anda
3. Klik **"Select a Project"** di atas
4. Klik **"NEW PROJECT"**
5. Beri nama project: `MINJEM? Tool Loan`
6. Klik **"CREATE"**

### 1.2 Enable Google+ API
1. Di dashboard, cari search bar `APIs & Services`
2. Klik **"Enabled APIs & services"**
3. Klik **"+ ENABLE APIS AND SERVICES"**
4. Cari "Google+ API"
5. Klik pada hasil pencarian
6. Klik **"ENABLE"**

### 1.3 Create OAuth Credentials
1. Di sidebar kiri, klik **"Credentials"**
2. Klik **"+ CREATE CREDENTIALS"**
3. Pilih **"OAuth client ID"**
4. Jika diminta setup Consent Screen, lakukan:
   - Pilih **"External"** sebagai User Type
   - Klik **"CREATE"**
   - Isi form dengan info app Anda
   - Klik **"SAVE AND CONTINUE"**

### 1.4 Configure OAuth Consent Screen (Jika diminta)
1. Pada halaman OAuth Consent Screen:
   - **App name:** MINJEM?
   - **User support email:** your-email@gmail.com
   - **Developer contact:** your-email@gmail.com
2. Klik **"SAVE AND CONTINUE"**
3. Skip Scopes (optional)
4. Klik **"SAVE AND CONTINUE"**

### 1.5 Create OAuth2 Client ID
1. Kembali ke Credentials
2. Klik **"+ CREATE CREDENTIALS"** lagi
3. Pilih **"OAuth client ID"**
4. Pilih **"Web application"** sebagai Application type
5. Beri nama: `MINJEM? Web Client`

### 1.6 Setup Authorized Redirect URIs
Di bagian "Authorized redirect URIs", tambahkan:
```
http://localhost:8000/auth/google/callback
https://yourdomain.com/auth/google/callback  (untuk production)
```

Klik **"CREATE"**

### 1.7 Simpan Credentials
Setelah dibuat, akan muncul popup dengan:
- **Client ID**
- **Client Secret**

Copy kedua nilai ini.

---

## Langkah 2: Setup Environment Variables

### 2.1 Update .env file
1. Buka file `.env` di root project:
```bash
c:\xampp\htdocs\project_peminjaman_alat\.env
```

2. Tambahkan atau update variabel Google:
```env
GOOGLE_CLIENT_ID=your_client_id_here
GOOGLE_CLIENT_SECRET=your_client_secret_here
GOOGLE_REDIRECT_URL=http://localhost:8000/auth/google/callback
```

**Contoh:**
```env
GOOGLE_CLIENT_ID=123456789-abcdefg.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-abcdefghijklmnop
GOOGLE_REDIRECT_URL=http://localhost:8000/auth/google/callback
```

3. Simpan file `.env`

### 2.2 Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

---

## Langkah 3: Implementasi di Application

### 3.1 Routes sudah tersetup
File: `routes/web.php`
```php
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
```

### 3.2 Controller Methods sudah tersetup
File: `app/Http/Controllers/AuthController.php`
```php
public function redirectToGoogle()
public function handleGoogleCallback()
```

### 3.3 Login Form sudah terintegrasi
File: `resources/views/auth/login.blade.php`
```html
<a href="{{ route('auth.google') }}" class="social-login-btn google-btn">
    <img src="..." alt="Google"> Login dengan Google
</a>
```

---

## Langkah 4: Testing

### 4.1 Local Testing
1. Pastikan `.env` sudah dikonfigurasi dengan credentials Google
2. Jalankan server:
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

3. Buka browser ke http://localhost:8000
4. Klik tombol "Google" di login page
5. Akan redirect ke Google Login
6. Setelah login, akan kembali ke aplikasi dan auto-create user

### 4.2 Troubleshooting

**Error: "Invalid client ID"**
- Pastikan GOOGLE_CLIENT_ID benar di .env
- Jalankan: `php artisan config:clear`

**Error: "Redirect URI mismatch"**
- Pastikan redirect URI di Google Console match dengan app Anda
- Untuk localhost: `http://localhost:8000/auth/google/callback`
- Untuk production: gunakan domain asli Anda

**Error: "User not authenticated"**
- Pastikan callback route berfungsi dengan baik
- Check Laravel logs di `storage/logs/laravel.log`

---

## Langkah 5: Production Deployment

### 5.1 Update Redirect URI
Di Google Cloud Console:
1. Buka Credentials
2. Edit OAuth Client ID
3. Update "Authorized redirect URIs":
```
https://your-domain.com/auth/google/callback
```

### 5.2 Update .env untuk Production
```env
GOOGLE_REDIRECT_URL=https://your-domain.com/auth/google/callback
APP_ENV=production
APP_DEBUG=false
```

### 5.3 SSL Certificate
Pastikan domain memiliki SSL certificate (HTTPS)

---

## User Flow

```
User Page
   ↓
Klik "Login dengan Google"
   ↓
redirectToGoogle() → Redirect to Google Login
   ↓
User Login di Google
   ↓
Google Callback → /auth/google/callback
   ↓
handleGoogleCallback()
   ├─ Get user info dari Google
   ├─ Check if email exists
   ├─ Create/Update user
   ├─ Auto-login user
   └─ Redirect to dashboard
```

---

## Database Changes

### User Table akan menyimpan:
```
id
name (dari Google)
email (dari Google)
google_id (unique identifier)
role (default: peminjam)
password (null - menggunakan Google OAuth)
```

---

## File yang Termodifikasi

✅ `config/services.php` - Google OAuth config  
✅ `app/Http/Controllers/AuthController.php` - Google methods  
✅ `routes/web.php` - Google routes  
✅ `resources/views/auth/login.blade.php` - Google button  
✅ `database/migrations/*.php` - Add google_id column  

---

## Environment Variables Checklist

```env
GOOGLE_CLIENT_ID=                    # Copy dari Google Console
GOOGLE_CLIENT_SECRET=                # Copy dari Google Console
GOOGLE_REDIRECT_URL=http://localhost:8000/auth/google/callback
```

---

## Testing Credentials (Development)

Untuk testing, Anda bisa menggunakan Google test account:
- Email: your-test-email@gmail.com
- Password: your-password

---

## Security Notes

1. **Never commit .env file**
   - `.env` berisi sensitive credentials
   - Gunakan `.env.example` untuk template

2. **Rotate credentials jika leaked**
   - Di Google Cloud Console, regenerate credentials

3. **Use HTTPS in production**
   - OAuth hanya bekerja dengan HTTPS di production

4. **Validate callback**
   - Selalu verify data dari Google sebelum create user

---

## Support Links

- [Google Cloud Console](https://console.cloud.google.com)
- [Laravel Socialite Docs](https://laravel.com/docs/socialite)
- [Google OAuth 2.0 Docs](https://developers.google.com/identity/protocols/oauth2)

---

**Status:** Setup Guide Complete ✅  
**Version:** 1.0
