@extends('layouts.app')

@section('title', 'Registrar Nuevo Personal')
@section('page_title', 'Registrar Nuevo Personal')

@section('content')
<div class="mb-3">
    <a href="{{ route('personal.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i> Volver al listado
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar bg-primary-lt text-primary rounded-circle">
                        <i class="ti ti-user-plus fs-2"></i>
                    </span>
                    <div>
                        <h3 class="card-title fw-bold text-dark mb-0">Información del Nuevo Personal</h3>
                        <div class="text-secondary small">Complete los datos de la cuenta y sus permisos</div>
                    </div>
                </div>
            </div>
            <form action="{{ route('personal.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger shadow-sm mb-3" role="alert">
                            <div class="d-flex">
                                <i class="ti ti-alert-triangle me-2 fs-2 flex-shrink-0"></i>
                                <div>
                                    <strong class="d-block mb-1">Por favor corrige los siguientes errores:</strong>
                                    <ul class="mb-0 ps-3">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label required fw-medium" for="nombre_completo">Nombre Completo</label>
                        <input type="text" id="nombre_completo" name="nombre_completo" class="form-control @error('nombre_completo') is-invalid @enderror" value="{{ old('nombre_completo') }}" required placeholder="Ej: María Mercedes Gómez Pérez">
                        <small class="form-hint text-muted">Nombre y apellidos oficiales del trabajador o colaborador.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required fw-medium" for="username">Nombre de Usuario</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="ti ti-at text-secondary"></i>
                            </span>
                            <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required placeholder="mgomez" autocomplete="off">
                        </div>
                        <small class="form-hint text-muted">Identificador único que se usará para iniciar sesión en SGP.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required fw-medium" for="id_rol">Rol asignado</label>
                        <select id="id_rol" name="id_rol" class="form-select @error('id_rol') is-invalid @enderror" required>
                            <option value="">-- Seleccionar Rol --</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id_rol }}" {{ old('id_rol') == $rol->id_rol ? 'selected' : '' }}>
                                    {{ $rol->nombre_rol }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-hint text-muted">Define los accesos y privilegios del usuario dentro de los módulos.</small>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-12 col-sm-6">
                            <label class="form-label required fw-medium" for="password">Contraseña</label>
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Mínimo 6 caracteres" autocomplete="new-password">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label required fw-medium" for="password_confirmation">Confirmar Contraseña</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Repita la contraseña" autocomplete="new-password">
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light d-flex justify-content-between align-items-center py-2 px-3">
                    <a href="{{ route('personal.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="ti ti-device-floppy me-1"></i> Guardar y Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
