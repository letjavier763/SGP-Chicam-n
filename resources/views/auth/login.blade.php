<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Acceso seguro al Sistema de Gestión de Pacientes del CAP Chicamán.">
    <title>Iniciar Sesión | SGP CAP Chicamán</title>
    
    <!-- Tabler UI Core CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=3">
</head>
<body class="d-flex flex-column bg-body-tertiary min-vh-100 justify-content-center">

    <div class="page page-center">
        <div class="container container-tight py-4">
            
            <div class="text-center mb-4">
                <a href="#" class="navbar-brand navbar-brand-autodark d-inline-flex align-items-center gap-2 text-primary fs-1 fw-bold text-decoration-none">
                    <span>SGP CAP Chicamán</span>
                </a>
            </div>

            <div class="card card-md shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h2 text-center mb-1 text-dark fw-bold">Iniciar Sesión</h2>
                    <p class="text-secondary text-center mb-4">Ingresa tus credenciales para acceder al sistema</p>

                    <!-- Alertas Generales -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible mb-3" role="alert">
                            <div class="d-flex">
                                <div><i class="ti ti-alert-circle me-2 fs-2"></i></div>
                                <div>
                                    @foreach ($errors->all() as $error)
                                        <p class="mb-0">{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST" autocomplete="off">
                        @csrf

                        <!-- Campo de Usuario -->
                        <div class="mb-3">
                            <label for="username" class="form-label required">Nombre de Usuario</label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="ti ti-user"></i>
                                </span>
                                <input 
                                    type="text" 
                                    name="username" 
                                    id="username" 
                                    class="form-control @error('username') is-invalid @enderror" 
                                    value="{{ old('username') }}" 
                                    placeholder="Ej: admin"
                                    required 
                                    autofocus
                                >
                            </div>
                        </div>

                        <!-- Campo de Contraseña -->
                        <div class="mb-3">
                            <label for="password" class="form-label required">Contraseña</label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="ti ti-lock"></i>
                                </span>
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="password" 
                                    class="form-control" 
                                    placeholder="••••••••"
                                    required
                                >
                            </div>
                        </div>

                        <!-- Botón de Envío -->
                        <div class="form-footer mt-4">
                            <button type="submit" class="btn btn-primary w-100 py-2 fs-3 fw-bold">
                                <i class="ti ti-login me-2"></i> Iniciar Sesión
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center text-secondary mt-3 small">
                SGP Chicamán &copy; {{ date('Y') }} — Centro de Atención Permanente
            </div>
        </div>
    </div>

    <!-- Tabler UI JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
</body>
</html>
