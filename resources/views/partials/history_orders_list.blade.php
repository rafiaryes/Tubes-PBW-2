{{-- Render list of orders as cards --}}
@if($orders->isEmpty())
    <div class="empty-state w-100">Belum ada riwayat pesanan.</div>
@else
    @foreach($orders as $order)
        <div class="col-12 col-md-6">
            <div class="order-card" data-order-id="{{ $order->id }}" data-payment-method="{{ $order->payment_method }}" data-status="{{ $order->status }}">
                <div class="mb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Order #{{ $order->id }}</strong>
                        <div>
                            <span class="badge badge-status 
                                @if($order->status=='waiting_for_payment') bg-warning text-dark
                                @elseif($order->status=='on_progress') bg-info text-white
                                @elseif($order->status=='pick_up') bg-primary text-white
                                @elseif($order->status=='finished') bg-success text-white
                                @elseif($order->status=='canceled') bg-danger text-white
                                @else bg-secondary text-white @endif
                            ">
                                {{ ucwords(str_replace('_',' ',$order->status)) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="text-muted">{{ $order->created_at ? $order->created_at->isoFormat('dddd D MMMM YYYY') : '' }}</span>
                    </div>
                </div>
                <div class="mb-2"><strong>Nama:</strong> {{ $order->name ?? '-' }}</div>
                <div class="mb-2"><strong>Email:</strong> {{ $order->email ?? '-' }}</div>
                <div class="mb-2"><strong>Phone:</strong> {{ $order->nophone ?? '-' }}</div>
                <div><strong>Total:</strong> Rp {{ number_format($order->total_price,0,',','.') }}</div>
                <div class="mt-2" id="continue-payment-btn-{{ $order->id }}"></div>
            </div>
        </div>
    @endforeach
@endif
