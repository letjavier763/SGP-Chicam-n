<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'SGP') | CAP Chicamán</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Tabler UI Core CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    
    <!-- Estilos del Design System SGP Chicamán -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=41">
    @yield('styles')
</head>
<body>
    <!-- Barra de Transición de Módulos Superior -->
    <div id="module-loading-bar" style="position: fixed; top: 0; left: 0; height: 3px; width: 0%; background: linear-gradient(90deg, #38bdf8, #0057cd); z-index: 9999; transition: width 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease; opacity: 0; pointer-events: none;"></div>

    <div class="page">
        
        <!-- Sidebar Navigation (Menú Lateral Tabler) -->
        <aside class="navbar navbar-vertical navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">

                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none text-white">
                        <span>SGP Chicamán</span>
                    </a>
                </h1>

                @auth
                <div class="navbar-nav flex-row d-lg-none">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Abrir menú de usuario">
                            <span class="avatar avatar-sm bg-blue text-white rounded-circle fw-bold">
                                {{ strtoupper(substr(Auth::user()->nombre_completo, 0, 2)) }}
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end mobile-user-dropdown shadow-lg">
                            <button type="button" class="dropdown-item text-danger fw-bold" data-bs-toggle="modal" data-bs-target="#modalConfirmarLogout">
                                <i class="ti ti-logout me-2"></i> Cerrar Sesión
                            </button>
                        </div>
                    </div>
                </div>
                @endauth

                <div class="collapse navbar-collapse d-none d-lg-block" id="sidebar-menu">

                    <ul class="navbar-nav pt-lg-3">
                        {{-- Dashboard / Inicio --}}
                        <li class="nav-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="ti ti-home me-2"></i> <span class="nav-link-title">Inicio</span>
                            </a>
                        </li>

                        @auth

                            {{-- ── Desplegable: Ventanilla ─────────────────── --}}
                            @if(Auth::user()->esAdministrador() || Auth::user()->esRecepcionista())
                                <li class="nav-item dropdown {{ Request::routeIs('ventanilla.*', 'turnos.*') ? 'active' : '' }}">
                                    <a class="nav-link dropdown-toggle" href="#sidebar-ventanilla" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ Request::routeIs('ventanilla.*', 'turnos.*') ? 'true' : 'false' }}">
                                        <i class="ti ti-building-hospital me-2"></i>
                                        <span class="nav-link-title">Ventanilla</span>
                                    </a>
                                    <div class="dropdown-menu {{ Request::routeIs('ventanilla.*', 'turnos.*') ? 'show' : '' }}">
                                        <a class="dropdown-item {{ Request::routeIs('ventanilla.*') ? 'active' : '' }}" href="{{ route('ventanilla.index') }}">
                                            <i class="ti ti-app-window me-2"></i> Ventanilla
                                        </a>
                                        <a class="dropdown-item {{ Request::routeIs('turnos.*') ? 'active' : '' }}" href="{{ route('turnos.index') }}">
                                            <i class="ti ti-calendar-time me-2"></i> Turnos del Personal
                                        </a>
                                    </div>
                                </li>
                            @endif

                            {{-- ── Desplegable: Registros ─────────────────── --}}
                            @if(Auth::user()->esAdministrador() || Auth::user()->esRecepcionista())
                                <li class="nav-item dropdown {{ Request::routeIs('pacientes.*', 'familias.*') ? 'active' : '' }}">
                                    <a class="nav-link dropdown-toggle" href="#sidebar-registros" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ Request::routeIs('pacientes.*', 'familias.*') ? 'true' : 'false' }}">
                                        <i class="ti ti-folder me-2"></i>
                                        <span class="nav-link-title">Registros</span>
                                    </a>
                                    <div class="dropdown-menu {{ Request::routeIs('pacientes.*', 'familias.*') ? 'show' : '' }}">
                                        <a class="dropdown-item {{ Request::routeIs('pacientes.*') ? 'active' : '' }}" href="{{ route('pacientes.index') }}">
                                            <i class="ti ti-users me-2"></i> Pacientes
                                        </a>
                                        <a class="dropdown-item {{ Request::routeIs('familias.*') ? 'active' : '' }}" href="{{ route('familias.index') }}">
                                            <i class="ti ti-home-heart me-2"></i> Núcleos Familiares
                                        </a>
                                    </div>
                                </li>
                            @endif

                            {{-- ── Desplegable: Reportería ─────────── --}}
                            @if(Auth::user()->esAdministrador() || Auth::user()->esDirector())
                                <li class="nav-item dropdown {{ Request::routeIs('reportes.*') ? 'active' : '' }}">
                                    <a class="nav-link dropdown-toggle" href="#sidebar-reporteria" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ Request::routeIs('reportes.*') ? 'true' : 'false' }}">
                                        <i class="ti ti-chart-bar me-2"></i>
                                        <span class="nav-link-title">Reportería</span>
                                    </a>
                                    <div class="dropdown-menu {{ Request::routeIs('reportes.*') ? 'show' : '' }}">
                                        <a class="dropdown-item {{ Request::routeIs('reportes.*') ? 'active' : '' }}" href="{{ route('reportes.index') }}">
                                            <i class="ti ti-file-analytics me-2"></i> Estadísticas y Reportes
                                        </a>
                                    </div>
                                </li>
                            @endif

                            {{-- ── Desplegable: Administración ──────────────────── --}}
                            @if(Auth::user()->esAdministrador())
                                <li class="nav-item dropdown {{ Request::routeIs('alertas.*', 'bitacora.*', 'personal.*') ? 'active' : '' }}">
                                    <a class="nav-link dropdown-toggle" href="#sidebar-admin" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ Request::routeIs('alertas.*', 'bitacora.*', 'personal.*') ? 'true' : 'false' }}">
                                        <i class="ti ti-settings me-2"></i>
                                        <span class="nav-link-title">Administración</span>
                                    </a>
                                    <div class="dropdown-menu {{ Request::routeIs('alertas.*', 'bitacora.*', 'personal.*') ? 'show' : '' }}">
                                        <a class="dropdown-item {{ Request::routeIs('personal.*') ? 'active' : '' }}" href="{{ route('personal.index') }}">
                                            <i class="ti ti-user-check me-2"></i> Gestión de Personal
                                        </a>
                                        <a class="dropdown-item {{ Request::routeIs('alertas.*') ? 'active' : '' }}" href="{{ route('alertas.index') }}">
                                            <i class="ti ti-copy-off me-2"></i> Alertas Duplicidad
                                        </a>
                                        <a class="dropdown-item {{ Request::routeIs('bitacora.*') ? 'active' : '' }}" href="{{ route('bitacora.index') }}">
                                            <i class="ti ti-history me-2"></i> Bitácora
                                        </a>
                                    </div>
                                </li>
                            @endif

                            {{-- Logout accesible en escritorio en el menú lateral --}}
                            <li class="nav-item d-none d-lg-block mt-3" style="border-top:1px solid rgba(255,255,255,0.08);padding-top:0.5rem;">
                                <button type="button" class="nav-link w-100 text-start" style="background:none;border:none;color:#f87171;font-weight:600;" data-bs-toggle="modal" data-bs-target="#modalConfirmarLogout">
                                    <i class="ti ti-logout me-2"></i> Cerrar Sesión
                                </button>
                            </li>

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
                        <div class="dropdown-menu dropdown-menu-end mobile-user-dropdown shadow-lg">
                            <button type="button" class="dropdown-item text-danger fw-bold" data-bs-toggle="modal" data-bs-target="#modalConfirmarLogout">
                                <i class="ti ti-logout me-2"></i> Cerrar Sesión
                            </button>
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
                            <h2 class="page-title text-primary-emphasis mb-0">
                                @yield('page_title', 'Inicio')
                            </h2>
                            @hasSection('page_subtitle')
                                <div class="text-secondary small mt-1">
                                    @yield('page_subtitle')
                                </div>
                            @endif
                        </div>
                        @hasSection('page_actions')
                        <div class="col-auto ms-auto d-print-none">
                            <div class="btn-list">
                                @yield('page_actions')
                            </div>
                        </div>
                        @endif
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
    @auth
    <!-- Backdrop difuminado para ventanas emergentes de la barra móvil -->
    <div class="mobile-nav-backdrop d-lg-none d-print-none" id="mobileNavBackdrop"></div>

    <!-- Barra de Navegación Inferior (Móvil) -->
    <nav class="mobile-bottom-nav d-lg-none d-print-none">
        {{-- Inicio / Dashboard --}}
        <div class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                <i class="ti ti-home"></i>
                <span>Inicio</span>
            </a>
        </div>

        {{-- Ventanilla --}}
        {{-- Ventanilla --}}
        @if(Auth::user()->esAdministrador() || Auth::user()->esRecepcionista())
            <div class="nav-item dropup nav-item-ventanilla {{ Request::routeIs('ventanilla.*', 'turnos.*') ? 'active' : '' }}">
                <a href="#" class="dropup-toggle nav-link {{ Request::routeIs('ventanilla.*', 'turnos.*') ? 'active' : '' }}"
                   data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                    <i class="ti ti-building-hospital"></i>
                    <span>Ventanilla</span>
                </a>
                <div class="dropdown-menu dropdown-menu-nav-custom">
                    <div class="nav-popover-header">
                        <span class="nav-popover-title">
                            <i class="ti ti-building-hospital"></i> Ventanilla
                        </span>
                        <button type="button" class="nav-popover-close" onclick="const d = bootstrap.Dropdown.getInstance(this.closest('.dropup').querySelector('[data-bs-toggle=dropdown]')); if(d) d.hide();" aria-label="Cerrar">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>

                    <a class="nav-popover-item {{ Request::routeIs('ventanilla.*') ? 'active' : '' }}" href="{{ route('ventanilla.index') }}">
                        <div class="nav-popover-icon bg-blue-subtle">
                            <i class="ti ti-app-window"></i>
                        </div>
                        <div class="nav-popover-text">
                            <span class="nav-popover-item-title">Ventanilla Única</span>
                            <span class="nav-popover-item-desc">Recepción y control de llegadas</span>
                        </div>
                        <div class="nav-popover-badge">
                            @if(Request::routeIs('ventanilla.*'))
                                <span class="nav-badge-pill">Activo</span>
                            @else
                                <i class="ti ti-chevron-right nav-popover-arrow"></i>
                            @endif
                        </div>
                    </a>

                    <a class="nav-popover-item {{ Request::routeIs('turnos.*') ? 'active' : '' }}" href="{{ route('turnos.index') }}">
                        <div class="nav-popover-icon bg-amber-subtle">
                            <i class="ti ti-calendar-event"></i>
                        </div>
                        <div class="nav-popover-text">
                            <span class="nav-popover-item-title">Turnos del Personal</span>
                            <span class="nav-popover-item-desc">Calendario y asignaciones</span>
                        </div>
                        <div class="nav-popover-badge">
                            @if(Request::routeIs('turnos.*'))
                                <span class="nav-badge-pill">Activo</span>
                            @else
                                <i class="ti ti-chevron-right nav-popover-arrow"></i>
                            @endif
                        </div>
                    </a>

                    <div class="nav-popover-footer">
                        <span class="nav-popover-module">Módulo activo: Ventanilla</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Registros --}}
        @if(Auth::user()->esAdministrador() || Auth::user()->esRecepcionista())
            <div class="nav-item dropup nav-item-registros {{ Request::routeIs('pacientes.*', 'familias.*') ? 'active' : '' }}">
                <a href="#" class="dropup-toggle nav-link {{ Request::routeIs('pacientes.*', 'familias.*') ? 'active' : '' }}"
                   data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                    <i class="ti ti-folder"></i>
                    <span>Registros</span>
                </a>
                <div class="dropdown-menu dropdown-menu-nav-custom">
                    <div class="nav-popover-header">
                        <span class="nav-popover-title">
                            <i class="ti ti-folder"></i> Registros
                        </span>
                        <button type="button" class="nav-popover-close" onclick="const d = bootstrap.Dropdown.getInstance(this.closest('.dropup').querySelector('[data-bs-toggle=dropdown]')); if(d) d.hide();" aria-label="Cerrar">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>

                    <a class="nav-popover-item {{ Request::routeIs('pacientes.*') ? 'active' : '' }}" href="{{ route('pacientes.index') }}">
                        <div class="nav-popover-icon bg-purple-subtle">
                            <i class="ti ti-user-heart"></i>
                        </div>
                        <div class="nav-popover-text">
                            <span class="nav-popover-item-title">Pacientes</span>
                            <span class="nav-popover-item-desc">Expedientes y datos clínicos</span>
                        </div>
                        <div class="nav-popover-badge">
                            @if(Request::routeIs('pacientes.*'))
                                <span class="nav-badge-pill">Activo</span>
                            @else
                                <i class="ti ti-chevron-right nav-popover-arrow"></i>
                            @endif
                        </div>
                    </a>

                    <a class="nav-popover-item {{ Request::routeIs('familias.*') ? 'active' : '' }}" href="{{ route('familias.index') }}">
                        <div class="nav-popover-icon bg-teal-subtle">
                            <i class="ti ti-users-group"></i>
                        </div>
                        <div class="nav-popover-text">
                            <span class="nav-popover-item-title">Núcleos Familiares</span>
                            <span class="nav-popover-item-desc">Familias y comunidades</span>
                        </div>
                        <div class="nav-popover-badge">
                            @if(Request::routeIs('familias.*'))
                                <span class="nav-badge-pill">Activo</span>
                            @else
                                <i class="ti ti-chevron-right nav-popover-arrow"></i>
                            @endif
                        </div>
                    </a>

                    <div class="nav-popover-footer">
                        <span class="nav-popover-module">Módulo activo: Registros</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Reportería --}}
        @if(Auth::user()->esAdministrador() || Auth::user()->esDirector())
            <div class="nav-item">
                <a href="{{ route('reportes.index') }}" class="nav-link {{ Request::routeIs('reportes.*') ? 'active' : '' }}">
                    <i class="ti ti-chart-bar"></i>
                    <span>Reportes</span>
                </a>
            </div>
        @endif

        {{-- Administración --}}
        @if(Auth::user()->esAdministrador())
            @php
                $totalAlertasDuplicidad = \App\Models\AlertaDuplicado::count();
            @endphp
            <div class="nav-item dropup nav-item-admin {{ Request::routeIs('alertas.*', 'bitacora.*', 'personal.*') ? 'active' : '' }}">
                <a href="#" class="dropup-toggle nav-link {{ Request::routeIs('alertas.*', 'bitacora.*', 'personal.*') ? 'active' : '' }}"
                   data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                    <i class="ti ti-settings"></i>
                    <span>Admin</span>
                </a>
                <div class="dropdown-menu dropdown-menu-nav-custom">
                    <div class="nav-popover-header">
                        <span class="nav-popover-title">
                            <i class="ti ti-settings"></i> Administración
                        </span>
                        <button type="button" class="nav-popover-close" onclick="const d = bootstrap.Dropdown.getInstance(this.closest('.dropup').querySelector('[data-bs-toggle=dropdown]')); if(d) d.hide();" aria-label="Cerrar">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>

                    <a class="nav-popover-item {{ Request::routeIs('personal.*') ? 'active' : '' }}" href="{{ route('personal.index') }}">
                        <div class="nav-popover-icon bg-blue-subtle">
                            <i class="ti ti-user-cog"></i>
                        </div>
                        <div class="nav-popover-text">
                            <span class="nav-popover-item-title">Gestión de Personal</span>
                            <span class="nav-popover-item-desc">Roles, médicos y turnos</span>
                        </div>
                        <div class="nav-popover-badge">
                            @if(Request::routeIs('personal.*'))
                                <span class="nav-badge-pill">Activo</span>
                            @else
                                <i class="ti ti-chevron-right nav-popover-arrow"></i>
                            @endif
                        </div>
                    </a>

                    <a class="nav-popover-item {{ Request::routeIs('alertas.*') ? 'active' : '' }} {{ $totalAlertasDuplicidad > 0 ? 'highlight-danger' : '' }}" href="{{ route('alertas.index') }}">
                        <div class="nav-popover-icon bg-danger-subtle">
                            <i class="ti ti-bell-alert"></i>
                        </div>
                        <div class="nav-popover-text">
                            <span class="nav-popover-item-title">Alertas Duplicidad</span>
                            <span class="nav-popover-item-desc">Registros duplicados</span>
                        </div>
                        <div class="nav-popover-badge">
                            @if($totalAlertasDuplicidad > 0)
                                <span class="nav-badge-danger">
                                    <span class="dot"></span> {{ $totalAlertasDuplicidad }} nuevas
                                </span>
                            @elseif(Request::routeIs('alertas.*'))
                                <span class="nav-badge-pill">Activo</span>
                            @else
                                <i class="ti ti-chevron-right nav-popover-arrow"></i>
                            @endif
                        </div>
                    </a>

                    <a class="nav-popover-item {{ Request::routeIs('bitacora.*') ? 'active' : '' }}" href="{{ route('bitacora.index') }}">
                        <div class="nav-popover-icon bg-teal-subtle">
                            <i class="ti ti-history"></i>
                        </div>
                        <div class="nav-popover-text">
                            <span class="nav-popover-item-title">Bitácora de Eventos</span>
                            <span class="nav-popover-item-desc">Auditoría de admisiones</span>
                        </div>
                        <div class="nav-popover-badge">
                            @if(Request::routeIs('bitacora.*'))
                                <span class="nav-badge-pill">Activo</span>
                            @else
                                <i class="ti ti-chevron-right nav-popover-arrow"></i>
                            @endif
                        </div>
                    </a>

                    <div class="nav-popover-footer">
                        <span class="nav-popover-module">Módulo activo: Admin</span>
                    </div>
                </div>
            </div>
        @endif
    </nav>
    @endauth

    @auth
    <!-- Modal Confirmación de Cerrar Sesión (Integrado en el Design System SGP) -->
    <div class="modal modal-blur fade" id="modalConfirmarLogout" tabindex="-1" aria-labelledby="modalConfirmarLogoutLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-body text-center py-4 px-4">
                    <h3 class="fw-bold text-dark mb-2" id="modalConfirmarLogoutLabel" style="font-size: 1.15rem;">¿Cerrar Sesión?</h3>
                    <p class="text-secondary small mb-0" style="line-height: 1.45;">
                        ¿Está seguro de que desea salir del sistema SGP Chicamán? Deberá volver a ingresar sus credenciales para acceder.
                    </p>
                </div>
                <div class="modal-footer bg-light border-top-0 d-flex gap-2 justify-content-center p-3">
                    <button type="button" class="btn btn-secondary flex-fill fw-medium" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="flex-fill m-0">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 fw-semibold">
                            <i class="ti ti-logout me-1"></i> Sí, Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endauth

    <!-- Tabler UI JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
    @yield('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var loaderBar = document.getElementById('module-loading-bar');
            
            // Indicador de carga fluido al navegar entre módulos
            document.querySelectorAll('a[href]:not([target="_blank"]):not([href^="#"]):not([href^="javascript"]):not([data-bs-toggle])').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    if (e.ctrlKey || e.shiftKey || e.metaKey || e.defaultPrevented) return;
                    
                    var href = this.getAttribute('href');
                    if (href && href !== window.location.href && !href.startsWith('#')) {
                        if (loaderBar) {
                            loaderBar.style.transition = 'width 0.4s ease-out, opacity 0.2s ease';
                            loaderBar.style.opacity = '1';
                            loaderBar.style.width = '75%';
                        }
                    }
                });
            });

            // Reposicionar automáticamente cualquier modal al <body> para prevenir bloqueos de capa (Stacking Context)
            document.addEventListener('show.bs.modal', function (e) {
                if (e.target && e.target.parentNode !== document.body) {
                    document.body.appendChild(e.target);
                }
            });

            // Transición al confirmar el Cierre de Sesión (Modal Logout)
            var logoutModalEl = document.getElementById('modalConfirmarLogout');
            if (logoutModalEl) {
                var wrapperEl = document.querySelector('.page-wrapper');
                var logoutForm = logoutModalEl.querySelector('form');
                if (logoutForm) {
                    logoutForm.addEventListener('submit', function () {
                        if (loaderBar) {
                            loaderBar.style.opacity = '1';
                            loaderBar.style.width = '80%';
                        }
                        if (wrapperEl) {
                            wrapperEl.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
                            wrapperEl.style.opacity = '0.2';
                            wrapperEl.style.transform = 'translateY(-6px)';
                        }
                    });
                }
            }

            window.addEventListener('pageshow', function (event) {
                if (loaderBar) {
                    loaderBar.style.width = '100%';
                    setTimeout(function() {
                        loaderBar.style.opacity = '0';
                        setTimeout(function() { loaderBar.style.width = '0%'; }, 400);
                    }, 200);
                }
                if (event.persisted || (typeof window.performance != "undefined" && window.performance.navigation.type === 2)) {
                    window.location.reload();
                }
            });

            // Alineación dinámica y control de Backdrop difuminado en popovers móviles
            var mobileBottomNav = document.querySelector('.mobile-bottom-nav');
            var mobileNavBackdrop = document.getElementById('mobileNavBackdrop');
            if (mobileBottomNav) {
                var dropups = mobileBottomNav.querySelectorAll('.dropup');

                dropups.forEach(function (dropup) {
                    dropup.addEventListener('show.bs.dropdown', function () {
                        if (mobileNavBackdrop) {
                            mobileNavBackdrop.classList.add('active');
                        }

                        var toggle = dropup.querySelector('[data-bs-toggle="dropdown"]');
                        var menu = dropup.querySelector('.dropdown-menu-nav-custom');
                        if (!toggle || !menu) return;

                        requestAnimationFrame(function () {
                            var toggleRect = toggle.getBoundingClientRect();
                            var menuRect = menu.getBoundingClientRect();
                            var toggleCenter = toggleRect.left + (toggleRect.width / 2);
                            var arrowLeft = toggleCenter - menuRect.left;
                            var arrowRight = menuRect.right - toggleCenter;

                            var clampedLeft = Math.max(22, Math.min(menuRect.width - 22, arrowLeft));
                            var clampedRight = Math.max(22, Math.min(menuRect.width - 22, arrowRight));

                            menu.style.setProperty('--arrow-left', clampedLeft + 'px');
                            menu.style.setProperty('--arrow-right', clampedRight + 'px');
                        });
                    });

                    dropup.addEventListener('hide.bs.dropdown', function () {
                        setTimeout(function () {
                            var anyOpen = mobileBottomNav.querySelector('.dropup.show, .dropdown-menu.show');
                            if (!anyOpen && mobileNavBackdrop) {
                                mobileNavBackdrop.classList.remove('active');
                            }
                        }, 20);
                    });
                });

                if (mobileNavBackdrop) {
                    mobileNavBackdrop.addEventListener('click', function () {
                        dropups.forEach(function (dropup) {
                            var toggle = dropup.querySelector('[data-bs-toggle="dropdown"]');
                            if (toggle) {
                                var instance = bootstrap.Dropdown.getInstance(toggle);
                                if (instance) instance.hide();
                            }
                        });
                        mobileNavBackdrop.classList.remove('active');
                    });
                }
            }
        });
    </script>
</body>
</html>
