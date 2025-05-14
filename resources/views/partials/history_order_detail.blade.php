{{-- Render order detail for modal --}}
<h3>Order #{{ $order->id }}</h3>
<p><strong>Status:</strong>
    <span class="badge badge-status
        @if($order->status=='waiting_for_payment') bg-warning text-dark      
        @elseif($order->status=='finished') bg-success text-white
        @elseif($order->status=='canceled') bg-danger text-white
        @else bg-secondary text-white @endif
    ">
        {{ ucwords(str_replace('_',' ',$order->status)) }}
    </span>
</p>
<p><strong>Date:</strong> {{ $order->created_at ? $order->created_at->format('d M Y H:i') : '' }}</p>
<table class="table">
    <thead>
        <tr>
            <th>Menu Item</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
            <tr>
                <td>{{ $item->menu->nama ?? '-' }}</td>
                <td>Rp {{ number_format($item->price,0,',','.') }}</td>
                <td>{{ $item->quantity }}</td>
                <td>Rp {{ number_format($item->price * $item->quantity,0,',','.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<p><strong>Total Price:</strong> Rp {{ number_format($order->items->sum(fn($item) => $item->price * $item->quantity),0,',','.') }}</p>
