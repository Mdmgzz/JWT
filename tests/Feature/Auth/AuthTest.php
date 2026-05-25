<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: test_login_con_credenciales_validas_devuelve_token
     */
    public function test_login_con_credenciales_validas_devuelve_token(): void
    {
        $user = User::factory()->create([
            'email' => 'migue@ejemplo.com',
            'password' => Hash::make('123456'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'migue@ejemplo.com',
            'password' => '123456',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
            ]);
    }

    /**
     * Test 2: test_login_con_credenciales_invalidas_devuelve_401
     */
    public function test_login_con_credenciales_invalidas_devuelve_401(): void
    {
        User::factory()->create([
            'email' => 'migue@ejemplo.com',
            'password' => Hash::make('123456'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'migue@ejemplo.com',
            'password' => 'password-incorrecto',
        ]);

        $response->assertStatus(401);
        $this->assertNull($response->json('access_token'));
    }

    /**
     * Test 3: test_login_con_campos_faltantes_devuelve_422
     */
    public function test_login_con_campos_faltantes_devuelve_422(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Test 4: test_logout_invalida_el_token
     */
    public function test_logout_invalida_el_token(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $logoutResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/auth/logout');

        $logoutResponse->assertStatus(200);

        // Al intentar usarlo de nuevo, debe ser rechazado (Blacklist activa)
        $retryResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/me');

        $retryResponse->assertStatus(401);
    }

    /**
     * Test 5: test_refresh_devuelve_nuevo_token_valido
     */
    public function test_refresh_devuelve_nuevo_token_valido(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);

        $newToken = $response->json('access_token');
        $this->assertNotEquals($token, $newToken);
    }

    /**
     * Test 6 & Test 25: test_me_devuelve_datos_del_usuario_autenticado_sin_password
     */
    public function test_me_devuelve_datos_del_usuario_autenticado_y_password_no_aparece(): void
    {
        $user = User::factory()->create([
            'name' => 'Miguel',
            'email' => 'migue@ejemplo.com',
        ]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonFragment(['email' => 'migue@ejemplo.com'])
            ->assertJsonMissingPath('password');
    }

    /**
     * Test 7: test_acceso_sin_token_devuelve_401
     */
    public function test_acceso_sin_token_devuelve_401(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    /**
     * Test 8: test_acceso_con_token_malformado_devuelve_401
     */
    public function test_acceso_con_token_malformado_devuelve_401(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer token_inventado_y_malformado')
            ->getJson('/api/auth/me');

        $response->assertStatus(401);
    }
}
