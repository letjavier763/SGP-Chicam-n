@extends('layouts.app')

@section('title', 'Nuevo Turno')
@section('page_title', 'Crear Nuevo Turno')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-clock-plus me-2 text-primary"></i> Datos del Turno
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

                <form action="{{ route('turnos.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="row g-3">
                        {{-- Personal asignado --}}
                        <div class="col-md-12">
                            <label for="id_usuario" class="form-label required">Personal Asignado</label>
                            <select id="id_usuario" name="id_usuario"
                                    class="form-select @error('id_usuario') is-invalid @enderror" required>
                                <option value="">— Seleccione un usuario —</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id_usuario }}"
                                        {{ old('id_usuario') == $usuario->id_usuario ? 'selected' : '' }}>
                                        {{ $usuario->nombre_completo }} ({{ $usuario->rol->nombre_rol }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_usuario')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Fecha --}}
                        <div class="col-md-6">
                            <label for="fecha" class="form-label required">Fecha del Turno</label>
                            <input type="date" id="fecha" name="fecha"
                                   class="form-control @error('fecha') is-invalid @enderror"
                                   value="{{ old('fecha', today()->toDateString()) }}" required>
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tipo de turno --}}
                        <div class="col-md-6">
                            <label for="tipo_turno" class="form-label required">Tipo de Turno</label>
                            <select id="tipo_turno" name="tipo_turno"
                                    class="form-select @error('tipo_turno') is-invalid @enderror" required>
                                <option value="">— Seleccione —</option>
                                <option value="matutino"   {{ old('tipo_turno') === 'matutino'   ? 'selected' : '' }}>Matutino (6:00 – 14:00)</option>
                                <option value="vespertino" {{ old('tipo_turno') === 'vespertino' ? 'selected' : '' }}>Vespertino (14:00 – 22:00)</option>
                                <option value="nocturno"   {{ old('tipo_turno') === 'nocturno'   ? 'selected' : '' }}>Nocturno (22:00 – 6:00)</option>
                            </select>
                            @error('tipo_turno')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Hora inicio --}}
                        <div class="col-md-6">
                            <label for="hora_inicio" class="form-label required">Hora de Inicio</label>
                            <input type="time" id="hora_inicio" name="hora_inicio"
                                   class="form-control @error('hora_inicio') is-invalid @enderror"
                                   value="{{ old('hora_inicio', '06:00') }}" required>
                            @error('hora_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Hora fin --}}
                        <div class="col-md-6">
                            <label for="hora_fin" class="form-label required">Hora de Fin</label>
                            <input type="time" id="hora_fin" name="hora_fin"
                                   class="form-control @error('hora_fin') is-invalid @enderror"
                                   value="{{ old('hora_fin', '14:00') }}" required>
                            @error('hora_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Observaciones --}}
                        <div class="col-12">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea id="observaciones" name="observaciones"
                                      class="form-control @error('observaciones') is-invalid @enderror"
                                      rows="3" placeholder="Notas adicionales sobre el turno…">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('turnos.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Guardar Turno
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto-rellenar horas según tipo de turno
document.getElementById('tipo_turno').addEventListener('change', function () {
    const horarios = {
        matutino:   { inicio: '06:00', fin: '14:00' },
        vespertino: { inicio: '14:00', fin: '22:00' },
        nocturno:   { inicio: '22:00', fin: '06:00' },
    };
    const sel = horarios[this.value];
    if (sel) {
        document.getElementById('hora_inicio').value = sel.inicio;
        document.getElementById('hora_fin').value    = sel.fin;
    }
});
</script>
@endsection
