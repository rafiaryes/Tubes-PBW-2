<?php

namespace App\Services;

use App\Models\Order;
use Exception;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;
use Midtrans\CoreApi;

class MidtransService
{
    protected string $serverKey;
    protected string $isProduction;
    protected string $isSanitized;
    protected string $is3ds;

    /**
     * MidtransService constructor.
     *
     * Menyiapkan konfigurasi Midtrans berdasarkan pengaturan yang ada di file konfigurasi.
     */
    public function __construct()
    {
        // Konfigurasi server key, environment, dan lainnya
        $this->serverKey = config('midtrans.server_key');
        $this->isProduction = config('midtrans.is_production');
        $this->isSanitized = config('midtrans.is_sanitized');
        $this->is3ds = config('midtrans.is_3ds');

        // Mengatur konfigurasi global Midtrans
        Config::$serverKey = $this->serverKey;
        Config::$isProduction = $this->isProduction;
        Config::$isSanitized = $this->isSanitized;
        Config::$is3ds = $this->is3ds;
    }

    /**
     * Membuat snap token untuk transaksi berdasarkan data order.
     *
     * @param Order $order Objek order yang berisi informasi transaksi.
     *
     * @return string Snap token yang dapat digunakan di front-end untuk proses pembayaran.
     * @throws Exception Jika terjadi kesalahan saat menghasilkan snap token.
     */
    public function createSnapToken(Order $order): string
    {
        // dd(round($order->items->sum(fn($item) => $item->quantity * $item->menu->price)));
        // data transaksi
        $params = [
            'transaction_details' => [
                'order_id' => $order->id,
                'gross_amount' =>(int) round($order->items->sum(fn($item) => $item->quantity * $item->menu->price)),
            ],
            'item_details' => $this->mapItemsToDetails($order),
            'customer_details' => $this->getCustomerDetails($order),
        ];

        try {
            // Membuat snap token
            return Snap::getSnapToken($params);
        } catch (Exception $e) {
            // Menangani error jika gagal mendapatkan snap token
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Membuat QRIS payment menggunakan Core API.
     * @param Order $order
     * @return array
     * @throws Exception
     */
    public function createQrisPayment(Order $order)
    {
        // Hitung gross_amount dari item_details (bukan dari order->items->sum)
        $itemDetails = $this->mapItemsToDetails($order);
        $grossAmount = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $itemDetails));

        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $order->id,
                'gross_amount' => (int) $grossAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => $this->getCustomerDetails($order),
        ];

        try {
            $response = CoreApi::charge($params);
            return $response;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Memvalidasi apakah signature key yang diterima dari Midtrans sesuai dengan signature key yang dihitung di server.
     *
     * @return bool Status apakah signature key valid atau tidak.
     */
    public function isSignatureKeyVerified(): bool
    {
        $notification = new Notification();

        // Membuat signature key lokal dari data notifikasi
        $localSignatureKey = hash('sha512',
            $notification->order_id . $notification->status_code .
            $notification->gross_amount . $this->serverKey);

        // Memeriksa apakah signature key valid
        return $localSignatureKey === $notification->signature_key;
    }

    /**
     * Mendapatkan data order berdasarkan order_id yang ada di notifikasi Midtrans.
     *
     * @return Order Objek order yang sesuai dengan order_id yang diterima.
     */
    public function getOrder(): Order
    {
        $notification = new Notification();

        // Mengambil data order dari database berdasarkan order_id
        return Order::where('id', $notification->order_id)->first();
    }

    /**
     * Mendapatkan status transaksi berdasarkan status yang diterima dari notifikasi Midtrans.
     *
     * @return string Status transaksi ('success', 'pending', 'expire', 'cancel', 'failed').
     */
    public function getStatus(): string
    {
        $notification = new Notification();
        $transactionStatus = $notification->transaction_status;
        $fraudStatus = $notification->fraud_status;

        return match ($transactionStatus) {
            'capture' => ($fraudStatus == 'accept') ? 'success' : 'pending',
            'settlement' => 'success',
            'deny' => 'failed',
            'cancel' => 'cancel',
            'expire' => 'expire',
            'pending' => 'pending',
            default => 'uhuy',
        };
    }

    /**
     * Memetakan item dalam order menjadi format yang dibutuhkan oleh Midtrans.
     *
     * @param Order $order Objek order yang berisi daftar item.
     * @return array Daftar item yang dipetakan dalam format yang sesuai.
     */
    protected function mapItemsToDetails(Order $order): array
    {
        return $order->items()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'price' => (int) round($item->price),
                'quantity' => $item->quantity,
                'name' => $item->menu->nama
            ];
        })->toArray();
    }

    /**
     * Mendapatkan informasi customer dari order.
     * Data ini dapat diambil dari relasi dengan tabel lain seperti users atau tabel khusus customer.
     *
     * @param Order $order Objek order yang berisi informasi tentang customer.
     * @return array Data customer yang akan dikirim ke Midtrans.
     */
    protected function getCustomerDetails(Order $order): array
    {
        // Sesuaikan data customer dengan informasi yang dimiliki oleh aplikasi Anda
        return [
            'first_name' => $order->name, // Ganti dengan data nyata
            'email' => $order->email, // Ganti dengan data nyata
            'phone' => $order->nophone, // Ganti dengan data nyata
            'no_meja' => $order->no_meja, // Ganti dengan data nyata
        ];
    }
}
