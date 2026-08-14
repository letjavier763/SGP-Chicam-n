@extends('layouts.app')

@section('title', 'Editar Familia')
@section('page_title', 'Editar Núcleo Familiar #' . $familia->numero_familia)

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('familias.show', $familia->id_family) }}" style="color: #0284c7; text-decoration: none; font-weight: 500;">
            ← Volver a la ficha de la familia
        </a>
    </div>

    @if ($errors->any())
        <div style="background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 14px; border-radius: 8px; margin-bottom: 20px;">
            <strong style="display: block; margin-bottom: 6px;">Atención: Revise los siguientes errores:</strong>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card" style="padding: 24px;">
        <form action="{{ route('familias.update', $familia->id_family) }}" method="POST">
            @csrf
            @method('PUT')

            <h4 style="margin: 0 0 16px; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">1. Datos del Núcleo Familiar</h4>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label class="form-label" for="numero_familia">Número de Familia <span style="color: #dc2626;">*</span></label>
                    <input type="text" id="numero_familia" name="numero_familia" class="form-control" value="{{ old('numero_familia', $familia->numero_familia) }}" required>
                </div>
                <div>
                    <label class="form-label" for="apellido_cabeza">Apellido Cabeza de Familia <span style="color: #dc2626;">*</span></label>
                    <input type="text" id="apellido_cabeza" name="apellido_cabeza" class="form-control" value="{{ old('apellido_cabeza', $familia->apellido_cabeza) }}" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                <div>
                    <label class="form-label" for="dpi">DPI Cabeza de Familia (Opcional - 13 dígitos)</label>
                    <input type="text" id="dpi" name="dpi" class="form-control" value="{{ old('dpi', $familia->dpi) }}" maxlength="13">
                </div>
                <div>
                    <label class="form-label" for="fecha_nacimiento">Fecha Nacimiento Cabeza (Opcional)</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', optional($familia->fecha_nacimiento)->format('Y-m-d')) }}">
                </div>
            </div>

            <h4 style="margin: 0 0 16px; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">2. Ubicación Territorial</h4>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                <div>
                    <label class="form-label" for="id_departamento">Departamento <span style="color: #dc2626;">*</span></label>
                    <select id="id_departamento" class="form-control" required>
                        <option value="">-- Seleccionar --</option>
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->id_departamento }}" {{ optional($selectedDepartamento)->id_departamento == $dep->id_departamento ? 'selected' : '' }}>
                                {{ $dep->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" for="id_municipio">Municipio <span style="color: #dc2626;">*</span></label>
                    <select id="id_municipio" class="form-control" required>
                        <option value="">-- Seleccionar --</option>
                        @foreach($municipios as $muni)
                            <option value="{{ $muni->id_municipio }}" {{ optional($selectedMunicipio)->id_municipio == $muni->id_municipio ? 'selected' : '' }}>
                                {{ $muni->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" for="id_comunidad">Comunidad <span style="color: #dc2626;">*</span></label>
                    <select id="id_comunidad" name="id_comunidad" class="form-control" required>
                        <option value="">-- Seleccionar --</option>
                        @foreach($comunidades as $com)
                            <option value="{{ $com->id_comunidad }}" {{ optional($selectedComunidad)->id_comunidad == $com->id_comunidad ? 'selected' : '' }}>
                                {{ $com->nombre }} {{ $com->zona ? '('.$com->zona.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <a href="{{ route('familias.show', $familia->id_family) }}" class="btn" style="background: #e2e8f0; color: #475569;">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar Familia</button>
            </div>
        </form>
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
});
</script>
@endsection
