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
                    <td>{{ 'Rp ' . number_format($item->price, 2, ",", ".") }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ 'Rp ' . number_format($item->quantity * $item->menu->price, 2, ",", ".") }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p><strong>Total Price:</strong> {{ 'Rp ' . number_format($order->items->sum(fn($item) => $item->quantity * $item->menu->price), 2, ",", ".") }}</p>

        <!-- Update Status Form -->
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

        <!-- Success Message -->
        @if (session('success'))
        <div class="mt-3 alert alert-success">
            {{ session('success') }}
        </div>
        @endif
    </div>
</x-app-layout>
