<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MINJEM - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: white;
            overflow-x: hidden;
        }

        /* Container 2 kolom penuh layar */
        .login-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* KOLOM KIRI - FORM LOGIN */
        .login-left {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px;
            background: white;
        }

        .login-card {
            max-width: 460px;
            width: 100%;
        }

        /* Logo SmartSave */
        .logo {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #1E1B4B 0%, #4C1D95 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 48px;
            letter-spacing: -0.5px;
        }

        /* Welcome Section */
        .welcome-title {
            font-size: 36px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .welcome-subtitle {
            font-size: 16px;
            color: #6B7280;
            margin-bottom: 40px;
            line-height: 1.5;
        }

        /* Tab Navigation */
        .tab-nav {
            display: flex;
            gap: 24px;
            margin-bottom: 32px;
            border-bottom: 1px solid #E5E7EB;
        }

        .tab-btn {
            font-size: 16px;
            font-weight: 600;
            background: none;
            border: none;
            padding: 0 0 12px 0;
            cursor: pointer;
            color: #9CA3AF;
            transition: all 0.2s ease;
        }

        .tab-btn.active {
            color: #4F46E5;
            border-bottom: 2px solid #4F46E5;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 0;
            border: none;
            border-bottom: 1px solid #E5E7EB;
            font-size: 15px;
            outline: none;
            background: transparent;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            border-bottom-color: #4F46E5;
        }

        .form-input::placeholder {
            color: #9CA3AF;
        }

        /* Continue Button */
        .continue-btn {
            width: 100%;
            background: #4F46E5;
            color: white;
            border: none;
            border-radius: 100px;
            padding: 14px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            margin: 16px 0 32px 0;
            transition: background 0.2s;
        }

        .continue-btn:hover {
            background: #4338CA;
        }

        /* Divider */
        .divider {
            text-align: center;
            margin: 24px 0;
            position: relative;
        }

        .divider hr {
            border: none;
            border-top: 1px solid #E5E7EB;
        }

        .divider span {
            background: white;
            padding: 0 12px;
            font-size: 12px;
            color: #9CA3AF;
            position: relative;
            top: -10px;
        }

        /* Social Buttons */
        .social-buttons {
            display: flex;
            gap: 16px;
            margin-bottom: 40px;
            justify-content: center;
        }

        .social-btn {
            flex: 0 1 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 24px;
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 100px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            transition: all 0.2s;
            text-decoration: none;
        }

        .social-btn:hover {
            background: #F9FAFB;
            border-color: #D1D5DB;
        }

        /* Info Text */
        .info-text {
            font-size: 12px;
            color: #6B7280;
            line-height: 1.6;
            text-align: left;
        }

        .info-text strong {
            color: #4F46E5;
        }

        /* KOLOM KANAN - ILUSTRASI */
        .login-right {
            flex: 1;
            background: linear-gradient(145deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Background pattern */
        .login-right::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.3;
        }

        /* Ilustrasi */
        .illustration {
            text-align: center;
            z-index: 1;
            padding: 48px;
        }

        .illustration img {
            width: 100%;
            max-width: 380px;
            height: auto;
            margin-bottom: 32px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .illustration h2 {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 16px;
            letter-spacing: -0.3px;
        }

        .illustration p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 15px;
            line-height: 1.6;
            max-width: 360px;
            margin: 0 auto;
        }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
            }
            
            .login-left, .login-right {
                flex: auto;
                min-height: 50vh;
            }
            
            .login-left {
                padding: 40px 24px;
            }
            
            .welcome-title {
                font-size: 28px;
            }
            
            .illustration svg {
                max-width: 260px;
            }
            
            .illustration h2 {
                font-size: 22px;
            }
        }

        /* Error styling */
        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 8px;
            display: block;
        }

        .alert-danger {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            border-left: 3px solid #dc2626;
        }
    </style>
</head>
<body>
    <div class="login-container">
        
        {{-- KOLOM KIRI - FORM LOGIN --}}
        <div class="login-left">
            <div class="login-card">
                
                {{-- SmartSave Logo --}}
                <div class="logo">MINJEM?</div>

                {{-- Welcome Back --}}
                <h1 class="welcome-title">Welcome Back</h1>
                <p class="welcome-subtitle">Please enter your details</p>

                {{-- Tab Sign In / Signup --}}
                <div class="tab-nav">
                    <button class="tab-btn active" id="signinTab">Sign In</button>
                    <button class="tab-btn" id="signupTab">Signup</button>
                </div>

                {{-- Error Messages --}}
                @if($errors->any())
                <div class="alert-danger">
                    {{ $errors->first() }}
                </div>
                @endif

                {{-- SIGN IN FORM --}}
                <div id="signinForm">
                    <form action="{{ url('/login') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-input" placeholder="siswa@app.com" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-input" placeholder="Enter your password" minlength="6" required>
                            @error('password')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="continue-btn">Sign In</button>
                    </form>
                </div>

                {{-- SIGN UP FORM --}}
                <div id="signupForm" style="display: none;">
                    <form action="{{ url('/register') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-input" placeholder="nama kamu" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-input" placeholder="nama_gmail_kamu@gmail.com" required>
                            @error('email')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-input" placeholder="Min 8 characters" minlength="8" required>
                            @error('password')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-input" placeholder="Confirm password" minlength="8" required>
                            @error('password_confirmation')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="continue-btn">Create Account</button>
                    </form>
                </div>

                {{-- Or Continue With --}}
                <div class="divider">
                    <hr>
                    <span>Or Continue With</span>
                </div>

                {{-- Social Buttons --}}
                {{-- <div class="social-buttons">
                    <a href="{{ route('auth.google') }}" class="social-btn" style="text-decoration: none; color: inherit;">
                        <svg width="18" height="18" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span>Google</span>
                    </a>
                </div> --}}

                {{-- Info Text --}}
                <div class="info-text">
                    Kelola peminjaman alat dengan mudah dan efisien. 
                    <strong>Login</strong> untuk mengakses dashboard pribadi Anda, lihat riwayat peminjaman, 
                    dan kelola denda dengan transparan.
                </div>

            </div>
        </div>

        {{-- KOLOM KANAN - ILUSTRASI --}}
        <div class="login-right">
            <div class="illustration">
                <img src="{{ asset('images/pusdik1.png') }}" alt="Tool Loan Management Illustration" class="illustration-image">
                
                <h2>SISTEM PEMINJAMAN ALAT</h2>
                <h2>SMK PK PUSDIKHUBAD CIMAHI</h2>
                <p>Bingung mau minjem alat? Login sekarang!</p>
            </div>
        </div>
    </div>

    <script>
        // Tab switching logic
        const signinTab = document.getElementById('signinTab');
        const signupTab = document.getElementById('signupTab');
        const signinForm = document.getElementById('signinForm');
        const signupFormEl = document.getElementById('signupForm');

        function switchToSignin() {
            signinTab.classList.add('active');
            signupTab.classList.remove('active');
            signinForm.style.display = 'block';
            signupFormEl.style.display = 'none';
        }

        function switchToSignup() {
            signupTab.classList.add('active');
            signinTab.classList.remove('active');
            signinForm.style.display = 'none';
            signupFormEl.style.display = 'block';
        }

        signinTab.addEventListener('click', switchToSignin);
        signupTab.addEventListener('click', switchToSignup);
    </script>
</body>
</html>