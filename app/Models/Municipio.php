<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id_municipio';

    protected $fillable = ['id_municipio', 'id_departamento', 'nombre'];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento', 'id_departamento');
    }

    public function comunidades()
    {
        return $this->hasMany(Comunidad::class, 'id_municipio', 'id_municipio');
    }
}
