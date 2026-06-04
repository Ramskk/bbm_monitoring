<nav id="sidebar" class="bg-dark text-white p-3 vh-100 sticky-top d-flex flex-column">
    <h4 class="mb-4">Sistem BBM</h4>
    <ul class="nav flex-column flex-grow-1">
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-house-door me-2"></i> Dashboard
            </a>
        </li>

        @role('super_admin|admin|petugas_operasional|viewer')
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('operational*') ? 'active' : '' }}" href="{{ route('operational.index') }}">
                <i class="bi bi-fuel-pump me-2"></i> Operasional BBM
            </a>
        </li>
        @endrole

        @role('super_admin|admin|admin_gudang|viewer')
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('gudang*') ? 'active' : '' }}" href="{{ route('gudang.index') }}">
                <i class="bi bi-box-seam me-2"></i> Gudang
            </a>
        </li>
        @endrole

        @role('super_admin|admin|admin_pengadaan|viewer')
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('po*') ? 'active' : '' }}" href="{{ route('po.index') }}">
                <i class="bi bi-cart-check me-2"></i> Purchase Order
            </a>
        </li>
        @endrole

        @role('super_admin|admin|kadiv')
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('approval*') ? 'active' : '' }}" href="{{ route('approval.index') }}">
                <i class="bi bi-check-circle me-2"></i> Approval
            </a>
        </li>
        @endrole

        @role('super_admin|admin|kadiv|petugas_operasional|admin_gudang|admin_pengadaan|viewer')
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('laporan*') ? 'active' : '' }}" href="{{ route('laporan.efisiensi') }}">
                <i class="bi bi-graph-up me-2"></i> Laporan
            </a>
        </li>
        @endrole

        @role('super_admin|admin')
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('kendaraan*') ? 'active' : '' }}" href="{{ route('kendaraan.index') }}">
                <i class="bi bi-car-front me-2"></i> Master Kendaraan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('bbm*') ? 'active' : '' }}" href="{{ route('bbm.index') }}">
                <i class="bi bi-gas-pump me-2"></i> Master BBM
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('vendor*') ? 'active' : '' }}" href="{{ route('vendor.index') }}">
                <i class="bi bi-truck me-2"></i> Master Vendor
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('user*') ? 'active' : '' }}" href="{{ route('user.index') }}">
                <i class="bi bi-people me-2"></i> User Management
            </a>
        </li>
        @endrole
    </ul>
    <div class="mt-auto">
        <hr class="bg-white">
        <p class="text-muted small">Logged in as: {{ Auth::user()->name }}</p>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm w-100">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </button>
        </form>
    </div>
</nav>
