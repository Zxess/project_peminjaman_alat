<?php 
 
namespace App\Http\Controllers; 
 
use Illuminate\Http\Request; 
use App\Models\Loan; 
use App\Models\Tool; 
use App\Models\ActivityLog; 
use App\Models\Fine; 
 
class AdminReturnController extends Controller 
{ 
    public function index() 
    { 
        $returns = Loan::with(['user', 'tool']) 
                    ->where('status', 'kembali') 
                    ->latest('tanggal_kembali_aktual') 
                    ->paginate(10); 
 
        return view('admin.returns.index', compact('returns')); 
    } 
 
    public function create() 
    { 
        $activeLoans = Loan::with(['user', 'tool']) 
                        ->where('status', 'disetujui') 
                        ->latest() 
                        ->get(); 
 
        return view('admin.returns.create', compact('activeLoans')); 
    } 
 
    public function store(Request $request) 
    { 
        $request->validate([ 
            'loan_id' => 'required|exists:loans,id', 
            'denda' => 'nullable|integer'  
        ]); 
 
        $loan = Loan::findOrFail($request->loan_id); 
 
        if ($loan->status != 'disetujui') { 
            return back()->with('error', 'Data tidak valid atau sudah dikembalikan.'); 
        } 
  
        $loan->update([ 
            'status' => 'kembali', 
            'tanggal_kembali_aktual' => now(), 
        ]); 
  
        $tool = Tool::findOrFail($loan->tool_id); 
        $tool->increment('stok'); 
 
        // Calculate and save fine if late
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
 
        ActivityLog::record('Pengembalian (Admin)', 'Memproses pengembalian alat: ' . $tool->nama_alat); 
 
        return redirect()->route('admin.returns.index')->with('success', 'Alat berhasil dikembalikan.'); 
    } 
 
    public function edit($id) 
    { 
        $loan = Loan::findOrFail($id);  
        if ($loan->status != 'kembali') { 
            return redirect()->route('admin.returns.index'); 
        } 
 
        return view('admin.returns.edit', compact('loan')); 
    } 
 
    public function update(Request $request, $id) 
    { 
        $loan = Loan::findOrFail($id); 
 
        $request->validate([ 
            'tanggal_kembali_aktual' => 'required|date' 
        ]); 
 
        $loan->update([ 
            'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual 
        ]); 
 
        return redirect()->route('admin.returns.index')->with('success', 'Data pengembalian diperbarui.'); 
    } 
 
    public function destroy($id) 
    { 
        $loan = Loan::findOrFail($id);
        $loan->delete(); 
 
        return redirect()->route('admin.returns.index')->with('success', 'Riwayat dihapus.'); 
    } 
} 
