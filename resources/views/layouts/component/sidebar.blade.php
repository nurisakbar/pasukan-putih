<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="/" class="brand-link">
            <img src="{{ asset('assets/dist/assets/img/jaksehat.png') }}" alt="DInkes Jakarta"
                class="brand-image opacity-75 shadow" />
            <span class="brand-text fw-light">DINKES JAKARTA</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="/" class="nav-link">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                @if (auth()->user()->role != 'perawat')
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <i class="nav-icon bi bi-person"></i>
                        <p>User</p>
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a href="{{ route('pasiens.index') }}" class="nav-link">
                        <i class="nav-icon bi bi-person-badge"></i>
                        <p>Pasien</p>
                    </a>
                </li>
                {{-- <li class="nav-item">
              <a href="{{ route('kunjungan.rencanaKunjunganAwal')}}" class="nav-link">
               <i class="nav-icon bi bi-calendar-event"></i>
                <p>Rencana Kunjungan Awal</p>
              </a>
            </li> --}}
                <li class="nav-item">
                    <a href="{{ route('visitings.index') }}" class="nav-link">
                        <i class="nav-icon bi bi-calendar-event"></i>
                        <p>Kunjungan</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
