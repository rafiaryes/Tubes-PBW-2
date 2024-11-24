@push('css')
    <style>
        /* Additional styling for the header */
        .header {
            background-color: #343a40;
            color: white;
        }

        .card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }

        .card img {
            object-fit: cover;
            height: 200px;
        }

        .card-body {
            padding: 15px;
        }

        .btn-warning {
            background-color: #F8BF40;
            color: white;
            border: 1px solid #EBE5DD;
        }

        .btn-warning:hover {
            background-color: #e6a900;
        }

        /* Menjaga card tetap responsif */
        @media (max-width: 768px) {
            .card {
                width: 100%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let currentRequest = null; // Variabel untuk menyimpan request AJAX sebelumnya
            let searchQuery = $("#search").val();
            let page = 1;
            const loadMoreLimit = 12;

            // Fungsi untuk memuat data berdasarkan pencarian
            function loadSearchResults(query = '', page = 1, isLoadMore = false) {
                // Jika ada request sebelumnya yang belum selesai, batalkan requestnya
                if (currentRequest) {
                    currentRequest.abort(); // Membatalkan request yang sedang berjalan
                }

                // Membuat AbortController baru untuk request yang akan datang
                const controller = new AbortController();
                const signal = controller.signal;

                // Menampilkan elemen loading
                $('#loading').show();

                // Menyimpan referensi request AJAX saat ini di dalam currentRequest
                currentRequest = $.ajax({
                    url: "{{ route('user.home') }}", // Ganti dengan route yang sesuai
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        search: query, // Kirimkan query pencarian ke server,
                        page: page, // Mengirim halaman ke server
                        limit: loadMoreLimit, // Mengirim jumlah data yang diinginkan
                    },
                    signal: signal, // Menghubungkan signal dari AbortController
                    success: function(response) {
                        // Menyembunyikan elemen loading
                        $('#loading').hide();

                        if (response.html) {
                            if (isLoadMore) {
                                $('#menu-container').append(response.html);
                            } else {
                                $('#menu-container').html(response.html);
                            }
                        } else {
                            $('#menu-container').html('<p>No menus found.</p>');
                        }

                        if (!response.hasMore) {
                            $('#load-more-btn').hide();
                        } else {
                            $('#load-more-btn').show();
                        }
                    },
                    error: function(xhr, status, error) {
                        // Menyembunyikan elemen loading jika terjadi error
                        $('#loading').hide();

                        // Cek jika error disebabkan oleh request yang dibatalkan
                        if (status !== 'abort') {
                            console.error('Error fetching data:', error);
                        }
                    }
                });
            }

            // Event listener untuk input pencarian
            $('#search').on('input', function() {
                page = 1;
                searchQuery = $(this).val();
                loadSearchResults(searchQuery); // Memanggil fungsi pencarian
            });

            $('#load-more-btn').on('click', function() {
                page++; // Tambahkan halaman untuk memuat data selanjutnya
                loadSearchResults(searchQuery, page, true); // Memanggil fungsi pencarian
            });

            // Memanggil fungsi untuk load data pertama kali jika ada query yang ada
            loadSearchResults();
        });
    </script>
@endpush

<x-user-layout>
    <div class="p-0 container-fluid">
        <div class="row no-gutters" style="min-height: 100vh; display: flex;">
            <!-- Left Sidebar (fixed) -->
            <div class="p-3 d-flex flex-column justify-content-center align-items-center"
                 style="background: #2D9CAD; position: sticky; top: 0; height: 100vh; width: 250px; z-index: 1;">
                <div class="gap-3 d-flex flex-column align-items-center w-100">
                    <a href="#" class="btn btn-light rounded-pill w-100">Beranda</a>
                    <a href="#" class="btn btn-light rounded-pill w-100">Makanan</a>
                    <a href="#" class="btn btn-light rounded-pill w-100">Minuman</a>
                    <a href="#" class="btn btn-light rounded-pill w-100">Cemilan</a>
                </div>
            </div>

            <!-- Content Area (col-10 for 10/12 width) with left margin -->
            <div class="p-3" style="flex: 1; padding-left: 20px; padding-top: 20px;">
                <!-- Main Content Header inside the content area -->
                <div class="mb-4">
                    <div class="">
                        <div class="mb-3 input-group">
                            <input type="text" class="form-control" placeholder="Search" aria-label="Search"
                                name="search" aria-describedby="button-search" id="search" value="">
                        </div>
                    </div>
                </div>

                {{-- table disini namun nantinya akan card kesamping --}}
                <div id="menu-container" class="row">
                    <!-- Data card akan dimasukkan di sini -->
                </div>
                <div id="loading" style="display:none; text-align: center;">
                    <!-- Bisa menggunakan animasi loading seperti spinner -->
                    <div class="mb-4 spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <button id="load-more-btn" class="btn btn-primary btn-block" style="display: block;">Load More</button>
                <!-- More content goes here -->
            </div>
        </div>
    </div>
</x-user-layout>
