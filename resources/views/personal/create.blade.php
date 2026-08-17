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
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title fw-bold">
                    <i class="ti ti-user-plus me-2"></i>
                    Información del Nuevo Personal
                </h3>
            </div>
            <form action="{{ route('personal.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <div class="d-flex">
                                <div><i class="ti ti-alert-triangle me-2 fs-2"></i></div>
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
                        <label class="form-label required" for="nombre_completo">Nombre Completo</label>
                        <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" value="{{ old('nombre_completo') }}" required placeholder="Ej: María Mercedes Gómez Pérez">
                        <small class="form-hint">Ingrese el nombre completo del personal.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required" for="username">Nombre de Usuario</label>
                        <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}" required placeholder="Ej: mgomez">
                        <small class="form-hint">Este nombre se usará para iniciar sesión en el sistema.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required" for="id_rol">Rol asignado</label>
                        <select id="id_rol" name="id_rol" class="form-select" required>
                            <option value="">-- Seleccionar Rol --</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id_rol }}" {{ old('id_rol') == $rol->id_rol ? 'selected' : '' }}>
                                    {{ $rol->nombre_rol }} &mdash; {{ $rol->descripcion }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-hint">Define los accesos y privilegios del usuario dentro de la plataforma.</small>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="password">Contraseña</label>
                            <input type="password" id="password" name="password" class="form-control" required placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="password_confirmation">Confirmar Contraseña</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Repita la contraseña">
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light text-end">
                    <a href="{{ route('personal.index') }}" class="btn btn-link link-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Guardar y Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
