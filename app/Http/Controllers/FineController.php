<?php

namespace App\Http\Controllers;

use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Notification;
use App\Models\Fine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FineController extends Controller
{
    /**
     * Untuk admin/petugas - daftar denda
     */
    public function index(Request $request)
    {
        $fines = Fine::with(['loan.user', 'loan.tool'])
                     ->orderBy('created_at', 'desc')
                     ->paginate(10);
        
        $routePrefix = 'admin';
        
        return view('admin.fines.index', compact('fines', 'routePrefix'));
    }

    /**
     * Menampilkan detail denda
     */
    public function show($id)
    {
        $fine = Fine::with(['loan.user', 'loan.tool'])->findOrFail($id);
        return view('admin.fines.show', compact('fine'));
    }

    /**
     * API: Create payment untuk AJAX
     */
    public function createPayment(Request $request)
    {
        try {
            $fine = Fine::with('loan.user')->findOrFail($request->fine_id);
            
            Log::info('Create payment untuk fine ID: ' . $fine->id . ', amount: ' . $fine->amount);
            
            // Cek apakah denda sudah dibayar
            if ($fine->status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Denda sudah dibayar sebelumnya.'
                ], 400);
            }
            
            // Konfigurasi Midtrans
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production', false);
            Config::$isSanitized = true;
            Config::$is3ds = true;
            
            // Generate order ID unik
            $orderId = 'FINE-' . $fine->id . '-' . time();
            
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $fine->amount,
                ],
                'customer_details' => [
                    'first_name' => $fine->loan->user->name ?? 'Customer',
                    'email' => $fine->loan->user->email ?? 'customer@example.com',
                ],
                'item_details' => [
                    [
                        'id' => (string) $fine->id,
                        'price' => (int) $fine->amount,
                        'quantity' => 1,
                        'name' => 'Denda Keterlambatan'
                    ]
                ]
            ];
            
            $snapToken = Snap::getSnapToken($params);
            
            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId
            ]);
            
        } catch (\Exception $e) {
            Log::error('Create payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Update status setelah pembayaran sukses (dipanggil dari JavaScript)
     * MENGGUNAKAN KOLOM YANG ADA: status, payment_date
     */
    public function updateStatusAfterPayment($fineId, Request $request)
    {
        Log::info('=== UPDATE STATUS DIPANGGIL ===');
        Log::info('Fine ID: ' . $fineId);
        Log::info('Request data: ' . json_encode($request->all()));
        
        try {
            DB::beginTransaction();
            
            $fine = Fine::findOrFail($fineId);
            Log::info('Fine ditemukan, status saat ini: ' . $fine->status);
            
            // Cek apakah sudah dibayar sebelumnya
            if ($fine->status === 'paid') {
                DB::rollBack();
                return response()->json([
                    'success' => true,
                    'message' => 'Denda sudah dibayar sebelumnya',
                    'already_paid' => true
                ]);
            }
            
            // ✅ UPDATE STATUS MENGGUNAKAN KOLOM YANG ADA
            $fine->status = 'paid';
            $fine->payment_date = now(); // Menggunakan kolom payment_date yang ada
            
            $fine->save();
            
            DB::commit();
            
            Log::info("✅ Denda ID: {$fineId} BERHASIL dibayar, status menjadi: " . $fine->status);
            
            return response()->json([
                'success' => true,
                'message' => 'Status denda berhasil diupdate menjadi PAID',
                'data' => [
                    'id' => $fine->id,
                    'status' => $fine->status,
                    'payment_date' => $fine->payment_date
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Gagal update status denda: {$e->getMessage()}");
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Cek status pembayaran (untuk polling)
     */
    public function checkPaymentStatus($fineId, Request $request)
    {
        try {
            $fine = Fine::findOrFail($fineId);
            
            Log::info('Cek status untuk fine ID: ' . $fineId . ', status: ' . $fine->status);
            
            return response()->json([
                'success' => true,
                'status' => $fine->status,
                'payment_date' => $fine->payment_date,
                'amount' => $fine->amount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook handler dari Midtrans
     */
    public function handleNotification(Request $request)
    {
        Log::info('=== WEBHOOK DITERIMA ===');
        
        try {
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production', false);
            
            $notification = new Notification();
            
            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            
            Log::info("Webhook: order_id={$orderId}, status={$transactionStatus}");
            
            // Ekstrak fine_id dari order_id (pattern: FINE-{id}-{timestamp})
            $parts = explode('-', $orderId);
            if (count($parts) >= 2 && $parts[0] === 'FINE') {
                $fineId = $parts[1];
                $fine = Fine::find($fineId);
                
                if ($fine && ($transactionStatus == 'capture' || $transactionStatus == 'settlement')) {
                    if ($fine->status != 'paid') {
                        $fine->status = 'paid';
                        $fine->payment_date = now();
                        $fine->save();
                        
                        Log::info("✅ Webhook: Denda {$fine->id} berhasil dibayar");
                    }
                }
            }
            
            return response()->json(['status' => 'ok']);
            
        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }
    
    /**
     * Confirm payment (alternatif)
     */
    public function confirmPayment(Request $request)
    {
        try {
            $fine = Fine::findOrFail($request->fine_id);
            
            if ($fine->status === 'paid') {
                return response()->json(['success' => true, 'message' => 'Sudah dibayar']);
            }
            
            $fine->status = 'paid';
            $fine->payment_date = now();
            $fine->save();
            
            Log::info("Confirm payment: Denda {$fine->id} berhasil diupdate");
            
            return response()->json(['success' => true, 'message' => 'Pembayaran dikonfirmasi']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * UPDATE STATUS MANUAL (untuk testing)
     */
    public function manualUpdateStatus($fineId)
    {
        try {
            $fine = Fine::findOrFail($fineId);
            $fine->status = 'paid';
            $fine->payment_date = now();
            $fine->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diupdate manual',
                'data' => $fine
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}