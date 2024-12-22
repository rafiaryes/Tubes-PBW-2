@push('css')
    <style>
        /* Full height for centering the content */
        .full-height {
            display: grid;
            grid-template-columns: 1fr 4fr;
            /* Pembagian kolom 1 di kiri dan 4 di kanan */
            min-height: 100vh;
            align-items: center;
            gap: 4rem
        }

        /* Logo */
        .logo-container {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-bottom: 90%;
        }

        /* Kartu container */
        .card-container {
            display: flex;
            justify-content: start;
            gap: 20px;
        }

        .card {
            width: 300px;
            height: 370px;
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
    </style>
@endpush

<x-user-layout>
    <div class="full-height">
        <!-- Logo di kiri (1 bagian) -->
        <div class="logo-container">
            <img src="{{ asset('Logo_Dapoer.jpg') }}" alt="Logo" class="shadow-lg img-fluid"
                style="max-width: 350px; height: auto;">
        </div>

        <!-- Kartu di kanan (4 bagian) -->
        <div class="card-container">
            <!-- Kartu Makan di Sini -->
            <a href="{{ route("user.home") }}" class="text-decoration-none" onclick="setOrderMethod('dine_in')">
                <div class="text-center shadow shadow-lg card">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Makan di Sini</h5>
                        <img src="{{ asset('dinein.svg') }}" class="card-img-top" alt="Makan di Sini">
                    </div>
                </div>
            </a>

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
    <script>
        // Function to store order method in localStorage
        function setOrderMethod(method) {
            localStorage.setItem('order_method', method);
        }
    </script>
</x-user-layout>
