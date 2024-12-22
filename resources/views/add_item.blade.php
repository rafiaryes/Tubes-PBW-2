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
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            const menuId = {{ $menu->id }};
            const price = {{ $menu->price }};
            const image = "{{ $menu->image }}";
            const menuName = "{{ $menu->nama }}";
            const $quantityInput = $('#quantity-input');
            const $minusBtn = $('#minus-btn');
            const $plusBtn = $('#plus-btn');
            const $addBtn = $('#add-btn');
            const $priceDisplay = $('#price-display');

            let cart = JSON.parse(localStorage.getItem('cart')) || [];

            function updateCart() {
                localStorage.setItem('cart', JSON.stringify(cart));
            }

            function updatePriceDisplay(quantity) {
                const totalPrice = quantity * price;
                $priceDisplay.text(`Rp. ${new Intl.NumberFormat('id-ID').format(totalPrice)}`);
            }

            $minusBtn.on('click', function() {
                let quantity = parseInt($quantityInput.val());
                if (quantity > 1) {
                    quantity--;
                    $quantityInput.val(quantity);
                    updatePriceDisplay(quantity);
                }
            });

            $plusBtn.on('click', function() {
                let quantity = parseInt($quantityInput.val());
                if (quantity < 100) {
                    quantity++;
                    $quantityInput.val(quantity);
                    updatePriceDisplay(quantity);
                }
            });

            $addBtn.on('click', function() {
                const quantity = parseInt($quantityInput.val());
                const data = {
                    _token: "{{ csrf_token() }}",
                    menu_id: menuId,
                    quantity: quantity,
                    user_id: localStorage.getItem('userUid'),
                    order_method: localStorage.getItem('order_method')
                };

                $.ajax({
                    url: "{{ route('user.order.add-item') }}",
                    method: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end',
                            timerProgressBar: true,
                            timer: 1300
                        }).then(() => {
                            window.location.href = "{{ route('user.home') }}";
                        });
                    },
                    error: function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menambahkan ke Keranjang',
                            text: error.responseJSON.message,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end',
                            timerProgressBar: true,
                            timer: 1300
                        });
                    }
                });
            });

        });
    </script>
@endpush

<x-user-layout>
    <div class="gap-4 container-fluid vh-100 d-flex justify-content-center align-items-center"
        style="flex-flow: column; position: relative;">
        <!-- Logo di kiri atas -->
        <div class="p-0 m-0 mt-5 position-absolute" style="top: 0; left: 0; padding: 20px;">
            <a href="{{ route('user.home') }}">
                <img src="{{ asset('logo_dapoer.svg') }}" alt="Logo" class="shadow-lg img-fluid"
                    style="max-width: 470px; height: auto;">
            </a>
        </div>

        <div class="px-5 pt-5 shadow-lg card" style="max-width: 350px; width: 100%; background: #FEF8F8;">
            <!-- Placeholder Gambar -->
            <div class="mb-4 text-center">
                <div class="rounded" style="width: 100%; height: 200px; background: #FEF8F8;">
                    <img src="{{ asset("storage/$menu->image") }}" class="img-fluid" alt="">
                </div>
                <h3 class="mt-3 fw-bold">{{ $menu->nama }}</h3>
                <p id="price-display" class="text-muted fs-5">Rp  {{ number_format($menu->price, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Kontrol Jumlah -->
        <div class=" row w-100 align-items-center justify-content-center" style="padding-left: 30%; padding-right: 30%">
            <!-- Tombol Minus -->
            <div class="p-0 col-2 d-flex justify-content-end">
                <button type="" id="minus-btn" class="shadow-lg btn btn-dark fs-3 rounded-0"
                    style="background: #FEF8F8; color: black; width: 4rem">-</button>
            </div>

            <!-- Input Jumlah -->
            <div class="p-0 col-7 d-flex justify-content-center">
                <input type="text" id="quantity-input"
                    class="text-center shadow-lg form-control btn-dark fs-3 rounded-0" readonly
                    style="width: 100%; background: #FEF8F8; color: black;" value="1">
            </div>

            <!-- Tombol Plus -->
            <div class="p-0 col-2">
                <button id="plus-btn" class="shadow-lg btn btn-dark fs-3 rounded-0"
                    style="background: #FEF8F8; color: black;width: 4rem">+</button>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="row w-100 align-items-center justify-content-center" style="max-width: 46rem">
            <!-- Tombol Batal -->
            <div class="p-0 col-4">
                <a href="{{ route('user.home') }}">
                    <button id="cancel-btn" class="shadow-lg btn btn-dark w-100 fs-3 rounded-0"
                        style="background: #FEF8F8; color: black">Batal</button>
                </a>
            </div>

            <!-- Tombol Tambah Pesanan -->
            <div class="p-0 col-4">
                <button id="add-btn" class="shadow-lg btn btn-dark w-100 fs-3 rounded-0"
                    style="background: #2D9CAD; color: #FEF8F8">Tambah pesanan</button>
            </div>
        </div>
    </div>
</x-user-layout>
