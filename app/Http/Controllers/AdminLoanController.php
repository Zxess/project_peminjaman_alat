<?php 
 
namespace App\Http\Controllers; 
 
use App\Models\Loan; 
use App\Models\User; 
use App\Models\Tool; 
use App\Models\ActivityLog;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
 
class AdminLoanController extends Controller 
{  
    public function index() 
    { 
        $loans = Loan::with(['user', 'tool'])->latest()->paginate(10); 
        return view('admin.loans.index', compact('loans')); 
    } 
 
    public function create() 
    {  
        $users = User::where('role', 'peminjam')->get();  
        $tools = Tool::all(); 
         
        return view('admin.loans.create', compact('users', 'tools')); 
    } 
 
    public function store(Request $request) 
    { 
        $request->validate([ 
            'user_id' => 'required', 
            'tool_id' => 'required', 
            'tanggal_pinjam' => 'required|date', 
            'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam', 
            'status' => 'required' 
        ]); 
  
        $tool = Tool::findOrFail($request->tool_id); 
        if ($request->status == 'disetujui' && $tool->stok < 1) { 
 
            return back()->with('error', 'Stok alat kosong, tidak bisa set status Disetujui.'); 
        } 
 
        Loan::create([ 
            'user_id' => $request->user_id, 
            'tool_id' => $request->tool_id, 
            'tanggal_pinjam' => $request->tanggal_pinjam, 
            'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana, 
            'status' => $request->status, 
            'petugas_id' => Auth::id() 
        ]); 
  
        if ($request->status == 'disetujui') { 
            $tool->decrement('stok'); 
        } 
 
        ActivityLog::record('Create Loan', 'Admin membuat data pinjaman baru'); 
 
        return redirect()->route('admin.loans.index')->with('success', 'Data peminjaman berhasil 
dibuat.'); 
    } 
  
    public function edit($id) 
    { 
        $loan = Loan::findOrFail($id); 
        $users = User::where('role', 'peminjam')->get(); 
        $tools = Tool::all(); 
 
        return view('admin.loans.edit', compact('loan', 'users', 'tools')); 
    } 
 
    public function update(Request $request, $id) 
    { 
        $loan = Loan::findOrFail($id); 
        $tool = Tool::findOrFail($request->tool_id); 
        if ($loan->status == 'pending' && $request->status == 'disetujui') { 
            $tool->decrement('stok'); 
        }  
        elseif ($loan->status == 'disetujui' && $request->status == 'kembali') { 
            $tool->increment('stok'); 
            $request->merge(['tanggal_kembali_aktual' => now()]); 
        } 
        elseif ($loan->status == 'disetujui' && $request->status == 'pending') { 
            $tool->increment('stok'); 
        }  
        $loan->update([ 
            'user_id' => $request->user_id, 
            'tool_id' => $request->tool_id, 
            'tanggal_pinjam' => $request->tanggal_pinjam, 
            'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana, 
            'status' => $request->status, 
            'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual ?? $loan->tanggal_kembali_aktual 
        ]); 
 
        return redirect()->route('admin.loans.index')->with('success', 'Data berhasil diperbarui.'); 
    } 
  
    public function destroy($id) 
    { 
        $loan = Loan::findOrFail($id); 
        if ($loan->status == 'disetujui') { 
            $loan->tool->increment('stok'); 
        } 
 
        $loan->delete(); 
        return redirect()->route('admin.loans.index')->with('success', 'Data peminjaman dihapus.'); 
    } 
}