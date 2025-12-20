<nav class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <i class="bi bi-file-earmark-check"></i>
            <span>Sistem Transaksi</span>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
    </div>
    
    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            @if(Auth::user()->isPemohon())
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('transactions.create') ? 'active' : '' }}" href="{{ route('transactions.create') }}">
                    <i class="bi bi-plus-circle"></i>
                    <span>Buat Pengajuan</span>
                </a>
            </li>
            @endif
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('transactions.index') ? 'active' : '' }}" href="{{ route('transactions.index') }}">
                    <i class="bi bi-file-text"></i>
                    <span>Daftar Transaksi</span>
                </a>
            </li>
            
            @if(Auth::user()->isPejabat())
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('transactions.pending') ? 'active' : '' }}" href="{{ route('transactions.pending') }}">
                    <i class="bi bi-clock-history"></i>
                    <span>Perlu Persetujuan</span>
                </a>
            </li>
            @endif
            
            <li class="nav-item mt-4">
                <div class="nav-section-title">AKUN</div>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
    
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <i class="bi bi-person-circle"></i>
            </div>
            <div class="user-details">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">{{ Auth::user()->role_name }}</div>
            </div>
        </div>
    </div>
    
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</nav>
