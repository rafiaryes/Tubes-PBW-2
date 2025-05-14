{{-- filepath: /Users/ramadhanabdulaziz/Documents/Freelance/2025/Tubes-PBW-2/resources/views/history_orders.blade.php --}}
@push('css')
<style>
    .order-card {
        background: #f8f9fa;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        margin-bottom: 1.5rem;
        padding: 1.5rem;
        cursor: pointer;
        transition: box-shadow 0.2s;
    }
    .order-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }
    .badge-status {
        font-size: 1rem;
        padding: 0.4em 1em;
        border-radius: 1em;
        font-weight: 600;
    }
    .empty-state {
        text-align: center;
        color: #aaa;
        margin-top: 4rem;
    }
</style>
@endpush

<x-user-layout>
    <div class="py-4 container-fluid">
        <div class="mb-4 row">
            <div class="col d-flex align-items-center">
                <div class="d-flex justify-content-center align-items-center"
                    style="width: 50px; height: 50px; border: 3px solid #EBE5DD; background-color: #2D9CAD; border-radius: 50%;">
                    <a href="{{ route('user.home') }}">
                        <i class="bi bi-arrow-left" style="font-size: 1.7rem; color: white;"></i>
                    </a>
                </div>
                <div class="d-flex justify-content-center align-items-center me-2"
                    style="width: 50px; height: 50px; border: 3px solid #EBE5DD; background-color: #F8BF40; border-radius: 50%;">
                    <a href="{{ route('user.cart') }}">
                        <img src="{{ asset('cart-bag.svg') }}" alt="">
                    </a>
                </div>
                <h2 class="mb-0 ms-3">Riwayat Pesanan</h2>
            </div>
        </div>
        <div id="orders-list" class="row">
            {!! $ordersHtml ?? '' !!}
        </div>
    </div>

    <!-- Modal Detail Order -->
    <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="orderDetailModalLabel">Detail Order</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body" id="order-detail-content">
            <!-- Detail order akan diisi via JS -->
          </div>
        </div>
      </div>
    </div>

    @push('scripts')
    <script>
        function statusBadge(status) {
            let color = {
                waiting_for_payment: 'bg-warning text-dark',
                on_progress: 'bg-info text-white',
                pick_up: 'bg-primary text-white',
                finished: 'bg-success text-white',
                canceled: 'bg-danger text-white'
            }[status] || 'bg-secondary text-white';
            return `<span class="badge badge-status ${color}">${status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</span>`;
        }

        function formatRupiah(num) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
        }

        $(document).ready(function() {
            const userId = localStorage.getItem('userUid');
            if (!userId) {
                window.location.href = "{{ route('user.home') }}";
                return;
            }

            // Ambil data order dari server (HTML sudah di-render blade partial)
            function loadOrders() {
                $.ajax({
                    url: "{{ route('user.get-history-orders') }}",
                    method: "GET",
                    data: { user_id: userId },
                    success: function(res) {
                        $('#orders-list').html(res.html);

                        // Card click event
                        $('.order-card').on('click', function() {
                            const orderId = $(this).data('order-id');
                            $.ajax({
                                url: "{{ route('user.get-history-order-detail') }}",
                                method: "GET",
                                data: { order_id: orderId },
                                success: function(res) {
                                    if (!res.html) return;
                                    $('#order-detail-content').html(res.html);

                                    // Cek status pembayaran untuk tombol lanjutkan pembayaran di modal
                                    // $.get('/api/payment-status', { order_id: orderId }, function(paymentRes) {
                                    //     console.log(paymentRes);
                                    //     if (paymentRes.can_continue_payment) {
                                    //         $('#order-detail-content').append(
                                    //             `<a href="/payment/qris?order_id=${orderId}" class="mt-2 btn btn-success">Lanjutkan Pembayaran</a>`
                                    //         );
                                    //     } else {
                                    //         $('#order-detail-content').append(
                                    //             `<span class="mt-2 badge bg-danger">Pembayaran Expired</span>`
                                    //         );
                                    //     }
                                    // });

                                    $('#orderDetailModal').modal('show');
                                }
                            });
                        });

                        $('.order-card').each(function() {
                            const orderId = $(this).data('order-id');                            
                            const status = $(this).data('status');
                            const paymentMethod = $(this).data('payment-method');
                            if (status === 'waiting_for_payment' && paymentMethod === 'pay_online') {
                                $.get('/api/payment-status', { order_id: orderId }, function(paymentRes) {
                                    if (paymentRes.can_continue_payment) {
                                        document.getElementById(`continue-payment-btn-${orderId}`).innerHTML = `
                                            <a href="/payment/qris?order_id=${orderId}" class="btn btn-success btn-sm">Lanjutkan Pembayaran</a>
                                        `;
                                    }
                                });
                            }
                        });
                    }
                });
            }

            loadOrders();
        });
    </script>
    @endpush
</x-user-layout>
