<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurnoPersonal extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id_turno';
    protected $table = 'turnos_personal';

    protected $fillable = [
        'id_usuario', 'id_recepcionista', 'nombre_recepcionista',
        'fecha', 'tipo_turno', 'hora_inicio', 'hora_fin', 'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function recepcionista()
    {
        return $this->belongsTo(Recepcionista::class, 'id_recepcionista', 'id_recepcionista');
    }

    public function registrosLlegada()
    {
        return $this->hasMany(RegistroLlegada::class, 'id_turno', 'id_turno');
    }

    public function reportesDiarios()
    {
        return $this->hasMany(ReporteDiario::class, 'id_turno', 'id_turno');
    }

    /**
     * Obtener el nombre del responsable del turno
     * (recepcionista si existe, si no el usuario del sistema)
     */
    public function getNombreResponsableAttribute(): string
    {
        if (!empty(trim($this->nombre_recepcionista ?? ''))) {
            return trim($this->nombre_recepcionista);
        }
        if ($this->recepcionista && !empty(trim($this->recepcionista->nombre ?? ''))) {
            return trim($this->recepcionista->nombre);
        }
        if ($this->usuario && !empty(trim($this->usuario->nombre_completo ?? ''))) {
            return trim($this->usuario->nombre_completo);
        }
        return 'Sin asignar';
    }
}

