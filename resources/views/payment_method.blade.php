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

        <h1 style="font-weight: bold; font-family:Arial, Helvetica; color: black">Form Pemesanan</h1>

        <form id="orderForm" action="{{ route('user.order.store') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" id="user_id">

            <div class="form-group">
                <label for="name">Nama</label>
                <input name="name" class="form-control" required value="{{ old('name') }}">
                <span class="invalid-feedback d-block" id="error-name"></span>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input name="email" class="form-control" required value="{{ old('email') }}">
                <span class="invalid-feedback d-block" id="error-email"></span>
            </div>

            <div class="form-group">
                <label for="nophone">No Handphone</label>
                <input name="nophone" class="form-control" required value="{{ old('nophone') }}">
                <span class="invalid-feedback d-block" id="error-nophone"></span>
            </div>

            <label for="order_method">Metode pemesanan</label>
            <div class="form-group">
                <input type="radio" id="dine_in" value="dine_in" name="order_method">
                <label for="dine_in">Makan Disini</label><br>
                <input type="radio" id="takeaway" value="takeaway" name="order_method">
                <label for="takeaway">Bawa Pulang</label><br>
                <span class="invalid-feedback d-block" id="error-order_method"></span>
            </div>

            {{-- <label for="payment_method">Metode Pembayaran</label> --}}
            <div class="form-group" style="display: none">
                <input type="radio" id="pay_in_casheer" value="pay_in_casheer" name="payment_method" checked>
                <label for="pay_in_casheer">Bayar di kasir</label><br>
                {{-- <input type="radio" id="pay_online" value="pay_online" name="payment_method">
                <label for="pay_online">Bayar Online</label><br> --}}
                <span class="invalid-feedback d-block" id="error-payment_method"></span>
            </div>
        </form>

        <button class="rounded btn btn-dark fs-3 btn-order" id="submitOrderBtn"
            style="background: #2D9CAD; color: #FEF8F8; height: 60px; width: 25%">
            Bayar
        </button>
    </div>
    <script>
        window.onload = function() {
            var paymentMethod = localStorage.getItem('payment_method');
            var orderMethod = localStorage.getItem('order_method');
            var userId = localStorage.getItem('userUid')

            if (userId) {
                document.getElementById('user_id').value = userId;
            } else {
                Swal.fire({
                    icon: 'success',
                    title: "Mohon refresh page",
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                    timerProgressBar: true,
                    timer: 3000
                }).then(() => {
                    window.location.href = "{{ route('user.home') }}";
                });
                return;
            }
            if (orderMethod) {
                let radio = document.querySelector(`input[name="order_method"][value="${orderMethod}"]`);
                if (radio) radio.checked = true;
            }
            if (paymentMethod) {
                let radio = document.querySelector(`input[name="payment_method"][value="${paymentMethod}"]`);
                if (radio) radio.checked = true;
            }
        };

        // AJAX submit
        document.getElementById('submitOrderBtn').addEventListener('click', function(e) {
            e.preventDefault();

            // Clear previous errors
            ['name', 'email', 'nophone', 'order_method', 'payment_method'].forEach(function(field) {
                document.getElementById('error-' + field).innerText = '';
                let input = document.querySelector('[name="' + field + '"]');
                if (input) input.classList.remove('is-invalid');
            });

            let form = document.getElementById('orderForm');
            let formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async response => {
                    let data = await response.json();
                    if (!response.ok) throw data;
                    return data;
                })
                .then(data => {                    
                    Swal.fire({
                        icon: 'success',
                        title: data.message || 'Order berhasil!',
                        timerProgressBar: true,
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        // result.isConfirmed akan true jika tombol OK ditekan
                        if (result.isConfirmed) {
                            if (data.data && data.data.redirect_url) {
                                window.location.href = data.data.redirect_url;
                            } else {
                                window.location.href = "{{ route('user.home') }}";
                            }
                        }
                    });
                    return;
                })
                .catch(error => {
                    if (error && error.errors) {
                        // Validation error
                        Object.keys(error.errors).forEach(function(field) {
                            let el = document.querySelector('[name="' + field + '"]');
                            if (el) el.classList.add('is-invalid');
                            let errEl = document.getElementById('error-' + field);
                            if (errEl) errEl.innerText = error.errors[field][0];
                        });
                        Swal.fire({
                            icon: 'error',
                            title: error.message || 'Validasi gagal',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: error && error.message ? error.message : 'Gagal membuat order',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
        });
    </script>
    {{-- <script>
        window.onload = function() {
            var paymentMethod = localStorage.getItem('payment_method');
            var orderMethod = localStorage.getItem('order_method');
            var userId = localStorage.getItem('userUid')

            // Set the payment and order method values from localStorage if they exist
            if (userId) {
                document.getElementById('user_id').value = userId;
            } else {
                Swal.fire({
                    icon: 'success',
                    title: "Mohon refresh page",
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                    timerProgressBar: true,
                    timer: 1300
                }).then(() => {
                    window.location.href = "{{ route('user.home') }}";
                });
                return;
            }
            if (orderMethod) {
                document.querySelector(`input[name="order_method"][value="${orderMethod}"]`).checked = true;
            }

            // Select the corresponding radio button for payment method
            if (paymentMethod) {
                document.querySelector(`input[name="payment_method"][value="${paymentMethod}"]`).checked = true;
            }
        };

        function submitOrderForm() {
            // Submit the form
            document.getElementById('orderForm').submit();
        }
    </script> --}}
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
