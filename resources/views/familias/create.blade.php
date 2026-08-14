@extends('layouts.app')

@section('title', 'Registrar Familia')
@section('page_title', 'Registrar Nuevo Núcleo Familiar')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="mb-3">
            <a href="{{ route('familias.index') }}" class="btn btn-link text-decoration-none p-0">
                <i class="ti ti-arrow-left me-1"></i> Volver a la lista de familias
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
                <h3 class="card-title text-primary"><i class="ti ti-home-plus me-2"></i> Formulario de Registro de Familia</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('familias.store') }}" method="POST" id="famForm">
                    @csrf

                    <h4 class="mb-3 text-secondary border-bottom pb-2">1. Datos del Núcleo Familiar</h4>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="numero_familia">Número de Familia</label>
                            <input type="text" id="numero_familia" name="numero_familia" class="form-control" value="{{ old('numero_familia') }}" required placeholder="Ej: FAM-2026-001">
                            <span id="msg-dup-numero_familia" class="form-hint fw-bold"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="apellido_cabeza">Apellido Cabeza de Familia</label>
                            <input type="text" id="apellido_cabeza" name="apellido_cabeza" class="form-control" value="{{ old('apellido_cabeza') }}" required placeholder="Ej: Pérez Ramos">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="dpi">DPI Cabeza de Familia (Opcional - 13 dígitos)</label>
                            <input type="text" id="dpi" name="dpi" class="form-control" value="{{ old('dpi') }}" maxlength="13" placeholder="Ej: 1234567890101">
                            <span id="msg-dup-dpi" class="form-hint fw-bold"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="fecha_nacimiento">Fecha Nacimiento Cabeza (Opcional)</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}">
                        </div>
                    </div>

                    <h4 class="mb-3 text-secondary border-bottom pb-2">2. Ubicación Territorial</h4>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label required" for="id_departamento">Departamento</label>
                            <select id="id_departamento" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                                @foreach($departamentos as $dep)
                                    <option value="{{ $dep->id_departamento }}">{{ $dep->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required" for="id_municipio">Municipio</label>
                            <select id="id_municipio" class="form-select" required disabled>
                                <option value="">-- Seleccionar Depto Primero --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required" for="id_comunidad">Comunidad</label>
                            <select id="id_comunidad" name="id_comunidad" class="form-select" required disabled>
                                <option value="">-- Seleccionar Muni Primero --</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('familias.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> Guardar Familia</button>
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
    const deptoSelect = document.getElementById('id_departamento');
    const muniSelect = document.getElementById('id_municipio');
    const comSelect = document.getElementById('id_comunidad');

    deptoSelect.addEventListener('change', function () {
        const deptoId = this.value;
        muniSelect.innerHTML = '<option value="">-- Cargando... --</option>';
        muniSelect.disabled = true;
        comSelect.innerHTML = '<option value="">-- Seleccionar Muni Primero --</option>';
        comSelect.disabled = true;

        if (!deptoId) return;

        fetch(`/api/ubicaciones/municipios/${deptoId}`)
            .then(res => res.json())
            .then(data => {
                muniSelect.innerHTML = '<option value="">-- Seleccionar Municipio --</option>';
                data.forEach(muni => {
                    muniSelect.innerHTML += `<option value="${muni.id_municipio}">${muni.nombre}</option>`;
                });
                muniSelect.disabled = false;
            });
    });

    muniSelect.addEventListener('change', function () {
        const muniId = this.value;
        comSelect.innerHTML = '<option value="">-- Cargando... --</option>';
        comSelect.disabled = true;

        if (!muniId) return;

        fetch(`/api/ubicaciones/comunidades/${muniId}`)
            .then(res => res.json())
            .then(data => {
                comSelect.innerHTML = '<option value="">-- Seleccionar Comunidad --</option>';
                data.forEach(com => {
                    const zonaText = com.zona ? ` (${com.zona})` : '';
                    comSelect.innerHTML += `<option value="${com.id_comunidad}">${com.nombre}${zonaText}</option>`;
                });
                comSelect.disabled = false;
            });
    });

    const numFamInput = document.getElementById('numero_familia');
    const dpiInput = document.getElementById('dpi');

    function checkDuplicateField(input, tipo, msgElId) {
        const val = input.value.trim();
        const msgEl = document.getElementById(msgElId);
        if (!val) {
            msgEl.textContent = '';
            return;
        }

        fetch(`/pacientes/verificar-duplicado?tipo=${tipo}&valor=${encodeURIComponent(val)}`)
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
    }

    numFamInput.addEventListener('blur', () => checkDuplicateField(numFamInput, 'numero_familia', 'msg-dup-numero_familia'));
    dpiInput.addEventListener('blur', () => checkDuplicateField(dpiInput, 'dpi', 'msg-dup-dpi'));
});
</script>
@endsection
