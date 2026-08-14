<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurnoPersonal extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id_turno';
    protected $table = 'turnos_personal';

    protected $fillable = [
        'id_usuario', 'fecha', 'tipo_turno', 'hora_inicio', 'hora_fin', 'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function registrosLlegada()
    {
        return $this->hasMany(RegistroLlegada::class, 'id_turno', 'id_turno');
    }

    public function reportesDiarios()
    {
        return $this->hasMany(ReporteDiario::class, 'id_turno', 'id_turno');
    }
}
