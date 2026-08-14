<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id_config';
    protected $table = 'configuracion';

    protected $fillable = [
        'id_usuario', 'clave', 'valor', 'descripcion', 'actualizado_en',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
