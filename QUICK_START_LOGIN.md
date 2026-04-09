# 🎯 Panduan Penggunaan Login System - MINJEM?

## 🚀 Akses Aplikasi

**URL:** http://localhost:8000

---

## 👥 Test Credentials (Dari Database Seeder)

### 1. **Admin Test Account**
```
Email:    admin@app.com
Password: password999
Role:     Admin
Access:   /admin/dashboard
```

### 2. **Petugas Test Account**
```
Email:    petugas@app.com
Password: password999
Role:     Petugas
Access:   /petugas/dashboard
```

### 3. **Peminjam Test Account (Default)**
```
Email:    siswa@app.com
Password: password123
Role:     Peminjam
Access:   /peminjam/dashboard
```

---

## 📱 Cara Menggunakan

### **A. Login dengan Akun Existing**
1. Buka http://localhost:8000
2. Klik tab "Sign In"
3. Masukkan email dan password
4. Klik tombol "Sign In"
5. Sistem akan redirect ke dashboard sesuai role

### **B. Membuat Akun Baru (Sign Up)**
1. Buka http://localhost:8000/login
2. Klik tab "Signup"
3. Isi form:
   - Full Name: Masukkan nama lengkap
   - Email: Masukkan email (belum terdaftar)
   - Password: Min 8 karakter
   - Confirm Password: Harus sama dengan password
4. Klik "Create Account"
5. Sistem akan redirect ke login
6. Login dengan akun yang baru dibuat
7. Akan masuk ke /peminjam/dashboard

### **C. Logout**
1. Klik menu dropdown atau logout button (ada di sidebar)
2. Kilik "Logout"
3. Session akan dihapus dan redirect ke login

---

## ✨ Fitur-Fitur yang Berfungsi

### Form Validation
- ✅ Email validation (format valid)
- ✅ Password validation (min 6 untuk login, min 8 untuk registrasi)
- ✅ Unique email validation (tidak bisa double)
- ✅ Password confirmation validation

### Security
- ✅ Password hashing dengan bcrypt
- ✅ CSRF token protection
- ✅ Session regeneration
- ✅ Role-based access control
- ✅ Activity logging

### UI/UX
- ✅ Tab navigation (Sign In ↔ Signup)
- ✅ Real-time error messages
- ✅ Responsive design (desktop & mobile)
- ✅ Loading states
- ✅ Error alerts

---

## 🔍 Troubleshooting

### Problem: "Login gagal" muncul
**Solution:**
- Pastikan email dan password benar
- Periksa database apakah user ada
- Gunakan test credentials di atas

### Problem: "email telah digunakan"
**Solution:**
- Email sudah terdaftar di sistem
- Gunakan email yang berbeda
- Atau login dengan akun yang sudah ada

### Problem: "password tidak cocok"
**Solution:**
- Password dan Confirm Password harus sama
- Pastikan tidak ada typo

### Problem: Password terlalu pendek
**Solution untuk Sign Up:**
- Password minimal 8 karakter
- Gunakan kombinasi huruf dan angka untuk keamanan

**Solution untuk Sign In:**
- Password minimal 6 karakter
- (Database existing users mungkin lebih pendek)

### Problem: Tidak bisa akses dashboard setelah login
**Solution:**
- Refresh halaman
- Clear browser cache
- Pastikan role user sesuai dengan rute yang diakses

---

## 🧪 Testing Manual

### Test Case 1: Login Berhasil
```
1. Masukkan: siswa@app.com / password123
2. Klik Sign In
3. ✅ Harusnya ke /peminjam/dashboard
```

### Test Case 2: Email Tidak Valid
```
1. Masukkan: invalidemail
2. Klik Sign In
3. ✅ Harusnya ada error "email tidak valid"
```

### Test Case 3: Password Salah
```
1. Masukkan: siswa@app.com / salahpassword
2. Klik Sign In
3. ✅ Harusnya ada error "Login gagal"
```

### Test Case 4: Registrasi Berhasil
```
1. Klik tab "Signup"
2. Isi form dengan data baru yang valid
3. Klik "Create Account"
4. ✅ Harusnya redirect ke login dengan message sukses
```

### Test Case 5: Tab Switching
```
1. Klik "Sign In" dan "Signup" berkali-kali
2. ✅ Form harus berubah dengan lancar
```

---

## 📊 User Flow

```
┌─ Homepage (/)
│
├─ If Authenticated
│  ├─ Admin   → /admin/dashboard
│  ├─ Petugas → /petugas/dashboard
│  └─ Peminjam → /peminjam/dashboard
│
└─ If Not Authenticated → /login (login.blade.php)
   │
   ├─ Tab: Sign In
   │  ├─ Input email & password
   │  ├─ Submit POST /login
   │  └─ Redirect ke role dashboard
   │
   └─ Tab: Signup
      ├─ Input name, email, password
      ├─ Submit POST /register
      └─ Create user & redirect to login
```

---

## 🔐 Security Notes

1. **Passwords:** Semua password di-hash dengan bcrypt
2. **CSRF:** Semua form dilindungi dengan CSRF token
3. **Sessions:** Regenerated setelah login berhasil
4. **Activity Logging:** Setiap login dicatat di activity_logs table
5. **Middleware:** Role-based access control aktif di semua route

---

## 📝 API Endpoints

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/` | Home (redirect jika sudah login) |
| GET | `/login` | Show login form |
| POST | `/login` | Process login |
| GET | `/register` | Show register form |
| POST | `/register` | Process registration |
| POST | `/logout` | Logout user |
| GET | `/admin/dashboard` | Admin panel (role: admin) |
| GET | `/petugas/dashboard` | Petugas panel (role: petugas) |
| GET | `/peminjam/dashboard` | Peminjam panel (role: peminjam) |

---

## ✅ Checklist Implementasi

- [x] Login form
- [x] Register form
- [x] Form validation
- [x] Error handling
- [x] Password hashing
- [x] Session management
- [x] Role-based redirect
- [x] Activity logging
- [x] CSRF protection
- [x] Responsive UI
- [x] Test credentials seeded
- [x] Middleware protection

---

## 📞 Support

Jika ada masalah dengan login system:
1. Cek file `LOGIN_FEATURES.md` untuk dokumentasi lengkap
2. Cek file `tests/Feature/AuthTest.php` untuk test cases
3. Lihat error message di browser console
4. Check Laravel log di `storage/logs/laravel.log`

---

**Status:** ✅ READY TO USE
**Last Updated:** 2024
