<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id_bitacora';
    protected $table = 'bitacora';

    protected $fillable = [
        'id_usuario',
        'accion',
        'tabla_afectada',
        'id_registro_afectado',
        'detalle',
        'ip_equipo',
        'fecha',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Registra una entrada en la bitácora.
     */
    public static function registrar(
        int $idUsuario,
        string $accion,
        string $tabla,
        ?int $idRegistro = null,
        ?string $detalle = null,
        ?string $ip = null
    ): void {
        static::create([
            'id_usuario'           => $idUsuario,
            'accion'               => $accion,
            'tabla_afectada'       => $tabla,
            'id_registro_afectado' => $idRegistro,
            'detalle'              => $detalle,
            'ip_equipo'            => $ip,
        ]);
    }
}
