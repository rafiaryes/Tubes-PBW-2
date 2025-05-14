<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumbs -->
        <div aria-label="breadcrumb">
            <ol class="breadcrumb">
                @foreach (Breadcrumbs::generate() as $breadcrumb)
                    <li class="breadcrumb-item">
                        @if ($breadcrumb->url)
                            <a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
                        @else
                            {{ $breadcrumb->title }}
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>

        <!-- Order Detail -->
        <h3>Order #{{ $order->id }}</h3>
        <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $order->status)) }}</p>
        <p><strong>User:</strong> {{ $order->name ?? 'Guest' }}</p>
        <p><strong>Date:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>

        <!-- Items -->
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
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->menu->nama }}</td>
                        <td>{{ 'Rp ' . number_format($item->price, 2, ',', '.') }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ 'Rp ' . number_format($item->price * $item->quantity, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p><strong>Total Price:</strong>
            {{ 'Rp ' . number_format($order->items->sum(fn($item) => $item->price * $item->quantity), 2, ',', '.') }}
        </p>
        
        @if ($order->payment_method == 'pay_online')
            <div class="mt-3 alert alert-info">
                <strong>Note:</strong> This order was paid online. Status cannot be updated manually.
            </div>
        @endif

        <!-- Update Status Form -->
        @if ($order->payment_method != 'pay_online')
            <form action="{{ $route }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="status">Update Status</label>
                    <select name="status" id="status" class="form-control" required>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="type" value="{{ $type }}">
                <button type="submit" class="mt-2 btn btn-primary">Update Status</button>
            </form>        
        @elseif ($order->payment_method == 'pay_online' && $order->status == 'finished' && $order->kasir_id == null)
            {{-- konfirmasi pembayaran --}}
            {{-- konfirmasi order  --}}
            <form action="{{ route('update-status-order', $order->id) }}" method="POST">
                @csrf
                {{-- payment method --}}
                <input type="hidden" name="payment_method" value="{{ $order->payment_method }}">
                <button type="submit" class="mt-2 btn btn-success">Konfirmasi Pemesanan</button>
            </form>
        @endif



        <!-- Success Message -->
        @if (session('success'))
            <div class="mt-3 alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    </div>
</x-app-layout>
