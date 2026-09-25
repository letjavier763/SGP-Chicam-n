@extends('layouts.app')

@section('title', 'Editar Personal')
@section('page_title', 'Editar Información de Personal')

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
                        <i class="ti ti-user-edit fs-2"></i>
                    </span>
                    <div>
                        <h3 class="card-title fw-bold text-dark mb-0">Editar Usuario: {{ $usuario->username }}</h3>
                        <div class="text-secondary small">Modifique los permisos o credenciales del usuario</div>
                    </div>
                </div>
            </div>
            <form action="{{ route('personal.update', $usuario->id_usuario) }}" method="POST">
                @csrf
                @method('PUT')
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
                        <input type="text" id="nombre_completo" name="nombre_completo" class="form-control @error('nombre_completo') is-invalid @enderror" value="{{ old('nombre_completo', $usuario->nombre_completo) }}" required placeholder="Ej: María Mercedes Gómez Pérez">
                        <small class="form-hint text-muted">Nombre y apellidos oficiales del trabajador.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required fw-medium" for="username">Nombre de Usuario</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="ti ti-at text-secondary"></i>
                            </span>
                            <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $usuario->username) }}" required placeholder="mgomez">
                        </div>
                        <small class="form-hint text-muted">Nombre de usuario único para acceso al sistema.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required fw-medium" for="id_rol">Rol asignado</label>
                        <select id="id_rol" name="id_rol" class="form-select @error('id_rol') is-invalid @enderror" required>
                            <option value="">-- Seleccionar Rol --</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id_rol }}" {{ old('id_rol', $usuario->id_rol) == $rol->id_rol ? 'selected' : '' }}>
                                    {{ $rol->nombre_rol }} &mdash; {{ $rol->descripcion }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-hint text-muted">Define los accesos y privilegios del usuario dentro de la plataforma.</small>
                    </div>

                    <div class="card bg-yellow-lt mb-3 p-2.5 border-0 rounded-2">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-key text-warning fs-2 me-2 flex-shrink-0"></i>
                            <div>
                                <strong class="d-block text-dark small fw-bold">¿Desea cambiar la contraseña?</strong>
                                <span class="text-secondary" style="font-size: 0.775rem;">Deje los campos de contraseña en blanco si desea conservar la clave actual.</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-medium" for="password">Nueva Contraseña</label>
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Mínimo 6 caracteres (opcional)" autocomplete="new-password">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-medium" for="password_confirmation">Confirmar Contraseña</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Repita la contraseña" autocomplete="new-password">
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light d-flex justify-content-between align-items-center py-2 px-3">
                    <a href="{{ route('personal.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="ti ti-device-floppy me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
