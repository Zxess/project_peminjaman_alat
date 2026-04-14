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
        $returnRequests = Loan::where('status', 'dikembalikan')->with(['user', 'tool'])->get(); 
        $sudahDikembalikan = Loan::where('status', 'kembali')->with(['user', 'tool'])->get();    
        
        // Check for overdue loans
        $overdueLoans = Loan::where('status', 'disetujui')
                           ->where('tanggal_kembali_rencana', '<', now())
                           ->with(['user', 'tool'])
                           ->get();
        
        return view('petugas.dashboard', compact('loans', 'activeLoans', 'returnRequests', 'sudahDikembalikan', 'overdueLoans')); 
    }

    // ✅ TAMBAHKAN METHOD INI UNTUK APPROVE/ MENYETUJUI
    public function approve($id)
    {
        $loan = Loan::findOrFail($id);

        // Cek apakah status masih pending
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Hanya permintaan yang statusnya "pending" bisa disetujui.');
        }

        // Update status menjadi disetujui
        $loan->update([ 
            'status' => 'disetujui', 
            'petugas_id' => Auth::id() 
        ]);

        // Kurangi stok alat
        $tool = Tool::find($loan->tool_id); 
        $tool->decrement('stok');          

        return back()->with('success', 'Peminjaman berhasil disetujui.');
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

    public function processReturn(Request $request, $id) { 
        $loan = Loan::findOrFail($id); 

        if (!in_array($loan->status, ['disetujui', 'dikembalikan'])) {
            return back()->with('error', 'Status peminjaman tidak valid untuk pengembalian.');
        }

        if ($loan->status === 'dikembalikan') {
            $request->validate([
                'return_photo' => 'required|image|max:2048',
            ]);
        } elseif ($request->hasFile('return_photo')) {
            $request->validate([
                'return_photo' => 'image|max:2048',
            ]);
        }

        $data = [
            'status' => 'kembali', 
            'tanggal_kembali_aktual' => now(),
            'petugas_id' => Auth::id(), 
        ];

        if ($request->hasFile('return_photo')) {
            $data['return_photo_path'] = $request->file('return_photo')->store('return_photos', 'public');
        }

        $loan->update($data); 
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

        return back()->with('success', 'Pengembalian berhasil diproses.'); 
    } 

    public function report(Request $request) {  
        $loans = Loan::with(['user', 'tool'])->orderBy('created_at', 'desc')->get(); 
        return view('petugas.laporan', compact('loans')); 
    } 
}