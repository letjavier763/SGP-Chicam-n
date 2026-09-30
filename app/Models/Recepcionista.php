<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recepcionista extends Model
{
    protected $table = 'recepcionistas';
    protected $primaryKey = 'id_recepcionista';

    protected $fillable = ['nombre', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function turnos()
    {
        return $this->hasMany(TurnoPersonal::class, 'id_recepcionista', 'id_recepcionista');
    }
}
