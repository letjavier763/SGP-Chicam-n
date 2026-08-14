<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroLlegada extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id_registro';
    protected $table = 'registros_llegada';

    protected $fillable = [
        'id_paciente', 'id_turno', 'fecha', 'hora_llegada', 'es_nuevo', 'observaciones',
    ];

    protected $casts = [
        'fecha'    => 'date',
        'es_nuevo' => 'boolean',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function turno()
    {
        return $this->belongsTo(TurnoPersonal::class, 'id_turno', 'id_turno');
    }
}
