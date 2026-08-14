<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertaDuplicado extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id_alerta';
    protected $table = 'alertas_duplicado';

    protected $fillable = [
        'id_usuario', 'tipo_duplicado', 'valor_duplicado', 'fecha_deteccion', 'accion_tomada',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
