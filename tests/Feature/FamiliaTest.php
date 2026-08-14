<?php

namespace Tests\Feature;

use App\Models\Usuario;
use App\Models\Familia;
use App\Models\Comunidad;
use Tests\TestCase;

class FamiliaTest extends TestCase
{
    private function getAdminUser()
    {
        return Usuario::where('username', 'admin')->first();
    }

    public function test_authenticated_user_can_view_familias_index(): void
    {
        $admin = $this->getAdminUser();
        $response = $this->actingAs($admin)->get('/familias');

        $response->assertStatus(200);
        $response->assertSee('Núcleos Familiares Registrados');
    }

    public function test_can_create_new_familia(): void
    {
        $admin = $this->getAdminUser();
        $comunidades = Comunidad::first();

        $numFam = 'TEST-FAM-' . rand(1000, 9999);
        $randomDpi = '2' . sprintf('%012d', rand(1, 999999999));

        $response = $this->actingAs($admin)->post('/familias', [
            'id_comunidad'    => $comunidades->id_comunidad,
            'numero_familia'  => $numFam,
            'apellido_cabeza' => 'González Test',
            'dpi'             => $randomDpi,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('familias', [
            'numero_familia'  => $numFam,
            'apellido_cabeza' => 'González Test',
        ]);

        $familia = Familia::where('numero_familia', $numFam)->first();
        $response->assertRedirect('/familias/' . $familia->id_family);
    }

    public function test_cannot_create_duplicate_numero_familia(): void
    {
        $admin = $this->getAdminUser();
        $familiaExistente = Familia::first();

        $response = $this->actingAs($admin)->post('/familias', [
            'id_comunidad'    => $familiaExistente->id_comunidad,
            'numero_familia'  => $familiaExistente->numero_familia,
            'apellido_cabeza' => 'Otro Apellido',
        ]);

        $response->assertSessionHasErrors('numero_familia');
        $this->assertDatabaseHas('alertas_duplicado', [
            'tipo_duplicado'  => 'numero_familia',
            'valor_duplicado' => $familiaExistente->numero_familia,
        ]);
    }
}
