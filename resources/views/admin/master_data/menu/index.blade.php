<x-app-layout>
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>
        <div class="card shadow mb-4">
            <div class="card-header py-3 py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Master Data Menu</h6>
                <a href="{{ route($routePrefix . '.create') }}" class="btn btn-success btn-sm">Tambah Menu</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Deskripsi</th>
                                <th>Harga</th>
                                <th>Stok</th>                                
                                <th>Foto Menu</th>
                                <th>Actions</th>
                            </tr>
                        </thead>                        
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route($routePrefix . '.index') }}', // Make sure this matches your controller's route
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex'},
                        { data: 'nama', name: 'nama' },
                        { data: 'deskripsi', name: 'deskripsi' },
                        { data: 'price', name: 'price' },
                        { data: 'stok', name: 'stok' },                        
                        { data: 'foto_menu', name: 'foto_menu', orderable: false, searchable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });
            });
        </script>
    @endpush
</x-app-layout>
