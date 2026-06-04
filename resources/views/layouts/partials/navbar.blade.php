<div class="topbar">
    <div class="d-flex justify-content-between align-items-center w-100">
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-light me-3 d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <h6 class="topbar-title">
                    @isset($header)
                        {{ $header }}
                    @else
                        {{ config('app.name', 'Sistem Pengawasan BBM') }}
                    @endisset
                </h6>
                @if(isset($breadcrumb) && is_array($breadcrumb) && count($breadcrumb) > 0)
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="text-decoration: none;">Home</a></li>
                            @foreach($breadcrumb as $label => $url)
                                @if(is_numeric($label))
                                    <li class="breadcrumb-item active">{{ $url }}</li>
                                @else
                                    <li class="breadcrumb-item"><a href="{{ $url }}" style="text-decoration: none;">{{ $label }}</a></li>
                                @endif
                            @endforeach
                        </ol>
                    </nav>
                @endif
            </div>
        </div>

        @auth
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-2"></i>
                    <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuDropdown">
                    <li><h6 class="dropdown-header">{{ auth()->user()->email }}</h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person me-2"></i> Profil
                        </a>
                    </li>
                    @if(auth()->user()->hasAnyRole(['admin', 'super_admin']))
                        <li><a class="dropdown-item" href="{{ route('user.index') }}">
                            <i class="bi bi-people me-2"></i> Manajemen User
                        </a></li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @endauth
    </div>
</div>
