<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    public $timestamps = false;
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'id_rol',
        'nombre_completo',
        'username',
        'password_hash',
        'ultimo_acceso',
        'activo',
    ];

    protected $hidden = ['password_hash'];

    // Necesario para que Laravel Auth funcione con password_hash
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // -------------------------
    // Relaciones
    // -------------------------

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function sesiones()
    {
        return $this->hasMany(Sesion::class, 'id_usuario', 'id_usuario');
    }

    public function turnosPersonal()
    {
        return $this->hasMany(TurnoPersonal::class, 'id_usuario', 'id_usuario');
    }

    public function alertasDuplicado()
    {
        return $this->hasMany(AlertaDuplicado::class, 'id_usuario', 'id_usuario');
    }

    public function bitacora()
    {
        return $this->hasMany(Bitacora::class, 'id_usuario', 'id_usuario');
    }

    public function configuraciones()
    {
        return $this->hasMany(Configuracion::class, 'id_usuario', 'id_usuario');
    }

    // -------------------------
    // Helpers de roles
    // -------------------------

    public function esAdministrador(): bool
    {
        return $this->rol->nombre_rol === 'Administrador';
    }

    public function esRecepcionista(): bool
    {
        return $this->rol->nombre_rol === 'Recepcionista';
    }

    public function esDirector(): bool
    {
        return $this->rol->nombre_rol === 'Director';
    }
}
