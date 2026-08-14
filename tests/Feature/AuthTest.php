<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Invitados son redireccionados al login al intentar ver el dashboard.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * El formulario de login carga con éxito.
     */
    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('SGP CAP Chicamán');
    }

    /**
     * El login falla con credenciales inválidas.
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post('/login', [
            'username' => 'invalid_user',
            'password' => 'wrong_password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    /**
     * El login tiene éxito con credenciales correctas.
     */
    public function test_login_succeeds_with_correct_credentials(): void
    {
        // El usuario admin ya existe por el Seeder en la base de datos local
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'Admin@2026!',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    /**
     * Cierre de sesión funciona correctamente.
     */
    public function test_user_can_logout(): void
    {
        $user = Usuario::where('username', 'admin')->first();
        
        $response = $this->actingAs($user)
                         ->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
