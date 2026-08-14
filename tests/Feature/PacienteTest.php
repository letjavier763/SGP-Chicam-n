<?php

namespace Tests\Feature;

use App\Models\Usuario;
use App\Models\Familia;
use App\Models\Paciente;
use Tests\TestCase;

class PacienteTest extends TestCase
{
    private function getAdminUser()
    {
        return Usuario::where('username', 'admin')->first();
    }

    public function test_authenticated_user_can_view_pacientes_index(): void
    {
        $admin = $this->getAdminUser();
        $response = $this->actingAs($admin)->get('/pacientes');

        $response->assertStatus(200);
        $response->assertSee('Pacientes Registrados');
    }

    public function test_can_create_paciente_inheriting_family_number_as_expediente(): void
    {
        $admin = $this->getAdminUser();
        $familia = Familia::first();

        $response = $this->actingAs($admin)->post('/pacientes', [
            'id_family'        => $familia->id_family,
            'nombres'          => 'Carlos Juan',
            'apellidos'        => 'Pérez Test',
            'fecha_nacimiento' => '1995-05-15',
            'sexo'             => 'M',
        ]);

        $this->assertDatabaseHas('pacientes', [
            'numero_expediente_fisico' => $familia->numero_familia,
            'nombres'                  => 'Carlos Juan',
        ]);

        $paciente = Paciente::where('nombres', 'Carlos Juan')->first();
        $response->assertRedirect('/pacientes/' . $paciente->id_paciente);
    }

    public function test_multiple_family_members_can_share_same_family_expediente_number(): void
    {
        $admin = $this->getAdminUser();
        $familia = Familia::first();

        // Registrar un segundo integrante en la misma familia compartiendo el expediente
        $response = $this->actingAs($admin)->post('/pacientes', [
            'id_family'                => $familia->id_family,
            'nombres'                  => 'María José',
            'apellidos'                => 'Pérez Test',
            'numero_expediente_fisico' => $familia->numero_familia,
            'fecha_nacimiento'         => '2000-01-01',
            'sexo'                     => 'F',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pacientes', [
            'id_family'                => $familia->id_family,
            'numero_expediente_fisico' => $familia->numero_familia,
            'nombres'                  => 'María José',
        ]);
    }
}
