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

        .full-height {
            display: grid;
            grid-template-columns: 1fr 4fr;
            /* Pembagian kolom 1 di kiri dan 4 di kanan */
            min-height: 100vh;
            /* align-items: center; */
            gap: 4rem
        }

        .logo-container {
            margin-top: 20%;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];

            function updateCartDisplay() {
                $('#cart-container').empty(); // Clear the container
                let totalPrice = 0;

                if (cart.length === 0) {
                    $('#cart-container').append(`
                    <div class="py-4 text-center col-12">
                        <p>No items in the cart.</p>
                    </div>
                `);
                    $('#add-btn').prop('disabled', true); // Disable the button
                    return;
                }

                cart.forEach((item, index) => {
                    const image = "{{ asset('') }}" + "storage/" + `${item.image}`;
                    totalPrice += item.totalPrice;

                    $('#cart-container').append(`
                    <div class="flex-wrap mb-4 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #ddd;">
                        <!-- Delete Button -->
                        <div class="col-2">
                            <button class="btn btn-danger w-100" onclick="removeItem(${index})">Hapus</button>
                        </div>

                        <!-- Image and Name -->
                        <div class="col-4 d-flex align-items-center">
                            <img src="${image}" alt="Menu Image" class="shadow-lg img-fluid" style="max-width: 80px; height: auto;">
                            <p class="fw-bold ms-3 text-truncate">${item.name || 'Nama Menu'}</p>
                        </div>

                        <!-- Quantity Controls -->
                        <div class="col-4 d-flex align-items-center justify-content-center">
                            <button class="shadow-lg btn btn-dark fs-5 rounded-0" style="width: 3rem;background: #FEF8F8;color:black;" onclick="updateQuantity(${index}, -1)">-</button>
                            <input type="text" class="mx-2 text-center shadow-lg form-control btn-dark fs-5 rounded-0"
                                   readonly style="width: 50px;background: #FEF8F8;color:black;" value="${item.quantity}">
                            <button class="shadow-lg btn btn-dark fs-5 rounded-0" style="width: 3rem;background: #FEF8F8;color:black;" onclick="updateQuantity(${index}, 1)">+</button>
                        </div>

                        <!-- Total Price -->
                        <div class="col-2 d-flex justify-content-end">
                            <p class="text-muted fs-5">Rp. ${new Intl.NumberFormat('id-ID').format(item.totalPrice)}</p>
                        </div>
                    </div>
                `);
                });

                $('#add-btn').prop('disabled', false);
                // Update the total price display
                $('#total-price').text("Rp. " + new Intl.NumberFormat('id-ID').format(totalPrice));
            }

            // Functions for removing and updating items
            window.removeItem = function(index) {
                cart.splice(index, 1);
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartDisplay();
            };

            window.updateQuantity = function(index, change) {
                let item = cart[index];
                item.quantity = Math.max(1, item.quantity + change);
                item.totalPrice = item.quantity * item.price;
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartDisplay();
            };

            updateCartDisplay();
        });

        $(document).ready(function() {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];

            if (cart.length === 0) {
                // Disable the button if the cart is empty
                $('#add-btn').prop('disabled', true);
            } else {
                // Set action on button click if cart has items
                $('#add-btn').on('click', function() {
                    window.location.href =
                    "{{ route('user.payment_method') }}"; // Redirect to payment method route
                });
            }
        });
    </script>
@endpush

<x-user-layout>
    <div class="full-height">
        <!-- Logo di kiri (1 bagian) -->
        <div class="logo-container">
            <a href="{{ route('user.home') }}">
                <img src="{{ asset('logo_dapoer.svg') }}" alt="Logo" class="shadow-lg img-fluid"
                    style="max-width: 470px; height: auto;">
            </a>
        </div>

        <div class="d-flex flex-column" style="margin-bottom: 10%">
            <!-- Kontainer utama -->
            <div id="cart-container" class="w-100 d-flex flex-column"
                style="max-width: 59rem; overflow-y: auto; height: 70%; margin-top: 10%">
                <!-- Item cart akan diisi menggunakan JavaScript -->
            </div>

            <!-- Total Harga -->
            <div class="w-100 d-flex justify-content-between align-items-center"
                style="max-width: 59rem; padding-top: 1rem; padding-bottom: 1rem; border-top: 1px solid #ddd;">
                <p class="fw-bold fs-4">Total Harga</p>
                <p class="text-muted fs-4" id="total-price">Rp. 0</p>
            </div>

            <!-- Tombol Batal dan Selesaikan Pesanan -->
            <div class="row w-100 align-items-center justify-content-center"
                style="max-width: 64rem; margin-top: 1rem;">
                <!-- Tombol Batal -->
                <div class="p-0 col-4">
                    <a href="{{ route('user.home') }}">
                        <button id="cancel-btn" class="shadow-lg btn btn-dark w-100 fs-3 rounded-0"
                            style="background: #FEF8F8; color: black">Tambah Pesanan</button>
                    </a>
                </div>

                <!-- Tombol Tambah Pesanan -->
                <div class="p-0 col-4">
                    <button id="add-btn" class="shadow-lg btn btn-dark w-100 fs-3 rounded-0"
                            style="background: #2D9CAD; color: #FEF8F8">Selesaikan pesanan</button>
                </div>
            </div>
        </div>
    </div>
</x-user-layout>
