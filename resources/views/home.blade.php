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
            let currentRequest = null;
            let searchQuery = $("#search").val();
            let page = 1;
            const loadMoreLimit = 12;
            let filterByCategory = "";
            let isLoading = false; // Untuk mencegah permintaan ganda

            function loadSearchResults(query = '', page = 1, isLoadMore = false) {
                if (isLoading) return; // Mencegah permintaan jika sedang memuat data

                isLoading = true; // Tandai proses loading aktif
                $('#loading').show();

                if (currentRequest) {
                    currentRequest.abort(); // Batalkan request sebelumnya
                }

                currentRequest = $.ajax({
                    url: "{{ route('user.home') }}", // Ganti dengan route Anda
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        search: query,
                        page: page,
                        limit: loadMoreLimit,
                        category: filterByCategory
                    },
                    success: function(response) {
                        $('#loading').hide();
                        if (response.html) {
                            if (isLoadMore) {
                                $('#menu-container').append(response.html);
                            } else {
                                $('#menu-container').html(response.html);
                            }
                        } else if (!isLoadMore) {
                            $('#menu-container').html('<p>No menus found.</p>');
                        }

                        isLoading = false; // Tandai proses loading selesai
                        if (!response.hasMore) {
                            observer.disconnect(); // Hentikan observer jika tidak ada data lagi
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#loading').hide();
                        if (status !== 'abort') {
                            console.error('Error fetching data:', error);
                        }
                        isLoading = false; // Tandai proses loading selesai meski ada error
                    }
                });
            }

            // Event listener untuk input pencarian
            $('#search').on('input', function() {
                page = 1;
                searchQuery = $(this).val();
                loadSearchResults(searchQuery);
            });

            $(document).on('click', '#btnCategory', function() {
                page = 1;
                filterByCategory = $(this).data('category');

                // Scroll ke atas sebelum memuat ulang data
                $('html, body').animate({
                    scrollTop: 0
                }, 'fast', function() {
                    setTimeout(() => {
                        loadSearchResults(searchQuery);
                    }, 500);
                });

                observer.observe(document.querySelector(
                '#sentinel')); // Mulai observasi pada elemen sentinel
            });

            // Intersection Observer untuk Infinite Scroll
            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting && !isLoading) {
                    setTimeout(() => {
                        page++;
                        loadSearchResults(searchQuery, page, true);
                    }, 400);
                }
            }, {
                root: null, // Menggunakan viewport sebagai root
                rootMargin: '0px', // Margin tambahan di sekitar root
                threshold: 0.5 // Persentase elemen yang harus terlihat sebelum memuat lebih banyak
            });

            observer.observe(document.querySelector('#sentinel')); // Mulai observasi pada elemen sentinel

            // Muat data awal
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
                    <button id="btnCategory" data-category="" class="btn btn-light rounded-pill w-100">Beranda</button>
                    <button id="btnCategory" data-category="makanan"
                        class="btn btn-light rounded-pill w-100">Makanan</button>
                    <button id="btnCategory" data-category="minuman"
                        class="btn btn-light rounded-pill w-100">Minuman</button>
                    <button id="btnCategory" data-category="cemilan"
                        class="btn btn-light rounded-pill w-100">Cemilan</button>
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
                <div id="sentinel" style="height: 1px; margin-bottom: 60px"></div>
                <!-- More content goes here -->
            </div>
        </div>
    </div>
</x-user-layout>
