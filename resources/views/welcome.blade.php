@push('css')
    <style>
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

<x-user-layout>
    <div class="gap-4 d-flex flex-column justify-content-center align-items-center full-height">
        <div class="text-center w-100">
            <!-- Logo at the top -->
            <img src="{{ asset('Logo_Dapoer.jpg') }}" alt="Logo" class="mb-4 shadow-lg img-fluid"
                style="max-width: 500px; width: 80%; height: auto;">
        </div>
        <a href="{{ route("user.order_method") }}" class="d-inline-block">
            <button class="shadow-lg btn btn-light btn-custom-lg" style="font-weight: bold; border: 2px solid #FEF8F8;">
                Mulai Pesanan
            </button>
        </a>
    </div>
</x-user-layout>
