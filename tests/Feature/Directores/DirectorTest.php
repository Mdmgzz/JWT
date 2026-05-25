<?php

namespace Tests\Feature\Directores;

use App\Models\Director;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DirectorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * genera las cabeceras con un token JWT válido.
     */
    private function getAuthHeaders(): array
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        return [
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ];
    }

    /**
     * Test 9: test_listar_directores_requiere_autenticacion
     */
    public function test_listar_directores_requiere_autenticacion(): void
    {
        $response = $this->getJson('/api/directores');

        $response->assertStatus(401);
    }

    /**
     * Test 10: test_listar_directores_autenticado_devuelve_coleccion
     */
    public function test_listar_directores_autenticado_devuelve_coleccion(): void
    {
        Director::factory()->count(3)->create();

        $response = $this->withHeaders($this->getAuthHeaders())->getJson('/api/directores');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /**
     * Test 11: test_crear_director_con_datos_validos
     */
    public function test_crear_director_con_datos_validos(): void
    {
        $payload = [
            'name' => 'Quentin',
            'surname' => 'Tarantino',
            'birthdate' => '1963-03-27',
        ];

        $response = $this->withHeaders($this->getAuthHeaders())->postJson('/api/directores', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('directors', [
            'name' => 'Quentin',
            'surname' => 'Tarantino',
            'birthdate' => '1963-03-27',
        ]);
    }

    /**
     * Test 12: test_crear_director_con_datos_invalidos_devuelve_422
     */
    public function test_crear_director_con_datos_invalidos_devuelve_422(): void
    {
        $payload = [
            'name' => '', // Obligatorio vacío
            'birthdate' => 'fecha-no-valida',
        ];

        $response = $this->withHeaders($this->getAuthHeaders())->postJson('/api/directores', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'birthdate']);
    }

    /**
     * Test 13: test_actualizar_director_existente
     */
    public function test_actualizar_director_existente(): void
    {
        $director = Director::factory()->create(['name' => 'Christopher']);

        $payload = ['name' => 'Chris'];

        $response = $this->withHeaders($this->getAuthHeaders())->putJson("/api/directores/{$director->id}", $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('directors', [
            'id' => $director->id,
            'name' => 'Chris',
        ]);
    }

    /**
     * Test 14: test_actualizar_director_inexistente_devuelve_404
     */
    public function test_actualizar_director_inexistente_devuelve_404(): void
    {
        $response = $this->withHeaders($this->getAuthHeaders())->putJson('/api/directores/9999', ['name' => 'Test']);

        $response->assertStatus(404);
    }

    /**
     * Test 15: test_eliminar_director_existente
     */
    public function test_eliminar_director_existente(): void
    {
        $director = Director::factory()->create();

        $response = $this->withHeaders($this->getAuthHeaders())->deleteJson("/api/directores/{$director->id}");

        $response->assertStatus(in_array($response->getStatusCode(), [200, 24]) ? $response->getStatusCode() : 200);
        $this->assertDatabaseMissing('directors', ['id' => $director->id]);
    }

    public function test_eliminar_director_con_peliculas_asociadas(): void
    {
        $director = Director::factory()->create();

        // Simula la película adaptada al esquema real de tu base de datos y modelo
        $director->films()->create([
            'title' => 'Pulp Fiction',
            'release_date' => '1994-10-14',
            'sinopsis' => 'La vida de dos pistoleros de la mafia...',
            'duration' => 154,
            'gendre' => 'Crime',
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())->deleteJson("/api/directores/{$director->id}");

        // Verifica que devuelva un código de error controlado (409 Conflict o 422 Unprocessable)
        // porque la base de datos restringe el borrado si tiene películas (onDelete('restrict'))
        $this->assertTrue(in_array($response->getStatusCode(), [409, 422]));
    }
}
