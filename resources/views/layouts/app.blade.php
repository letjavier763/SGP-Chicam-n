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
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>

                        @auth

                            {{-- ── Desplegable: Ventanilla ─────────────────── --}}
                            @if(Auth::user()->esAdministrador() || Auth::user()->esRecepcionista())
                                <li class="nav-item dropdown {{ Request::routeIs('ventanilla.*', 'turnos.*') ? 'active' : '' }}">
                                    <a class="nav-link dropdown-toggle" href="#sidebar-ventanilla" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ Request::routeIs('ventanilla.*', 'turnos.*') ? 'true' : 'false' }}">
                                        <span class="nav-link-title">Ventanilla</span>
                                    </a>
                                    <div class="dropdown-menu {{ Request::routeIs('ventanilla.*', 'turnos.*') ? 'show' : '' }}">
                                        <a class="dropdown-item {{ Request::routeIs('ventanilla.*') ? 'active' : '' }}" href="{{ route('ventanilla.index') }}">
                                            Ventanilla
                                        </a>
                                        <a class="dropdown-item {{ Request::routeIs('turnos.*') ? 'active' : '' }}" href="{{ route('turnos.index') }}">
                                            Turnos del Personal
                                        </a>
                                    </div>
                                </li>
                            @endif

                            {{-- ── Desplegable: Registros ─────────────────── --}}
                            @if(Auth::user()->esAdministrador() || Auth::user()->esRecepcionista())
                                <li class="nav-item dropdown {{ Request::routeIs('pacientes.*', 'familias.*') ? 'active' : '' }}">
                                    <a class="nav-link dropdown-toggle" href="#sidebar-registros" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ Request::routeIs('pacientes.*', 'familias.*') ? 'true' : 'false' }}">
                                        <span class="nav-link-title">Registros</span>
                                    </a>
                                    <div class="dropdown-menu {{ Request::routeIs('pacientes.*', 'familias.*') ? 'show' : '' }}">
                                        <a class="dropdown-item {{ Request::routeIs('pacientes.*') ? 'active' : '' }}" href="{{ route('pacientes.index') }}">
                                            Pacientes
                                        </a>
                                        <a class="dropdown-item {{ Request::routeIs('familias.*') ? 'active' : '' }}" href="{{ route('familias.index') }}">
                                            Núcleos Familiares
                                        </a>
                                    </div>
                                </li>
                            @endif

                            {{-- ── Desplegable: Reportería ─────────── --}}
                            @if(Auth::user()->esAdministrador() || Auth::user()->esDirector())
                                <li class="nav-item dropdown {{ Request::routeIs('reportes.*') ? 'active' : '' }}">
                                    <a class="nav-link dropdown-toggle" href="#sidebar-reporteria" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ Request::routeIs('reportes.*') ? 'true' : 'false' }}">
                                        <span class="nav-link-title">Reportería</span>
                                    </a>
                                    <div class="dropdown-menu {{ Request::routeIs('reportes.*') ? 'show' : '' }}">
                                        <a class="dropdown-item {{ Request::routeIs('reportes.*') ? 'active' : '' }}" href="{{ route('reportes.index') }}">
                                            Estadísticas y Reportes
                                        </a>
                                    </div>
                                </li>
                            @endif

                            {{-- ── Desplegable: Administración ──────────────────── --}}
                            @if(Auth::user()->esAdministrador())
                                <li class="nav-item dropdown {{ Request::routeIs('alertas.*', 'bitacora.*') ? 'active' : '' }}">
                                    <a class="nav-link dropdown-toggle" href="#sidebar-admin" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ Request::routeIs('alertas.*', 'bitacora.*') ? 'true' : 'false' }}">
                                        <span class="nav-link-title">Administración</span>
                                    </a>
                                    <div class="dropdown-menu {{ Request::routeIs('alertas.*', 'bitacora.*') ? 'show' : '' }}">
                                        <a class="dropdown-item {{ Request::routeIs('alertas.*') ? 'active' : '' }}" href="{{ route('alertas.index') }}">
                                            Alertas Duplicidad
                                        </a>
                                        <a class="dropdown-item {{ Request::routeIs('bitacora.*') ? 'active' : '' }}" href="{{ route('bitacora.index') }}">
                                            Bitácora
                                        </a>
                                    </div>
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
