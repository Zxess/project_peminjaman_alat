<?php 
namespace App\Http\Controllers; 
use Illuminate\Http\Request; 
use App\Models\User; 
use App\Models\Tool; 
use App\Models\Category; 
use App\Models\Loan; 
use App\Models\ActivityLog; 
 
class AdminController extends Controller 
{ 
    public function index() 
    {  
        $totalUser = User::count(); 
        $totalAlat = Tool::count();
        $totalStok = Tool::sum('stok'); 
        $totalKategori = Category::count(); 
        $sedangDipinjam = Loan::where('status', 'disetujui')->count(); 
        $sudahDikembalikan = Loan::where('status', 'kembali')->count();  
        $recentLogs = ActivityLog::with('user')->latest()->take(5)->get(); 
        return view('admin.dashboard', compact( 
            'totalUser',  
            'totalAlat',  
            'totalStok',  
            'totalKategori',  
            'sedangDipinjam', 
            'sudahDikembalikan', 
            'recentLogs' 
        )); 
    } 
} 