<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 23: test_token_expirado_devuelve_401 utilizando Carbon::setTestNow()
     */
    public function test_token_expirado_devuelve_401(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        // Viajamos al futuro (2 horas después) para asegurar que el token haya expirado
        Carbon::setTestNow(Carbon::now()->addHours(2));

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->withHeader('Accept', 'application/json')
                         ->getJson('/api/auth/me');

        $response->assertStatus(401);

        // Limpiamos el simulador de tiempo
        Carbon::setTestNow();
    }

    /**
     * Test 24: test_respuestas_de_error_no_exponen_stack_trace (Entorno de producción seguro)
     */
    public function test_respuestas_de_error_no_exponen_stack_trace(): void
    {
        // Simulamos entorno de producción con debug desactivado
        config(['app.env' => 'production']);
        config(['app.debug' => 'false']);

        // Forzamos una petición que sabemos que va a fallar (404)
        $response = $this->withHeader('Accept', 'application/json')
                         ->getJson('/api/directores/9999');

        // Verificamos que no se filtren datos de depuración en la respuesta JSON
        $response->assertJsonMissingPath('exception');
        $response->assertJsonMissingPath('file');
        $response->assertJsonMissingPath('line');
        $response->assertJsonMissingPath('trace');
    }
}