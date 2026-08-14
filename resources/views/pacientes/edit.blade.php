@extends('layouts.app')

@section('title', 'Editar Paciente')
@section('page_title', 'Editar Paciente: ' . $paciente->nombres . ' ' . $paciente->apellidos)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="mb-3">
            <a href="{{ route('pacientes.show', $paciente->id_paciente) }}" class="btn btn-link text-decoration-none p-0">
                <i class="ti ti-arrow-left me-1"></i> Volver a la ficha del paciente
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <div class="fw-bold mb-1">Atención: Revise los siguientes errores:</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title text-primary"><i class="ti ti-edit me-2"></i> Editar Ficha del Paciente</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('pacientes.update', $paciente->id_paciente) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h4 class="mb-3 text-secondary border-bottom pb-2">1. Adscripción al Núcleo Familiar / Expediente</h4>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label required" for="id_family">Núcleo Familiar / Expediente Compartido</label>
                            <select id="id_family" name="id_family" class="form-select" required>
                                <option value="">-- Seleccione una familia registrada --</option>
                                @foreach($familias as $fam)
                                    <option value="{{ $fam->id_family }}" data-numero-familia="{{ $fam->numero_familia }}" {{ (old('id_family', $paciente->id_family) == $fam->id_family) ? 'selected' : '' }}>
                                        No. Familia: {{ $fam->numero_familia }} — Cabeza: {{ $fam->apellido_cabeza }} ({{ $fam->comunidad->nombre ?? 'Sin comunidad' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="numero_expediente_fisico">No. Expediente Físico</label>
                            <input type="text" id="numero_expediente_fisico" name="numero_expediente_fisico" class="form-control bg-light" value="{{ old('numero_expediente_fisico', $paciente->numero_expediente_fisico) }}" readonly>
                        </div>
                    </div>

                    <h4 class="mb-3 text-secondary border-bottom pb-2">2. Información Personal del Paciente</h4>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="nombres">Nombres</label>
                            <input type="text" id="nombres" name="nombres" class="form-control" value="{{ old('nombres', $paciente->nombres) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="apellidos">Apellidos</label>
                            <input type="text" id="apellidos" name="apellidos" class="form-control" value="{{ old('apellidos', $paciente->apellidos) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label required" for="fecha_nacimiento">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', optional($paciente->fecha_nacimiento)->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required" for="sexo">Sexo</label>
                            <select id="sexo" name="sexo" class="form-select" required>
                                <option value="M" {{ old('sexo', $paciente->sexo) === 'M' ? 'selected' : '' }}>Masculino (M)</option>
                                <option value="F" {{ old('sexo', $paciente->sexo) === 'F' ? 'selected' : '' }}>Femenino (F)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="dpi">DPI (Opcional - 13 dígitos)</label>
                            <input type="text" id="dpi" name="dpi" class="form-control" value="{{ old('dpi', $paciente->dpi) }}" maxlength="13">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="telefono">Teléfono (Opcional - 8 dígitos)</label>
                            <input type="text" id="telefono" name="telefono" class="form-control" value="{{ old('telefono', $paciente->telefono) }}" maxlength="8">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('pacientes.show', $paciente->id_paciente) }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> Actualizar Paciente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const familySelect = document.getElementById('id_family');
    const expInput = document.getElementById('numero_expediente_fisico');

    familySelect.addEventListener('change', function () {
        const selectedOpt = this.options[this.selectedIndex];
        if (selectedOpt && selectedOpt.dataset.numeroFamilia) {
            expInput.value = selectedOpt.dataset.numeroFamilia;
        }
    });
});
</script>
@endsection
