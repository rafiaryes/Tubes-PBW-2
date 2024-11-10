<x-app-layout>
    <div class="container-fluid">        
         <!-- Display Breadcrumbs -->
         <div aria-label="breadcrumb">
            <ol class="breadcrumb">
                @foreach (Breadcrumbs::generate() as $breadcrumb)
                    <li class="breadcrumb-item">
                        @if ($breadcrumb->url)
                            <a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
                        @else
                            {{ $breadcrumb->title }}
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3 py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Master Data Role</h6>
                <a href="{{ route($routePrefix . '.create') }}" class="btn btn-success btn-sm">Tambah Role</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Role</th>
                                <th>Guard</th>
                                <th>Actions</th>
                            </tr>
                        </thead>                        
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabel Permission -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Master Data Permission</h6>
                <a href="{{ route('admin.master_data.permission.create') }}" class="btn btn-success btn-sm">Tambah Permission</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="permissionTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>                                
                                <th>Nama Permission</th>
                                <th>Guard</th>
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
                        { data: 'DT_RowIndex', name: 'DT_RowIndex' }, // Index column
                        { data: 'name', name: 'name' }, // Name column
                        { data: 'guard_name', name: 'guard_name' }, // Guard column
                        { data: 'action', name: 'action', orderable: false, searchable: false } // Actions (Edit/Delete)
                    ]
                });

                // DataTable untuk Permission
                $('#permissionTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('admin.master_data.permission.index') }}',
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex' },                      
                        { data: 'name', name: 'name' },
                        { data: 'guard_name', name: 'guard_name' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ],
                    rowGroup: {
                        dataSrc: 'prefix'
                    },
                    drawCallback: function (settings) {
                        console.log(settings)
                    }

                });

            });
        </script>
    @endpush
</x-app-layout>
