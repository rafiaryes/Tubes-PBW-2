 <!-- Sidebar -->
 <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Kantin Pintar</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

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
    <li class="nav-item {{ request()->is('admin/master_data*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMasterData"
            aria-expanded="true" aria-controls="collapseMasterData">
            <i class="fas fa-fw fa-database"></i>
            <span>Master Data</span>
        </a>
        <div id="collapseMasterData" class="collapse {{ request()->is('admin/master_data*') ? 'show' : '' }}" aria-labelledby="headingMasterData" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Data Management:</h6>
                <a class="collapse-item {{ request()->is('admin/master_data/menu*') ? 'active' : '' }}" href="{{ route('admin.menu.index') }}">Menu</a>              
                <!-- Tambahkan item lain di sini sesuai kebutuhan -->
            </div>
        </div>
    </li>

     <!-- Divider -->
     <hr class="sidebar-divider d-none d-md-block">

     <!-- Sidebar Toggler (Sidebar) -->
     <div class="text-center d-none d-md-inline">
         <button class="rounded-circle border-0" id="sidebarToggle"></button>
     </div>         
</ul>
<!-- End of Sidebar -->