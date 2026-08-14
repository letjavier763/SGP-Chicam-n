<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sesion extends Model
{
    public $timestamps = false;
    protected $table = 'sesiones';
    protected $primaryKey = 'id_sesion';

    protected $fillable = [
        'id_usuario', 'hora_inicio', 'hora_cierre', 'ip_equipo', 'estado',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
