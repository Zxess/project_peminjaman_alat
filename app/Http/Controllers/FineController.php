<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fine;
use App\Models\ActivityLog;

class FineController extends Controller
{
    public function index()
    {
        $fines = Fine::with(['loan.user', 'loan.tool'])
                    ->latest()
                    ->paginate(10);

        return view('admin.fines.index', compact('fines'));
    }

    public function show($id)
    {
        $fine = Fine::with(['loan.user', 'loan.tool'])->findOrFail($id);
        return view('admin.fines.show', compact('fine'));
    }

    public function payForm($id)
    {
        $fine = Fine::with(['loan.user', 'loan.tool'])->findOrFail($id);

        if ($fine->status === 'paid') {
            return redirect()->route('admin.fines.index')->with('error', 'Denda sudah dibayar.');
        }

        return view('admin.fines.pay', compact('fine'));
    }

    public function pay(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $fine = Fine::findOrFail($id);

        if ($fine->status === 'paid') {
            return redirect()->route('admin.fines.index')->with('error', 'Denda sudah dibayar.');
        }

        $fine->update([
            'status' => 'paid',
            'payment_date' => now()
        ]);

        ActivityLog::record('Pembayaran Denda', 'Pembayaran denda ID: ' . $fine->id . ' sebesar Rp ' . number_format($fine->amount));

        return redirect()->route('admin.fines.index')->with('success', 'Pembayaran denda berhasil diproses.');
    }
}
