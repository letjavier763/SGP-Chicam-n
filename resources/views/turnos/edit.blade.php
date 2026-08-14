@extends('layouts.app')

@section('title', 'Editar Turno')
@section('page_title', 'Editar Turno #' . $turno->id_turno)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-clock-edit me-2 text-warning"></i> Editar Turno
                </h3>
            </div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('turnos.update', $turno->id_turno) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="id_usuario" class="form-label required">Personal Asignado</label>
                            <select id="id_usuario" name="id_usuario"
                                    class="form-select @error('id_usuario') is-invalid @enderror" required>
                                <option value="">— Seleccione un usuario —</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id_usuario }}"
                                        {{ old('id_usuario', $turno->id_usuario) == $usuario->id_usuario ? 'selected' : '' }}>
                                        {{ $usuario->nombre_completo }} ({{ $usuario->rol->nombre_rol }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_usuario') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="fecha" class="form-label required">Fecha del Turno</label>
                            <input type="date" id="fecha" name="fecha"
                                   class="form-control @error('fecha') is-invalid @enderror"
                                   value="{{ old('fecha', $turno->fecha->toDateString()) }}" required>
                            @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="tipo_turno" class="form-label required">Tipo de Turno</label>
                            <select id="tipo_turno" name="tipo_turno"
                                    class="form-select @error('tipo_turno') is-invalid @enderror" required>
                                <option value="">— Seleccione —</option>
                                @foreach(['matutino', 'vespertino', 'nocturno'] as $tipo)
                                    <option value="{{ $tipo }}"
                                        {{ old('tipo_turno', $turno->tipo_turno) === $tipo ? 'selected' : '' }}>
                                        {{ ucfirst($tipo) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_turno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="hora_inicio" class="form-label required">Hora de Inicio</label>
                            <input type="time" id="hora_inicio" name="hora_inicio"
                                   class="form-control @error('hora_inicio') is-invalid @enderror"
                                   value="{{ old('hora_inicio', $turno->hora_inicio) }}" required>
                            @error('hora_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="hora_fin" class="form-label required">Hora de Fin</label>
                            <input type="time" id="hora_fin" name="hora_fin"
                                   class="form-control @error('hora_fin') is-invalid @enderror"
                                   value="{{ old('hora_fin', $turno->hora_fin) }}" required>
                            @error('hora_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea id="observaciones" name="observaciones"
                                      class="form-control @error('observaciones') is-invalid @enderror"
                                      rows="3">{{ old('observaciones', $turno->observaciones) }}</textarea>
                            @error('observaciones') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('turnos.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="ti ti-device-floppy me-1"></i> Actualizar Turno
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
