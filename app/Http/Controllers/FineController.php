<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Fine;
use App\Models\ActivityLog;

class FineController extends Controller
{
    protected function routePrefix()
    {
        $routeName = request()->route()->getName() ?? '';
        return Str::startsWith($routeName, 'petugas.') ? 'petugas' : 'admin';
    }

    protected function viewPath($path)
    {
        return 'admin.fines.' . $path;
    }

    protected function routeName($action)
    {
        return $this->routePrefix() . '.fines.' . $action;
    }

    public function index()
    {
        $fines = Fine::with(['loan.user', 'loan.tool'])
                    ->latest()
                    ->paginate(10);

        return view($this->viewPath('index'), compact('fines'))
                    ->with('routePrefix', $this->routePrefix());
    }

    public function show($id)
    {
        $fine = Fine::with(['loan.user', 'loan.tool'])->findOrFail($id);
        return view($this->viewPath('show'), compact('fine'))
                    ->with('routePrefix', $this->routePrefix());
    }

    public function payForm($id)
    {
        $fine = Fine::with(['loan.user', 'loan.tool'])->findOrFail($id);

        if ($fine->status === 'paid') {
            return redirect()->route($this->routeName('index'))->with('error', 'Denda sudah dibayar.');
        }

        return view($this->viewPath('pay'), compact('fine'))
                    ->with('routePrefix', $this->routePrefix());
    }

    public function pay(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $fine = Fine::findOrFail($id);

        if ($fine->status === 'paid') {
            return redirect()->route($this->routeName('index'))->with('error', 'Denda sudah dibayar.');
        }

        $fine->update([
            'status' => 'paid',
            'payment_date' => now()
        ]);

        ActivityLog::record('Pembayaran Denda', 'Pembayaran denda ID: ' . $fine->id . ' sebesar Rp ' . number_format($fine->amount));

        return redirect()->route($this->routeName('index'))->with('success', 'Pembayaran denda berhasil diproses.');
    }
}
