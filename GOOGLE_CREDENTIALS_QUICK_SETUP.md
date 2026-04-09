# 🔐 Google OAuth Credentials - Quick Reference

## Get Your Credentials (10 minutes)

### Option 1: Step-by-Step in Google Cloud Console

1. **Go to:** https://console.cloud.google.com
2. **Create Project:**
   - Click "Select a Project"
   - Click "NEW PROJECT"
   - Name: `MINJEM? Tool Loan`
   - Click "CREATE"

3. **Enable Google+ API:**
   - Search bar: Search `Google+ API`
   - Click on result
   - Click "ENABLE"

4. **Create OAuth Credentials:**
   - Left sidebar: "Credentials"
   - Click "+ CREATE CREDENTIALS"
   - Choose "OAuth client ID"
   - If asked: Setup Consent Screen
     - App name: `MINJEM?`
     - User support email: `your-email@gmail.com`
     - Developer contact: `your-email@gmail.com`
     - Save

5. **Configure Client:**
   - Application type: **Web application**
   - Name: **MINJEM? Web Client**
   - Authorized redirect URIs: Add these:
     ```
     http://localhost:8000/auth/google/callback
     ```
   - Click "CREATE"

6. **Copy Credentials:**
   - You'll see: **Client ID** and **Client Secret**
   - Copy both values

### Option 2: Video Tutorial
- Search YouTube: "Google OAuth 2.0 Setup Laravel"
- Follow along untuk visual guide

---

## Update .env File

### Find Your .env File:
```
📁 project_peminjaman_alat/
   └─ .env  ← This one
```

### Edit .env:
Add these 3 lines (atau replace if sudah ada):

```env
GOOGLE_CLIENT_ID=123456789-abcdefghijklmn.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-1a2b3c4d5e6f7g8h9i0j
GOOGLE_REDIRECT_URL=http://localhost:8000/auth/google/callback
```

**Replace dengan values Anda dari Google Console**

### Save .env File

---

## Verify Configuration

### 1. Clear Cache:
```bash
php artisan config:clear
```

### 2. Check Service Configuration:
```bash
php artisan tinker
```

Then in tinker:
```php
config('services.google')
// Should output your config
```

Press `Ctrl+D` untuk exit

---

## Environment Variable Checklist

**Check if defined:**
```bash
php artisan env
```

**Should se:**
```
GOOGLE_CLIENT_ID: ✅ Set
GOOGLE_CLIENT_SECRET: ✅ Set
GOOGLE_REDIRECT_URL: ✅ Set
```

---

## Test Google Login

### 1. Start Server:
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

### 2. Open Browser:
```
http://localhost:8000
```

### 3. Click "Google" Button
- Should redirect to Google login
- Login with your Google account
- Should return to app and auto-login

### 4. Success Signs:
✅ Redirected to dashboard  
✅ New user created (check database)  
✅ Activity log recorded  

---

## Troubleshooting

### "GOOGLE_CLIENT_ID is not set"
```bash
1. Open .env file
2. Check GOOGLE_CLIENT_ID value
3. Run: php artisan config:clear
4. Reload browser
```

### "Invalid Client ID"
```bash
1. Copy Client ID exactly from Google Console
2. Paste to .env
3. Tidak ada extra spaces
4. Run: php artisan config:clear
```

### "Redirect URI mismatch"
```bash
1. Go to Google Console
2. In OAuth Client: Check Authorized Redirect URIs
3. Must match exactly: http://localhost:8000/auth/google/callback
4. If different, edit and save
```

### No response from Google login button
```bash
1. Make sure GOOGLE_CLIENT_ID is set
2. Make sure server is running
3. Try hard refresh: Ctrl+Shift+R
4. Check browser console for errors
```

---

## Environment Values Template

**Copy & Paste Template:**
```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=your_client_id_from_google_console
GOOGLE_CLIENT_SECRET=your_client_secret_from_google_console
GOOGLE_REDIRECT_URL=http://localhost:8000/auth/google/callback
```

---

## Multiple Environments

### Development (.env):
```env
GOOGLE_REDIRECT_URL=http://localhost:8000/auth/google/callback
```

### Production (.env.production):
```env
GOOGLE_REDIRECT_URL=https://yourdomain.com/auth/google/callback
```

**Note:** Update redirect URI di Google Console untuk masing-masing environment

---

## Verify Installation

### Check if Socialite Installed:
```bash
composer show laravel/socialite
```

Should output: `laravel/socialite v5.26.1`

### Check if Migration Applied:
```bash
php artisan migrate:status
```

Should show: ``✅ 2024_04_09_000000_add_google_id_to_users_table`

### Check if Routes Registered:
```bash
php artisan route:list | grep google
```

Should show:
```
GET|HEAD /auth/google ......... auth.google
GET|HEAD /auth/google/callback auth.google.callback
```

---

## Security Reminders

⚠️ **DO NOT:**
- Share Client Secret publicly
- Commit .env to git
- Use personal Google account secrets

✅ **DO:**
- Keep .env secure
- Use strong OAuth scope
- Monitor OAuth applications
- Rotate credentials regularly

---

## Need Help?

1. Read [GOOGLE_OAUTH_SETUP.md](./GOOGLE_OAUTH_SETUP.md) - Detailed guide
2. Read [GOOGLE_LOGIN_GUIDE.md](./GOOGLE_LOGIN_GUIDE.md) - Feature guide
3. Check [Laravel Socialite Docs](https://laravel.com/docs/socialite)
4. Check [Google OAuth Docs](https://developers.google.com/identity)

---

**Time to Complete:** ~10 minutes  
**Difficulty:** Easy  
**Blockers:** None
