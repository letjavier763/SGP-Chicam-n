<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SGP') | CAP Chicamán</title>
    
    <!-- Tabler UI Core CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    
    <!-- Estilos Adicionales Personalizados -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=3">
    @yield('styles')
</head>
<body>
    <div class="page">
        
        <!-- Sidebar Navigation (Menú Lateral Tabler) -->
        <aside class="navbar navbar-vertical navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none text-white">
                        <span>SGP Chicamán</span>
                    </a>
                </h1>

                @auth
                <div class="navbar-nav flex-row d-lg-none">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                            <span class="avatar avatar-sm bg-blue text-white rounded-circle">
                                {{ strtoupper(substr(Auth::user()->nombre_completo, 0, 2)) }}
                            </span>
                        </a>
                    </div>
                </div>
                @endauth

                <div class="collapse navbar-collapse" id="sidebar-menu">
                    <ul class="navbar-nav pt-lg-3">
                        {{-- Dashboard --}}
                        <li class="nav-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-dashboard"></i>
                                </span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>

                        @auth

                            {{-- ── Ventanilla y Turnos ─────────────────── --}}
                            @if(Auth::user()->esAdministrador() || Auth::user()->esRecepcionista())
                                <li class="nav-item mt-2">
                                    <span class="nav-link text-uppercase text-secondary" style="font-size:.65rem; letter-spacing:.08em; pointer-events:none; padding-bottom:2px;">
                                        Ventanilla
                                    </span>
                                </li>
                                <li class="nav-item {{ Request::routeIs('ventanilla.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('ventanilla.index') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <i class="ti ti-door-enter"></i>
                                        </span>
                                        <span class="nav-link-title">Ventanilla</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::routeIs('turnos.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('turnos.index') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <i class="ti ti-clock"></i>
                                        </span>
                                        <span class="nav-link-title">Turnos del Personal</span>
                                    </a>
                                </li>
                            @endif

                            {{-- ── Pacientes / Familias ─────────────────── --}}
                            @if(Auth::user()->esAdministrador() || Auth::user()->esRecepcionista())
                                <li class="nav-item mt-2">
                                    <span class="nav-link text-uppercase text-secondary" style="font-size:.65rem; letter-spacing:.08em; pointer-events:none; padding-bottom:2px;">
                                        Registros
                                    </span>
                                </li>
                                <li class="nav-item {{ Request::routeIs('pacientes.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('pacientes.index') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <i class="ti ti-users"></i>
                                        </span>
                                        <span class="nav-link-title">Pacientes</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::routeIs('familias.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('familias.index') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <i class="ti ti-home-heart"></i>
                                        </span>
                                        <span class="nav-link-title">Núcleos Familiares</span>
                                    </a>
                                </li>
                            @endif

                            {{-- ── Reportería (Admin + Director) ─────────── --}}
                            @if(Auth::user()->esAdministrador() || Auth::user()->esDirector())
                                <li class="nav-item mt-2">
                                    <span class="nav-link text-uppercase text-secondary" style="font-size:.65rem; letter-spacing:.08em; pointer-events:none; padding-bottom:2px;">
                                        Reportería
                                    </span>
                                </li>
                                <li class="nav-item {{ Request::routeIs('reportes.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('reportes.index') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <i class="ti ti-chart-bar"></i>
                                        </span>
                                        <span class="nav-link-title">Estadísticas y Reportes</span>
                                    </a>
                                </li>
                            @endif

                            {{-- ── Solo Administrador ──────────────────── --}}
                            @if(Auth::user()->esAdministrador())
                                <li class="nav-item mt-2">
                                    <span class="nav-link text-uppercase text-secondary" style="font-size:.65rem; letter-spacing:.08em; pointer-events:none; padding-bottom:2px;">
                                        Administración
                                    </span>
                                </li>
                                <li class="nav-item {{ Request::routeIs('alertas.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('alertas.index') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <i class="ti ti-alert-triangle"></i>
                                        </span>
                                        <span class="nav-link-title">Alertas Duplicidad</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::routeIs('bitacora.*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('bitacora.index') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <i class="ti ti-shield-check"></i>
                                        </span>
                                        <span class="nav-link-title">Bitácora</span>
                                    </a>
                                </li>
                            @endif

                        @endauth
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Navbar Header (Encabezado Superior Tabler) -->
        <header class="navbar navbar-expand-md d-none d-lg-flex d-print-none shadow-sm">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="navbar-nav flex-row order-md-last">
                    @auth
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Abrir menú de usuario">
                            <span class="avatar avatar-sm bg-blue-lt text-blue rounded-circle me-2 fw-bold">
                                {{ strtoupper(substr(Auth::user()->nombre_completo, 0, 2)) }}
                            </span>
                            <div class="d-none d-xl-block ps-1 text-start">
                                <div class="fw-bold">{{ Auth::user()->nombre_completo }}</div>
                                <div class="mt-1 small text-secondary">{{ Auth::user()->rol->nombre_rol }}</div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger fw-bold">
                                    <i class="ti ti-logout me-2"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                    @endauth
                </div>
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <div class="text-secondary small fw-medium">
                        <i class="ti ti-calendar me-1"></i> {{ now()->locale('es')->isoFormat('D [de] MMMM, YYYY') }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido Principal -->
        <div class="page-wrapper">
            <!-- Encabezado de Página -->
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <h2 class="page-title text-primary-emphasis">
                                @yield('page_title', 'Inicio')
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cuerpo de Página -->
            <div class="page-body">
                <div class="container-xl">
                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer footer-transparent d-print-none mt-auto py-3">
                <div class="container-xl text-center text-secondary small">
                    SGP Chicamán &copy; {{ date('Y') }} — Centro de Atención Permanente
                </div>
            </footer>
        </div>
    </div>

    <!-- Tabler UI JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
    @yield('scripts')
    <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (typeof window.performance != "undefined" && window.performance.navigation.type === 2)) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>
