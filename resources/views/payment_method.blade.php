@push('css')
    <style>
        .menu-card {
            background-color: #3c3c3c;
            border-radius: 16px;
            padding: 20px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            color: white;
        }

        .menu-card img {
            width: 100%;
            max-width: 300px;
            height: 200px;
            object-fit: contain;
            border-radius: 8px;
        }

        .menu-card h4 {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .menu-card p {
            font-size: 1.25rem;
            font-weight: bold;
            color: #f39c12;
        }

        .quantity-control {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .quantity-control button {
            background-color: #272727;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            font-size: 1.5rem;
            border-radius: 8px;
        }

        .action-buttons {
            display: flex;
            width: 100%;
            margin-top: 20px;
        }

        .action-buttons button {
            width: 50%;
            padding: 1rem;
            border: none;
            font-size: 1.25rem;
            font-weight: bold;
            color: white;
        }

        .btn-cancel {
            background-color: #dc3545;
        }

        .btn-order {
            background-color: #28a745;
        }

        /* Full height for centering the content */
        .full-height {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 1660px;
        }

        img {
            object-fit: contain;
            height: auto;
        }

        .btn-custom-lg {
            font-size: 1.25rem;
            /* Ukuran teks lebih besar */
            padding: 1rem 2rem;
            /* Padding lebih besar untuk tombol */
        }

        .card img {
            max-height: 300px;
            object-fit: contain;
        }

        .card:hover:not(:active) {
            transform: scale(1.05);
            transition: transform 0.2s ease-in-out;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .card:active {
            transform: scale(0.90);
            transition: transform 0.2s ease-in-out;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-order:hover:not(:active) {
            transform: scale(1.05);
            transition: transform 0.2s ease-in-out;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-order:active {
            transform: scale(0.90);
            transition: transform 0.2s ease-in-out;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

<x-user-layout>
    <div class="gap-4 container-fluid vh-100 d-flex justify-content-center align-items-center"
        style="flex-flow: column; position: relative;">

        <div class="p-0 m-0 mt-5 position-absolute" style="top: 0; left: 0; padding: 20px;">
            <a href="{{ route('user.home') }}">
                <img src="{{ asset('logo_dapoer.svg') }}" alt="Logo" class="shadow-lg img-fluid"
                    style="max-width: 470px; height: auto;">
            </a>
        </div>

        <h1 style="font-weight: bold; font-family:Arial, Helvetica; color: black">Pilih Pembayaran</h1>

        <form id="orderForm" action="{{ route('user.order.store') }}" method="POST">
            @csrf
            <input type="hidden" name="payment_method" id="payment_method">
            <input type="hidden" name="order_method" id="order_method">
            <input type="hidden" name="cart" id="cart">
            <input type="hidden" name="total_price" id="total_price">
        </form>

        <button onclick="setPaymentAndSubmit('pay_here')" class="btn-order">
            <div class="text-center shadow shadow-lg card" style="width: 350px; height: 300px; transform: scale(1.1);">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <h5 class="card-title fs-4 fw-bold" style="font-size: 1.8rem;">Pay Here</h5>
                    <img src="{{ asset('pay_here_icon.svg') }}" class="card-img-top" alt="Pay Here"
                        style="max-width: 100%; max-height: 200px; object-fit: contain;">
                </div>
            </div>
        </button>

        <h2 style="font-family:Arial, Helvetica; color: gray;margin-top: 1%">Atau</h1>

            <button class="rounded btn btn-dark fs-3 btn-order" onclick="setPaymentAndSubmit('pay_in_casheer')"
                style="background: #2D9CAD; color: #FEF8F8; height: 60px;width: 40%">
                Bayar di Kasir
            </button>
    </div>
    <script>
        window.onload = function() {
            // Retrieve values from localStorage
            var paymentMethod = localStorage.getItem('payment_method');
            var orderMethod = localStorage.getItem('order_method');
            var cart = localStorage.getItem('cart');
            var totalPrice = 0;

            // If cart exists in localStorage, calculate total price and set hidden inputs
            if (cart) {
                // Parse the cart from JSON
                var cartItems = JSON.parse(cart);

                // Calculate total price by summing the price * quantity for each item
                cartItems.forEach(function(item) {
                    totalPrice += item.price * item
                    .quantity; // Assuming each item has a 'price' and 'quantity' property
                });

                // Set the total price in the hidden input field
                document.getElementById('total_price').value = totalPrice;

                // Set the cart as a string in the hidden input field
                document.getElementById('cart').value = JSON.stringify(cartItems);
            }

            // Set the payment and order method values from localStorage if they exist
            if (paymentMethod) {
                document.getElementById('payment_method').value = paymentMethod;
            }
            if (orderMethod) {
                document.getElementById('order_method').value = orderMethod;
            }
        };

        function setPaymentAndSubmit(method) {
            // Store the payment method in localStorage

            document.getElementById('payment_method').value = method;

            // Submit the form
            document.getElementById('orderForm').submit();
        }
        // Function to store order method in localStorage
        function setPaymentMethod(method) {
            localStorage.setItem('payment', method);
        }
    </script>
</x-user-layout>

{{-- <x-user-layout>
    <div class="full-height">
        <!-- Logo di kiri (1 bagian) -->
        <div class="logo-container">
            <img src="{{ asset('logo_dapoer.svg') }}" alt="Logo" class="shadow-lg img-fluid"
                style="max-width: 470px; height: auto;">
        </div>

        <!-- Kartu di kanan (4 bagian) -->
        <div>
            <h1 class="text-center" style="font-weight: bold; font-family:Arial, Helvetica; color: black">Pilih Pembayaran</h1>

            <div class="card-container justify-content-center">
            <!-- Kartu Bawa Pulang -->
            <a href="{{ route("user.home") }}" class="text-decoration-none" onclick="setOrderMethod('takeaway')">
                <div class="text-center shadow shadow-lg card">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Bawa Pulang</h5>
                        <img src="{{ asset('takeaway.svg') }}" class="card-img-top" alt="Bawa Pulang">
                    </div>
                </div>
            </a>
            </div>

        </div>
    </div>
    <script>
        // Function to store order method in localStorage
        function setOrderMethod(method) {
            localStorage.setItem('order_method', method);
        }
    </script>
</x-user-layout> --}}
