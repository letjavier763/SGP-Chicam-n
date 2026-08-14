<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Familia extends Model
{
    public $timestamps = false;
    protected $table = 'familias';
    protected $primaryKey = 'id_family';

    protected $fillable = [
        'id_comunidad',
        'numero_familia',
        'apellido_cabeza',
        'dpi',
        'fecha_nacimiento',
        'fecha_registro',
        'activo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_registro'   => 'datetime',
        'activo'           => 'boolean',
    ];

    public function comunidad()
    {
        return $this->belongsTo(Comunidad::class, 'id_comunidad', 'id_comunidad');
    }

    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'id_family', 'id_family');
    }
}
