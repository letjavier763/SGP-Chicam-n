@extends('layouts.app')

@section('title', 'Registrar Paciente')
@section('page_title', 'Registrar Nuevo Paciente')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="mb-3">
            <a href="{{ route('pacientes.index') }}" class="btn btn-link text-decoration-none p-0">
                <i class="ti ti-arrow-left me-1"></i> Volver a la lista de pacientes
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
                <h3 class="card-title text-primary"><i class="ti ti-user-plus me-2"></i> Formulario de Registro de Paciente</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('pacientes.store') }}" method="POST" id="pacienteForm">
                    @csrf

                    <h4 class="mb-3 text-secondary border-bottom pb-2">1. Adscripción al Núcleo Familiar / Expediente</h4>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label required" for="id_family">Núcleo Familiar / Expediente Compartido</label>
                            <select id="id_family" name="id_family" class="form-select" required>
                                <option value="">-- Seleccione un núcleo familiar registrado --</option>
                                @foreach($familias as $fam)
                                    <option value="{{ $fam->id_family }}" data-numero-familia="{{ $fam->numero_familia }}" {{ (old('id_family', $selectedFamilyId) == $fam->id_family) ? 'selected' : '' }}>
                                        No. Familia: {{ $fam->numero_familia }} — Cabeza: {{ $fam->apellido_cabeza }} ({{ $fam->comunidad->nombre ?? 'Sin comunidad' }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="form-hint">¿No encuentra el núcleo familiar? <a href="{{ route('familias.create') }}" target="_blank">Registrar nueva familia</a></span>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="numero_expediente_fisico">No. Expediente Físico</label>
                            <input type="text" id="numero_expediente_fisico" name="numero_expediente_fisico" class="form-control bg-light" value="{{ old('numero_expediente_fisico') }}" readonly placeholder="Se asigna según la familia">
                            <span class="form-hint small text-muted">Hereda el número del núcleo familiar</span>
                        </div>
                    </div>

                    <h4 class="mb-3 text-secondary border-bottom pb-2">2. Información Personal del Paciente</h4>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="nombres">Nombres</label>
                            <input type="text" id="nombres" name="nombres" class="form-control" value="{{ old('nombres') }}" required placeholder="Ej: María Mercedes">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="apellidos">Apellidos</label>
                            <input type="text" id="apellidos" name="apellidos" class="form-control" value="{{ old('apellidos') }}" required placeholder="Ej: Gómez Pérez">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label required" for="fecha_nacimiento">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required" for="sexo">Sexo</label>
                            <select id="sexo" name="sexo" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                                <option value="M" {{ old('sexo') === 'M' ? 'selected' : '' }}>Masculino (M)</option>
                                <option value="F" {{ old('sexo') === 'F' ? 'selected' : '' }}>Femenino (F)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="dpi">DPI (Opcional - 13 dígitos)</label>
                            <input type="text" id="dpi" name="dpi" class="form-control" value="{{ old('dpi') }}" maxlength="13" placeholder="Ej: 1987654320101">
                            <span id="msg-dup-dpi" class="form-hint fw-bold"></span>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="telefono">Teléfono de Contacto (Opcional - 8 dígitos)</label>
                            <input type="text" id="telefono" name="telefono" class="form-control" value="{{ old('telefono') }}" maxlength="8" placeholder="Ej: 55551234">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('pacientes.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> Guardar Paciente</button>
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

    function updateExpediente() {
        const selectedOpt = familySelect.options[familySelect.selectedIndex];
        if (selectedOpt && selectedOpt.dataset.numeroFamilia) {
            expInput.value = selectedOpt.dataset.numeroFamilia;
        } else {
            expInput.value = '';
        }
    }

    familySelect.addEventListener('change', updateExpediente);
    updateExpediente();

    const dpiInput = document.getElementById('dpi');
    const msgEl = document.getElementById('msg-dup-dpi');

    dpiInput.addEventListener('blur', function () {
        const val = this.value.trim();
        if (!val) {
            msgEl.textContent = '';
            return;
        }

        fetch(`/pacientes/verificar-duplicado?tipo=dpi&valor=${encodeURIComponent(val)}`)
            .then(res => res.json())
            .then(data => {
                if (data.duplicate) {
                    msgEl.className = 'form-hint text-danger fw-bold';
                    msgEl.textContent = '⚠ ' + data.message;
                } else {
                    msgEl.className = 'form-hint text-success fw-bold';
                    msgEl.textContent = '✓ Disponible';
                }
            });
    });
});
</script>
@endsection
