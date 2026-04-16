<?php 
 
namespace App\Http\Controllers; 
 
use App\Models\Loan; 
use App\Models\Tool; 
use App\Models\Fine; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
 
class PetugasController extends Controller 
{ 
    public function index() { 
 
        $loans = Loan::where('status', 'pending')->with(['user', 'tool'])->get();  
        $activeLoans = Loan::where('status', 'disetujui')->with(['user', 'tool'])->get(); 
        $sudahDikembalikan = Loan::where('status', 'kembali')->with(['user', 'tool'])->get();    
        
        // Check for overdue loans
        $overdueLoans = Loan::where('status', 'disetujui')
                           ->where('tanggal_kembali_rencana', '<', now())
                           ->with(['user', 'tool'])
                           ->get();
        
        return view('petugas.dashboard', compact('loans', 'activeLoans', 'sudahDikembalikan', 'overdueLoans')); 
    } 
 
    public function approve($id) { 
        $loan = Loan::findOrFail($id); 
        $loan->update([ 
            'status' => 'disetujui', 
            'petugas_id' => Auth::id() 
        ]);  
        $tool = Tool::find($loan->tool_id); 
        $tool->decrement('stok');          
        return back()->with('success', 'Peminjaman disetujui.'); 
    } 

    public function reject($id) {
        $loan = Loan::findOrFail($id);

        if ($loan->status !== 'pending') {
            return back()->with('error', 'Hanya permintaan pending yang bisa ditolak.');
        }

        $loan->update([
            'status' => 'ditolak',
            'petugas_id' => Auth::id()
        ]);

        return back()->with('success', 'Peminjaman telah ditolak.');
    }

    public function processReturn($id) { 
        $loan = Loan::findOrFail($id); 

        if ($loan->status !== 'disetujui') {
            return back()->with('error', 'Status peminjaman tidak valid untuk pengembalian.');
        }

        $loan->update([ 
            'status' => 'kembali', 
            'tanggal_kembali_aktual' => now(),
            'petugas_id' => Auth::id() 
        ]); 
        $tool = Tool::find($loan->tool_id); 
        $tool->increment('stok'); 

        $fineAmount = $loan->calculateFine();
        if ($fineAmount > 0) {
            Fine::firstOrCreate([
                'loan_id' => $loan->id,
            ], [
                'amount' => $fineAmount,
                'status' => 'pending',
                'reason' => 'Keterlambatan pengembalian alat'
            ]);
        }

        return back()->with('success', 'Alat telah dikembalikan.'); 
    } 
 
    public function report(Request $request) {  
        $loans = Loan::with(['user', 'tool'])->orderBy('created_at', 'desc')->get(); 
        return view('petugas.laporan', compact('loans')); 
    } 
} 