<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function midtrans($order_id)
    {
        // Ambil data order berdasarkan ID
        $order = Order::findOrFail($order_id);

        // Ambil token pembayaran (snap_token) dari data pembayaran
        $payment = $order->payments; // Asumsi ada relasi payment di model Order

        // Jika pembayaran token kosong, tampilkan pesan error atau redirect
        if (!$payment || !$payment->snap_token) {
            return redirect()->route('user.home')->with('error', 'Pembayaran tidak ditemukan.');
        }


        // Kirim token pembayaran dan informasi order ke view
        return view('payment', compact('payment', 'order'));
    }

    public function midtransCallback(Request $request, MidtransService $midtransService)
    {
        try{
            if ($midtransService->isSignatureKeyVerified()) {
                $order = $midtransService->getOrder();

                if ($midtransService->getStatus() == 'success') {
                    $order->update([
                        'status' => 'pick_up',
                        'payment_status' => 'paid',
                    ]);

                    $lastPayment = $order->payments()->latest()->first();
                    $lastPayment->update([
                        'status' => 'PAID',
                        'paid_at' => now(),
                    ]);
                }

                if ($midtransService->getStatus() == 'pending') {
                    response()
                    ->json([
                        'success' => true,
                        'message' => 'Status still pending',
                    ], 201);
                }

                if ($midtransService->getStatus() == 'expire') {
                    // lakukan sesuatu jika pembayaran expired, seperti mengirim notifikasi ke customer
                    // bahwa pembayaran expired dan harap melakukan pembayaran ulang
                }

                if ($midtransService->getStatus() == 'cancel') {
                    // lakukan sesuatu jika pembayaran dibatalkan
                }

                if ($midtransService->getStatus() == 'failed') {
                    // lakukan sesuatu jika pembayaran gagal
                }

                return response()
                    ->json([
                        'success' => true,
                        'message' => 'Notifikasi berhasil diproses',
                    ], 200);
            } else {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401);
            }
        } catch(Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
