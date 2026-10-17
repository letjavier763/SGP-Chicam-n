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
                <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center gap-2" style="border-radius: 8px;">
                    <i class="ti ti-info-circle fs-3 text-info"></i>
                    <div class="small">
                        <strong>Modo Flexible Habilitado:</strong> Puede ingresar pacientes con datos incompletos (sin fecha de nacimiento, sin apellido, sin DPI completo o sin familia asignada) para facilitar la transcripción de fichas y expedientes físicos.
                    </div>
                </div>

                <form action="{{ route('pacientes.store') }}" method="POST" id="pacienteForm">
                    @csrf

                    <h4 class="mb-3 text-secondary border-bottom pb-2">1. Adscripción al Núcleo Familiar / Expediente</h4>

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label" for="id_family">
                                Núcleo Familiar / Expediente Compartido 
                                <span class="badge bg-light text-secondary ms-1">Opcional</span>
                            </label>
                            <select id="id_family" name="id_family" class="form-select">
                                <option value="">-- Sin núcleo familiar asignado (Se creará/asignará expediente general S/F) --</option>
                                @foreach($familias as $fam)
                                    <option value="{{ $fam->id_family }}" data-numero-familia="{{ $fam->numero_familia }}" {{ (old('id_family', $selectedFamilyId) == $fam->id_family) ? 'selected' : '' }}>
                                        No. Familia: {{ $fam->numero_familia }} — Cabeza: {{ $fam->apellido_cabeza }} ({{ $fam->comunidad->nombre ?? 'Sin comunidad' }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="form-hint">Si no selecciona una familia existente, puede indicar un número nuevo o dejarlo vacío para asignar a expediente general.</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="numero_familia">O Escriba No. de Familia / Expediente Nuevo <span class="badge bg-light text-secondary ms-1">Opcional</span></label>
                            <input type="text" id="numero_familia" name="numero_familia" class="form-control" value="{{ old('numero_familia') }}" placeholder="Ej: 17569 (si es una familia nueva)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="numero_expediente_fisico">No. Expediente Físico en Archivo <span class="badge bg-light text-secondary ms-1">Opcional</span></label>
                            <input type="text" id="numero_expediente_fisico" name="numero_expediente_fisico" class="form-control" value="{{ old('numero_expediente_fisico') }}" placeholder="Ej: 17569 o S/N">
                        </div>
                    </div>

                    <h4 class="mb-3 text-secondary border-bottom pb-2">2. Información Personal del Paciente</h4>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="nombres">Nombres <span class="badge bg-light text-secondary ms-1">Opcional</span></label>
                            <input type="text" id="nombres" name="nombres" class="form-control" value="{{ old('nombres') }}" placeholder="Ej: María Mercedes (opcional)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="apellidos">Apellidos <span class="badge bg-light text-secondary ms-1">Opcional</span></label>
                            <input type="text" id="apellidos" name="apellidos" class="form-control" value="{{ old('apellidos') }}" placeholder="Ej: Gómez Pérez (opcional)">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label" for="fecha_nacimiento">Fecha de Nacimiento <span class="badge bg-light text-secondary ms-1">Opcional</span></label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="sexo">Sexo <span class="badge bg-light text-secondary ms-1">Opcional</span></label>
                            <select id="sexo" name="sexo" class="form-select">
                                <option value="">-- Sin especificar / Opcional --</option>
                                <option value="M" {{ old('sexo') === 'M' ? 'selected' : '' }}>Masculino (M)</option>
                                <option value="F" {{ old('sexo') === 'F' ? 'selected' : '' }}>Femenino (F)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="dpi">DPI / CUI <span class="badge bg-light text-secondary ms-1">Opcional</span></label>
                            <input type="text" id="dpi" name="dpi" class="form-control" value="{{ old('dpi') }}" inputmode="numeric" maxlength="20" placeholder="Ej: 1987654320101 (completo o parcial)">
                            <span id="msg-dup-dpi" class="form-hint fw-bold"></span>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="telefono">Teléfono de Contacto <span class="badge bg-light text-secondary ms-1">Opcional</span></label>
                            <input type="text" id="telefono" name="telefono" class="form-control" value="{{ old('telefono') }}" maxlength="20" placeholder="Ej: 55551234 (opcional)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="direccion">Dirección o Aldea <span class="badge bg-light text-secondary ms-1">Opcional</span></label>
                            <input type="text" id="direccion" name="direccion" class="form-control" value="{{ old('direccion') }}" maxlength="255" placeholder="Ej: Caserío El Centro, Sector 2 (opcional)">
                        </div>
                    </div>

                    <h4 class="mb-3 text-secondary border-bottom pb-2">3. Datos de Ficha / Expediente Físico</h4>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label" for="numero_registro"># Ficha o Expediente <span class="badge bg-light text-secondary ms-1">Opcional</span></label>
                            <input type="text" id="numero_registro" name="numero_registro" class="form-control" value="{{ old('numero_registro') }}" inputmode="numeric" pattern="[0-9]*" placeholder="Ej: 1024">
                            <span id="msg-dup-reg" class="form-hint fw-bold"></span>
                            <span class="form-hint text-muted">Número de ficha o expediente físico</span>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" for="descripcion_registro">Descripción / Ubicación del Registro <span class="badge bg-light text-secondary ms-1">Opcional</span></label>
                            <textarea id="descripcion_registro" name="descripcion_registro" class="form-control auto-expand-textarea" rows="1" maxlength="150" placeholder="Ej: Archivero 3, Cajón B, Folder amarillo">{{ old('descripcion_registro') }}</textarea>
                            <span class="form-hint">Máx. 150 caracteres — indica dónde se encuentra el expediente físico</span>
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

    const dpiInput = document.getElementById('dpi');
    const msgDpiEl = document.getElementById('msg-dup-dpi');
    const regInput = document.getElementById('numero_registro');
    const msgRegEl = document.getElementById('msg-dup-reg');
    const form = document.getElementById('pacienteForm');

    let dpiDuplicado = false;
    let regDuplicado = false;

    if (dpiInput && msgDpiEl) {
        function verificarDpi(val) {
            fetch(`/pacientes/verificar-duplicado?tipo=dpi&valor=${encodeURIComponent(val)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.duplicate) {
                        dpiDuplicado = true;
                        dpiInput.classList.add('is-invalid');
                        msgDpiEl.className = 'form-hint text-danger fw-bold';
                        msgDpiEl.textContent = '⚠ ' + data.message;
                    } else if (data.incomplete) {
                        dpiDuplicado = false;
                        dpiInput.classList.add('is-invalid');
                        msgDpiEl.className = 'form-hint text-warning fw-bold';
                        msgDpiEl.textContent = '⚠ ' + data.message;
                    } else {
                        dpiDuplicado = false;
                        dpiInput.classList.remove('is-invalid');
                        msgDpiEl.className = 'form-hint text-success fw-bold';
                        msgDpiEl.textContent = '✓ DPI válido (13 dígitos) y disponible';
                    }
                })
                .catch(() => {
                    dpiDuplicado = false;
                });
        }

        dpiInput.addEventListener('input', function() {
            let clean = this.value.replace(/\D/g, '');
            if (clean.length > 13) clean = clean.slice(0, 13);
            if (this.value !== clean) {
                this.value = clean;
            }

            dpiDuplicado = false;
            dpiInput.classList.remove('is-invalid');

            if (!clean) {
                msgDpiEl.textContent = '';
                return;
            }

            if (clean.length < 13) {
                msgDpiEl.className = 'form-hint text-warning fw-bold';
                msgDpiEl.textContent = `⚠ Incompleto: ${clean.length} de 13 dígitos`;
            } else if (clean.length === 13) {
                msgDpiEl.className = 'form-hint text-muted';
                msgDpiEl.textContent = 'Verificando DPI...';
                verificarDpi(clean);
            }
        });

        dpiInput.addEventListener('blur', function () {
            let val = this.value.replace(/\D/g, '').trim();
            if (this.value !== val) this.value = val;
            if (!val) {
                msgDpiEl.textContent = '';
                dpiDuplicado = false;
                dpiInput.classList.remove('is-invalid');
                return;
            }
            if (val.length !== 13) {
                dpiInput.classList.remove('is-invalid');
                msgDpiEl.className = 'form-hint text-warning fw-semibold';
                msgDpiEl.textContent = `ℹ DPI incompleto (${val.length} dígitos) - se guardará como parcial.`;
                return;
            }
            verificarDpi(val);
        });

        if (dpiInput.value) {
            dpiInput.dispatchEvent(new Event('input'));
        }
    }

    if (regInput && msgRegEl) {
        regInput.addEventListener('input', function() {
            regDuplicado = false;
            regInput.classList.remove('is-invalid');
            msgRegEl.textContent = '';
        });

        regInput.addEventListener('blur', function () {
            const val = this.value.trim();
            if (!val) {
                msgRegEl.textContent = '';
                regDuplicado = false;
                regInput.classList.remove('is-invalid');
                return;
            }

            fetch(`/pacientes/verificar-duplicado?tipo=numero_registro&valor=${encodeURIComponent(val)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.duplicate) {
                        regDuplicado = true;
                        regInput.classList.add('is-invalid');
                        msgRegEl.className = 'form-hint text-danger fw-bold';
                        msgRegEl.textContent = '⚠ ' + data.message;
                    } else {
                        regDuplicado = false;
                        regInput.classList.remove('is-invalid');
                        msgRegEl.className = 'form-hint text-success fw-bold';
                        msgRegEl.textContent = '✓ Disponible';
                    }
                });
        });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            // Nota: Se permite el envío de DPI incompleto en el modo de ingreso flexible
            if (dpiDuplicado) {
                e.preventDefault();
                alert('No se puede guardar: El DPI ya está registrado en el sistema.');
                dpiInput.focus();
                return false;
            }
            if (regDuplicado) {
                e.preventDefault();
                alert('No se puede guardar: El # de ficha o expediente ya está asignado a otro paciente.');
                regInput.focus();
                return false;
            }
        });
    }
});
</script>
@endsection
