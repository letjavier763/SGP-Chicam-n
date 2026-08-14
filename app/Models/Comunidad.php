<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comunidad extends Model
{
    public $timestamps = false;
    protected $table = 'comunidades';
    protected $primaryKey = 'id_comunidad';

    protected $fillable = ['id_comunidad', 'id_municipio', 'nombre', 'zona'];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio', 'id_municipio');
    }

    public function familias()
    {
        return $this->hasMany(Familia::class, 'id_comunidad', 'id_comunidad');
    }
}
