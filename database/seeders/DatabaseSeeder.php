<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =============================================
        // 1. Roles del sistema
        // =============================================
        $roles = [
            ['id_rol' => 1, 'nombre_rol' => 'Administrador', 'descripcion' => 'Acceso total al sistema, gestión de usuarios y auditoría.'],
            ['id_rol' => 2, 'nombre_rol' => 'Recepcionista',  'descripcion' => 'Registro y búsqueda de pacientes en ventanilla.'],
            ['id_rol' => 3, 'nombre_rol' => 'Director',       'descripcion' => 'Visualización de reportes y estadísticas del CAP.'],
        ];

        DB::table('roles')->insert($roles);

        // =============================================
        // 2. Departamento base (El Quiché)
        // =============================================
        DB::table('departamentos')->insert([
            ['id_departamento' => 14, 'nombre' => 'El Quiché'],
        ]);

        // =============================================
        // 3. Municipio base (Chicamán)
        // =============================================
        DB::table('municipios')->insert([
            ['id_municipio' => 1401, 'id_departamento' => 14, 'nombre' => 'Chicamán'],
        ]);

        // =============================================
        // 4. Comunidad base (Casco urbano)
        // =============================================
        DB::table('comunidades')->insert([
            ['id_comunidad' => 1, 'id_municipio' => 1401, 'nombre' => 'Casco Urbano Chicamán', 'zona' => 'Zona 1'],
        ]);

        // =============================================
        // 5. Usuario administrador por defecto
        // =============================================
        DB::table('usuarios')->insert([
            [
                'id_rol'          => 1,
                'nombre_completo' => 'Administrador del Sistema',
                'username'        => 'admin',
                'password_hash'   => Hash::make('Admin@2026!'),
                'ultimo_acceso'   => null,
                'activo'          => true,
            ],
        ]);

        // =============================================
        // 7. Familia y Paciente iniciales de prueba
        // =============================================
        DB::table('familias')->insert([
            [
                'id_family'       => 1,
                'id_comunidad'    => 1,
                'numero_familia'  => 'FAM-001',
                'apellido_cabeza' => 'Pérez Ramos',
                'dpi'             => '1987654320101',
                'activo'          => true,
            ]
        ]);

        DB::table('pacientes')->insert([
            [
                'id_paciente'              => 1,
                'id_family'                => 1,
                'nombres'                  => 'Juan Pedro',
                'apellidos'                => 'Pérez Ramos',
                'dpi'                      => '1987654320101',
                'numero_expediente_fisico' => 'FAM-001',
                'fecha_nacimiento'         => '1990-01-01',
                'sexo'                     => 'M',
                'telefono'                 => '55551234',
                'activo'                   => true,
            ]
        ]);
    }
}
