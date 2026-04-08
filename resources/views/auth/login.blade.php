@extends('layouts.app') 

@section('content') 
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;"> 
        <div class="col-md-5 col-lg-4"> 
    
            <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;"> 

                <div class="card-header bg-primary bg-gradient text-white text-center py-4 border-0"> 
                    <h5 class="mb-0 fw-bold">Login Aplikasi</h5>
                    <small class="opacity-75">Peminjaman Alat Hubad</small>
                </div> 

                <div class="card-body p-4 p-md-5"> 
                    <form action="{{ url('/login') }}" method="POST"> 
                        @csrf 
                        
                        <div class="mb-4"> 
                            <label class="form-label fw-semibold text-secondary">Email Address</label> 
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" name="email" class="form-control bg-light border-start-0 ps-0" placeholder="nama@email.com" required autofocus> 
                            </div>
                        </div> 

                        <div class="mb-4"> 
                            <label class="form-label fw-semibold text-secondary">Password</label> 
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" name="password" class="form-control bg-light border-start-0 ps-0" placeholder="••••••••" required> 
                            </div>
                        </div> 

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm rounded-pill py-2">
                                Masuk <i class="fas fa-sign-in-alt ms-2"></i>
                            </button> 
                        </div>
                    </form> 
                </div> 
            </div> 

            <p class="text-center text-muted mt-4 small">
                &copy; Zxess {{ date('d.M.Y') }} Pusdikhubad. All Rights Reserved.
            </p>
        </div> 
    </div> 
</div> 
@endsection
