<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporteDiario extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id_reporte';
    protected $table = 'reportes_diarios';

    protected $fillable = [
        'id_turno',
        'fecha',
        'total_pacientes',
        'total_nuevos',
        'total_recurrentes',
        'tiempo_promedio_registro_seg',
        'generado_en',
    ];

    protected $casts = [
        'fecha'        => 'date',
        'generado_en'  => 'datetime',
    ];

    public function turno()
    {
        return $this->belongsTo(TurnoPersonal::class, 'id_turno', 'id_turno');
    }
}
