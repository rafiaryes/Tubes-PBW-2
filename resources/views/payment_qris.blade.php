<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QRIS Payment #{{ $order->id }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div class="container pt-5 pb-5">
    <div class="row">
        <div class="col-12 col-md-8">
            <div class="mb-3 shadow card">
                <div class="card-header">
                    <h5>Data Order</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-condensed">
                        <tr>
                            <td>ID</td>
                            <td><b>#{{ $order->id }}</b></td>
                        </tr>
                        <tr>
                            <td>Total Harga</td>
                            <td><b>Rp {{ number_format($order->total_price, 2, ',', '.') }}</b></td>
                        </tr>
                        <tr>
                            <td>Status Order</td>
                            <td><b>{{ $order->status }}</b></td>
                        </tr>
                        <tr>
                            <td>Status Pembayaran</td>
                            <td><b>{{ $payment->status }}</b></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td><b>{{ $order->created_at->format('d M Y H:i') }}</b></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="shadow card">
                <div class="p-0 card-header">
                    <button class="px-4 py-3 btn btn-link w-100 text-start" type="button" data-bs-toggle="collapse" data-bs-target="#orderDetailAccordion" aria-expanded="false" aria-controls="orderDetailAccordion" style="text-decoration:none;">
                        <h6 class="mb-0">Detail Pesanan <span class="float-end"><i class="bi bi-chevron-down"></i></span></h6>
                    </button>
                </div>
                <div id="orderDetailAccordion" class="collapse">
                    <div class="card-body">
                        <table class="table mb-0 table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $item->menu->nama ?? '-' }}</td>                                        
                                        <td>Rp {{ number_format($item->menu->price, 0, ',', '.') }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>Rp {{ number_format($item->menu->price * $item->quantity, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="shadow card">
                <div class="card-header">
                    <h5>Pembayaran QRIS</h5>
                </div>
                <div class="text-center card-body">
                    @if ($order->status == 'finished' && $payment->status == 'PAID')
                        <div class="mb-3">
                            <svg width="120" height="120" fill="none" viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="60" fill="#28a745" opacity="0.15"/>
                                <circle cx="60" cy="60" r="50" fill="#28a745"/>
                                <path d="M40 62l14 14 26-26" stroke="#fff" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="alert alert-success fw-bold fs-5">
                            Pembayaran Berhasil!
                        </div>
                        <div class="mt-2">
                            <span>Terima kasih, pembayaran Anda telah diterima.</span>
                        </div>
                        <div class="gap-2 mt-4 d-flex justify-content-center">
                            <button class="btn btn-secondary" onclick="window.location.href='{{ route('user.history-orders') }}'">Lihat Riwayat</button>
                        </div>
                    @else
                        @if ($qrImage)
                            <img src="{{ $qrImage }}" alt="QRIS" class="mb-3 img-fluid" style="max-width: 250px;">
                        @elseif ($qrString)
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($qrString) }}" alt="QRIS" class="mb-3 img-fluid">
                        @else
                            <div class="alert alert-danger">QRIS tidak tersedia.</div>
                        @endif
                        <div class="mt-2">
                            <span>Scan QRIS di atas untuk membayar</span>
                        </div>
                        <div class="gap-2 mt-4 d-flex justify-content-center" id="btn-payment">
                            <button class="btn btn-secondary" onclick="window.history.back()">Kembali</button>
                            <button class="btn btn-primary" id="done-button">Check Status Pembayaran</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('done-button').addEventListener('click', function() {
        // AJAX cek status pembayaran
        // disable button
        this.disabled = true;
        this.innerHTML = '<i class="bi bi-arrow-clockwise spinner-border spinner-border-sm" role="status"></i> Memeriksa...';
        this.classList.add('disabled');
        fetch('/api/payment-status?order_id={{ urlencode($order->id) }}')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'paid' || data.status === 'pick_up' || data.status === 'finished') {
                    Swal.fire('Pembayaran Berhasil!', 'Pesanan Anda sudah dibayar.', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1200);
                } else if (data.can_continue_payment == false) {
                    Swal.fire('Transaksi Expired', 'Silahkan lakukan transaksi baru.', 'error');
                    document.getElementById('btn-payment').innerHTML = '<button class="btn btn-secondary" onclick="window.location.href=\'{{ route('user.history-orders') }}\'">Lihat Riwayat</button>';
                } else {
                    Swal.fire('Belum Lunas', 'Status pembayaran: ' + data.status_text, 'info');
                }
            })
            .catch(() => {
                Swal.fire('Gagal', 'Tidak dapat memeriksa status pembayaran.', 'error');
            })
            .finally(() => {
                // enable button
                this.disabled = false;
                this.innerHTML = 'Check Status Pembayaran';
                this.classList.remove('disabled');
            });
    });
</script>
</body>
</html>
