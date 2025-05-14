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

    public function qris(Request $request)
    {
        $order_id = $request->query('order_id');
        if (!$order_id) {
            return redirect()->route('user.home')->with('error', 'Order ID tidak ditemukan.');
        }

        $order = Order::findOrFail($order_id);
        $payment = $order->payments;

        if (!$payment || !$payment->snap_token) {
            return redirect()->route('user.home')->with('error', 'Pembayaran tidak ditemukan.');
        }


        $qrString = $payment->qr_string;
        $qrImage = null;

        if ($qrString) {
            $qrImage = 'data:image/png;base64,' . base64_encode($qrString);
        }

        $qrImage = $payment->qr_url;

        return view('payment_qris', compact('order', 'payment', 'qrString', 'qrImage'));
    }

    public function midtransCallback(Request $request, MidtransService $midtransService)
    {
        // Log payload untuk debug jika callback tidak masuk
        Log::info('Midtrans Callback Payload:', $request->all());

        try {
            if ($midtransService->isSignatureKeyVerified()) {
                $order = $midtransService->getOrder();

                if ($midtransService->getStatus() == 'success') {
                    $order->update([
                        'status' => 'finished',
                        'payment_status' => 'PAID',
                    ]);

                    $lastPayment = $order->payments()->latest()->first();
                    $lastPayment->update([
                        'status' => 'PAID',
                        'paid_at' => now(),
                    ]);
                }

                if ($midtransService->getStatus() == 'pending') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Status still pending',
                    ], 201);
                }

                if ($midtransService->getStatus() == 'expire') {
                    // lakukan sesuatu jika pembayaran expired, seperti mengirim notifikasi ke customer
                    // bahwa pembayaran expired dan harap melakukan pembayaran ulang
                    $order->update([
                        'status' => 'canceled',
                        'payment_status' => 'canceled',
                    ]);

                    $lastPayment = $order->payments()->latest()->first();
                    $lastPayment->update([
                        'status' => 'EXPIRED',
                    ]);
                }

                if ($midtransService->getStatus() == 'cancel') {
                    // lakukan sesuatu jika pembayaran dibatalkan
                    $order->update([
                        'status' => 'canceled',
                        'payment_status' => 'canceled',
                    ]);

                    $lastPayment = $order->payments()->latest()->first();
                    $lastPayment->update([
                        'status' => 'CANCELED',
                    ]);
                }

                if ($midtransService->getStatus() == 'failed') {
                    // lakukan sesuatu jika pembayaran gagal
                    $order->update([
                        'status' => 'canceled',
                        'payment_status' => 'canceled',
                    ]);

                    $lastPayment = $order->payments()->latest()->first();
                    $lastPayment->update([
                        'status' => 'FAILED',
                    ]);
                }

                return response()
                    ->json([
                        'success' => true,
                        'message' => 'Notifikasi berhasil diproses',
                    ], 200);
            } else {
                Log::warning('Midtrans signature verification failed', $request->all());
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
