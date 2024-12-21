 <!-- Sidebar -->
 <ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="mx-3 sidebar-brand-text">Dapo Smart</div>
    </a>

    <!-- Divider -->
    <hr class="my-0 sidebar-divider">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route("dashboard") }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Managemen
    </div>
    <!-- Nav Item - Master Data Collapse Menu -->
    @hasrole("admin")
    <li class="nav-item {{ request()->is('admin/master_data*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMasterData"
            aria-expanded="true" aria-controls="collapseMasterData">
            <i class="fas fa-fw fa-database"></i>
            <span>Master Data</span>
        </a>
        <div id="collapseMasterData" class="collapse {{ request()->is('admin/master_data*') ? 'show' : '' }}" aria-labelledby="headingMasterData" data-parent="#accordionSidebar">
            <div class="py-2 bg-white rounded collapse-inner">
                <h6 class="collapse-header">Data Management:</h6>
                {{-- @can("menu.view") --}}
                <a class="collapse-item {{ request()->is('admin/master_data/menu*') ? 'active' : '' }}" href="{{ route('admin.master_data.menu.index') }}">Menu</a>
                {{-- @endcan               --}}
                <a class="collapse-item {{ request()->is('admin/master_data/user*') ? 'active' : '' }}" href="{{ route('admin.master_data.user.index') }}">User</a>
                <a class="collapse-item {{ (request()->is('admin/master_data/role*') || request()->is('admin/master_data/permission*')) ? 'active' : '' }}" href="{{ route('admin.master_data.role.index') }}">Role & Permission</a> <!-- Menambahkan Role -->
                <!-- Tambahkan item lain di sini sesuai kebutuhan -->
            </div>
        </div>
    </li>
    @endhasrole
    @hasrole("kasir")
    <li class="nav-item {{ request()->is('order*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('order-list') }}" aria-expanded="true">
            <i class="fas fa-fw fa-database"></i>
            <span>Order Data</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('history-order*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('history-order-list') }}" aria-expanded="true">
            <i class="fas fa-fw fa-database"></i>
            <span>History Order Data</span>
        </a>
    </li>

    @endhasrole

     <!-- Divider -->
     <hr class="sidebar-divider d-none d-md-block">

     <!-- Sidebar Toggler (Sidebar) -->
     <div class="text-center d-none d-md-inline">
         <button class="border-0 rounded-circle" id="sidebarToggle"></button>
     </div>
</ul>
<!-- End of Sidebar -->
