<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    public $timestamps = false;
    protected $table = 'pacientes';
    protected $primaryKey = 'id_paciente';

    protected $fillable = [
        'id_family',
        'nombres',
        'apellidos',
        'dpi',
        'numero_expediente_fisico',
        'fecha_nacimiento',
        'sexo',
        'telefono',
        'parentesco_familia',
        'fecha_registro',
        'activo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_registro'   => 'datetime',
        'activo'           => 'boolean',
    ];

    // -------------------------
    // Relaciones
    // -------------------------

    public function familia()
    {
        return $this->belongsTo(Familia::class, 'id_family', 'id_family');
    }

    public function registrosLlegada()
    {
        return $this->hasMany(RegistroLlegada::class, 'id_paciente', 'id_paciente');
    }

    // -------------------------
    // Scopes útiles
    // -------------------------

    /** Sólo pacientes activos */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /** Búsqueda rápida por nombre, DPI o número de expediente */
    public function scopeBuscar($query, string $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombres', 'ilike', "%{$termino}%")
              ->orWhere('apellidos', 'ilike', "%{$termino}%")
              ->orWhere('dpi', 'like', "%{$termino}%")
              ->orWhere('numero_expediente_fisico', 'like', "%{$termino}%");
        });
    }
}
