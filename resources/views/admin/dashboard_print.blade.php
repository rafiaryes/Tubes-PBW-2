<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapan Dashboard</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        .header-title { text-align: center; font-weight: bold; margin-bottom: 24px; font-size: 20px; }
        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
        .main-table th, .main-table td { border: 1px solid #bbb; padding: 8px 10px; vertical-align: top; }
        .main-table th { background: #f3f3f3; }
        .order-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .order-table th, .order-table td { border: 1px solid #aaa; padding: 3px 6px; }
        .order-table th { background: #f7f7f7; }
        .fw-bold { font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bg-total { background: #e6f7e6; }
    </style>
</head>
<body>
    <div class="header-title">
        Rekapan Transaksi
        @if($isBulanan)
            <div>Bulan {{ $bulanNama }} {{ $tahun }}</div>
        @else
            <div>Tahun {{ $tahun }}</div>
        @endif
    </div>

    @if($isAdmin)
        @php
            $grandTotalOrder = 0;
            $grandTotalAmount = 0;
        @endphp
        <table class="main-table">
            <thead>
                <tr>
                    <th>Kasir</th>
                    <th>User</th>
                    <th>List Order</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grouped as $kasirId => $users)
                    @php
                        $kasirTotalOrder = 0;
                        $kasirTotalAmount = 0;
                    @endphp
                    @foreach($users as $userId => $orders)
                        @php
                            $firstOrder = $orders->first();
                            $userTotalOrder = $orders->count();
                            $userTotalAmount = $orders->sum(fn($order) => $order->items->sum(fn($item) => $item->price * $item->quantity));
                            $kasirTotalOrder += $userTotalOrder;
                            $kasirTotalAmount += $userTotalAmount;
                            $grandTotalOrder += $userTotalOrder;
                            $grandTotalAmount += $userTotalAmount;
                        @endphp
                        <tr>
                            <td>
                                {{ optional($firstOrder?->kasir)->name ?? 'Tanpa Kasir' }}<br>
                                <span style="font-size:11px; color:#888;">ID: {{ $kasirId }}</span>
                                <div style="margin-top:8px;">
                                    <span class="fw-bold">Total Order Kasir: {{ $kasirTotalOrder }}</span><br>
                                    <span class="fw-bold">Total Uang Kasir: Rp {{ number_format($kasirTotalAmount, 0, ',', '.') }}</span>
                                </div>
                            </td>
                            <td>
                                <b>{{ $firstOrder?->name ?? '-' }}</b><br>
                                <span style="font-size:11px; color:#888;">User ID: {{ $userId }}</span>
                                <div style="margin-top:8px;">
                                    <span class="fw-bold">Total Order: {{ $userTotalOrder }}</span><br>
                                    <span class="fw-bold">Total Uang: Rp {{ number_format($userTotalAmount, 0, ',', '.') }}</span>
                                </div>
                            </td>
                            <td>
                                <table class="order-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Order</th>
                                            <th>Pemesan</th>
                                            <th>No HP</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th>Detail</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orders as $idx => $order)
                                            <tr>
                                                <td class="text-center">{{ $idx+1 }}</td>
                                                <td>#{{ $order->id }}</td>
                                                <td>{{ $order->name ?? '-' }}</td>
                                                <td>{{ $order->nophone ?? '-' }}</td>
                                                <td>{{ ucfirst($order->status) }}</td>
                                                <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                                <td>
                                                    <table style="width:100%; border-collapse:collapse;">
                                                        <thead>
                                                            <tr>
                                                                <th style="font-size:11px;">Menu</th>
                                                                <th style="font-size:11px;">Harga</th>
                                                                <th style="font-size:11px;">Qty</th>
                                                                <th style="font-size:11px;">Subtotal</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($order->items as $item)
                                                                <tr>
                                                                    <td style="font-size:11px;">{{ $item->menu->nama ?? '-' }}</td>
                                                                    <td style="font-size:11px;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                                                    <td style="font-size:11px; text-align:center;">{{ $item->quantity }}</td>
                                                                    <td style="font-size:11px;">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td class="text-right fw-bold">
                                                    Rp {{ number_format($order->items->sum(fn($item) => $item->price * $item->quantity), 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                    {{-- <tr class="bg-total">
                        <td colspan="2" class="text-right fw-bold">Grand Total Kasir (ID: {{ $kasirId }})</td>
                        <td>
                            <div class="fw-bold">Total Order Kasir: {{ $kasirTotalOrder }}</div>
                            <div class="fw-bold">Total Uang Kasir: Rp {{ number_format($kasirTotalAmount, 0, ',', '.') }}</div>
                        </td>
                    </tr> --}}
                @endforeach
                <tr class="bg-total">
                    <td colspan="2" class="text-right fw-bold">Grand Total Kasir</td>
                    <td>
                        <div class="fw-bold">Total Order : {{ $grandTotalOrder }}</div>
                        <div class="fw-bold">Total Uang : Rp {{ number_format($grandTotalAmount, 0, ',', '.') }}</div>
                    </td>
                </tr>
            </tbody>
        </table>
    @else
        @php
            $grandTotalOrder = 0;
            $grandTotalAmount = 0;
        @endphp
        <table class="main-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>List Order</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $orders = $grouped->first();
                @endphp
                @foreach($orders as $userId => $userOrders)
                    @php
                        $firstOrder = $userOrders->first();
                        $userTotalOrder = $userOrders->count();
                        $userTotalAmount = $userOrders->sum(fn($order) => $order->items->sum(fn($item) => $item->price * $item->quantity));
                        $grandTotalOrder += $userTotalOrder;
                        $grandTotalAmount += $userTotalAmount;
                    @endphp
                    <tr>
                        <td>
                            <b>{{ $firstOrder?->name ?? '-' }}</b><br>
                            <span style="font-size:11px; color:#888;">User ID: {{ $userId }}</span>
                            <div style="margin-top:8px;">
                                <span class="fw-bold">Total Order: {{ $userTotalOrder }}</span><br>
                                <span class="fw-bold">Total Uang: Rp {{ number_format($userTotalAmount, 0, ',', '.') }}</span>
                            </div>
                        </td>
                        <td>
                            <table class="order-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Order</th>
                                        <th>Pemesan</th>
                                        <th>No HP</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Detail</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($userOrders as $idx => $order)
                                        <tr>
                                            <td class="text-center">{{ $idx+1 }}</td>
                                            <td>#{{ $order->id }}</td>
                                            <td>{{ $order->name ?? '-' }}</td>
                                            <td>{{ $order->nophone ?? '-' }}</td>
                                            <td>{{ ucfirst($order->status) }}</td>
                                            <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                            <td>
                                                <table style="width:100%; border-collapse:collapse;">
                                                    <thead>
                                                        <tr>
                                                            <th style="font-size:11px;">Menu</th>
                                                            <th style="font-size:11px;">Harga</th>
                                                            <th style="font-size:11px;">Qty</th>
                                                            <th style="font-size:11px;">Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($order->items as $item)
                                                            <tr>
                                                                <td style="font-size:11px;">{{ $item->menu->nama ?? '-' }}</td>
                                                                <td style="font-size:11px;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                                                <td style="font-size:11px; text-align:center;">{{ $item->quantity }}</td>
                                                                <td style="font-size:11px;">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </td>
                                            <td class="text-right fw-bold">
                                                Rp {{ number_format($order->items->sum(fn($item) => $item->price * $item->quantity), 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    {{-- <tr class="bg-total">
                        <td class="text-right fw-bold">Grand Total User (ID: {{ $userId }})</td>
                        <td>
                            <div class="fw-bold">Total Order: {{ $userTotalOrder }}</div>
                            <div class="fw-bold">Total Uang: Rp {{ number_format($userTotalAmount, 0, ',', '.') }}</div>
                        </td>
                    </tr> --}}
                @endforeach
                <tr class="bg-total">
                    <td class="text-right fw-bold">Grand Total  User</td>
                    <td>
                        <div class="fw-bold">Total Order : {{ $grandTotalOrder }}</div>
                        <div class="fw-bold">Total Uang : Rp {{ number_format($grandTotalAmount, 0, ',', '.') }}</div>
                    </td>
                </tr>
            </tbody>
        </table>
    @endif
</body>
</html>
