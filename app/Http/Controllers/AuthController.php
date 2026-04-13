<?php 
namespace App\Http\Controllers; 
use App\Models\ActivityLog; 
use App\Models\User; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller 
{ 
    public function showLoginForm() { 
        return view('auth.login'); 
    } 
    public function showRegisterForm() { 
        return view('auth.login'); 
    } 
    public function login(Request $request) { 
        $credentials = $request->validate([ 
            'email' => ['required', 'email'], 
            'password' => ['required'], 
        ]); 
        if (Auth::attempt($credentials)) { 
            $request->session()->regenerate(); 
            ActivityLog::record('Login', 'Pengguna melakukan login'); 
            if (Auth::user()->role == "admin") { 
                return redirect('/admin/dashboard'); 
            } 
            if (Auth::user()->role == "petugas") { 
                return redirect('/petugas/dashboard'); 
            } 
            return redirect('/peminjam/dashboard'); 
        } 
        return back()->withErrors(['email' => 'Login gagal.']); 
    } 
    public function register(Request $request) { 
        $request->validate([ 
            'name' => 'required|string|max:255', 
            'email' => 'required|string|email|max:255|unique:users', 
            'password' => 'required|string|min:8|confirmed', 
        ]); 
 
        User::create([ 
            'name' => $request->name, 
            'email' => $request->email, 
            'password' => bcrypt($request->password), 
            'role' => 'peminjam', 
        ]); 
 
        return redirect('/login')->with('success', 'Registration successful. Please login.'); 
    } 
    public function logout(Request $request) { 
        Auth::logout(); 
        $request->session()->invalidate(); 
        $request->session()->regenerateToken(); 
        return redirect('/login'); 
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('google_id', $googleUser->getId())
                        ->orWhere('email', $googleUser->getEmail())
                        ->first();
            
            if ($user) {
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            } else {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'role' => 'peminjam',
                    'password' => bcrypt(uniqid('google_', true)), // Random password untuk OAuth
                ]);
            }

            Auth::login($user, remember: true);

            ActivityLog::record('Login', 'Pengguna melakukan login via Google');
            
            request()->session()->regenerate();

            if ($user->role == "admin") {
                return redirect('/admin/dashboard');
            }
            if ($user->role == "petugas") {
                return redirect('/petugas/dashboard');
            }
            return redirect('/peminjam/dashboard');
            
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['email' => 'Gagal login dengan Google. Silakan coba lagi.']);
        }
    }
} 