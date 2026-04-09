<?php 
 
namespace App\Http\Controllers; 
 
use App\Models\ActivityLog; 
use App\Models\Loan; 
use App\Models\Tool; 
use App\Models\Fine;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
 
class PeminjamController extends Controller 
{ 
    public function index() { 
        $tools = Tool::with('category')->get(); 
        
        // Check for pending fines
        $pendingFines = Fine::whereHas('loan', function($query) {
            $query->where('user_id', Auth::id());
        })->where('status', 'pending')->get();
        
        $totalPendingFine = $pendingFines->sum('amount');
        
        // Check for overdue loans
        $overdueLoans = Loan::where('user_id', Auth::id())
                           ->where('status', 'disetujui')
                           ->where('tanggal_kembali_rencana', '<', now())
                           ->with(['tool'])
                           ->get();
        
        return view('peminjam.dashboard', compact('tools', 'pendingFines', 'totalPendingFine', 'overdueLoans')); 
    } 
     
    public function store(Request $request) { 
        $tool = Tool::find($request->tool_id); 
        if($tool->stok > 0) { 
            Loan::create([ 
                'user_id' => Auth::id(), 
                'tool_id' => $request->tool_id, 
                'tanggal_pinjam' => now(), 
                'tanggal_kembali_rencana' => $request->tanggal_kembali, 
                'status' => 'pending' 
            ]); 
            ActivityLog::record('Tambah Alat', 'Menambahkan alat baru: ' . $request->nama_alat); 
            return back()->with('success', 'Pengajuan berhasil, menunggu persetujuan.'); 
        } 
    } 
 
    public function history() { 
        $loans = Loan::where('user_id', Auth::id()) 
                    ->with(['tool', 'fines']) 
                    ->orderBy('created_at', 'desc') 
                    ->get(); 
        
        // Check for pending fines
        $pendingFines = Fine::whereHas('loan', function($query) {
            $query->where('user_id', Auth::id());
        })->where('status', 'pending')->get();
        
        $totalPendingFine = $pendingFines->sum('amount');
        
        // Check for overdue loans
        $overdueLoans = Loan::where('user_id', Auth::id())
                           ->where('status', 'disetujui')
                           ->where('tanggal_kembali_rencana', '<', now())
                           ->with(['tool'])
                           ->get();
        
        return view('peminjam.riwayat', compact('loans', 'pendingFines', 'totalPendingFine', 'overdueLoans')); 
    }

    public function fines() {
        $fines = Fine::whereHas('loan', function($query) {
            $query->where('user_id', Auth::id());
        })->with(['loan.tool'])->orderBy('created_at', 'desc')->paginate(10);
        
        // Get total statistics (not paginated)
        $allFines = Fine::whereHas('loan', function($query) {
            $query->where('user_id', Auth::id());
        })->get();
        
        $pendingFines = $allFines->where('status', 'pending');
        $paidFines = $allFines->where('status', 'paid');
        
        $totalPendingAmount = $pendingFines->sum('amount');
        $totalPaidAmount = $paidFines->sum('amount');
        
        return view('peminjam.denda', compact('fines', 'pendingFines', 'paidFines', 'totalPendingAmount', 'totalPaidAmount'));
    } 
}