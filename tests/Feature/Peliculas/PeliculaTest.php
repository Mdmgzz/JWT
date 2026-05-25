<?php

namespace Tests\Feature\Peliculas;

use App\Models\Director;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class PeliculaTest extends TestCase
{
    use RefreshDatabase;

    private function getAuthHeaders(): array
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        return [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ];
    }

    public function test_listar_peliculas_autenticado_devuelve_coleccion(): void
    {
        $director = Director::factory()->create();
        $director->films()->create([
            'title' => 'Inception', 
            'release_date' => '2010-07-16', 
            'sinopsis' => 'Un thriller sobre sueños.', 
            'duration' => 148, 
            'gendre' => 'Sci-Fi'
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())->getJson('/api/peliculas');

        $response->assertStatus(200);
    }

    public function test_crear_pelicula_asociada_a_director_existente(): void
    {
        $director = Director::factory()->create();

        $payload = [
            'title' => 'Interstellar',
            'release_date' => '2014-11-07',
            'sinopsis' => 'Viaje espacial.',
            'duration' => 169,
            'gendre' => 'Sci-Fi',
            'director_id' => $director->id
        ];

        $response = $this->withHeaders($this->getAuthHeaders())->postJson('/api/peliculas', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('films', [
            'title' => 'Interstellar',
            'director_id' => $director->id
        ]);
    }

    public function test_crear_pelicula_con_director_inexistente_devuelve_422(): void
    {
        $payload = [
            'title' => 'Avatar 3',
            'release_date' => '2025-12-19',
            'sinopsis' => 'Sinopsis',
            'duration' => 180,
            'gendre' => 'Sci-Fi',
            'director_id' => 9999 
        ];

        $response = $this->withHeaders($this->getAuthHeaders())->postJson('/api/peliculas', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['director_id']);
    }

    public function test_actualizar_pelicula(): void
    {
        $director = Director::factory()->create();
        $film = $director->films()->create([
            'title' => 'Oppenheimer', 
            'release_date' => '2023-07-21', 
            'sinopsis' => 'Historia de la bomba.', 
            'duration' => 180, 
            'gendre' => 'Biopic'
        ]);

        $payload = ['title' => 'Oppenheimer (Director\'s Cut)'];

        $response = $this->withHeaders($this->getAuthHeaders())->putJson("/api/peliculas/{$film->id}", $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'title' => 'Oppenheimer (Director\'s Cut)'
        ]);
    }

    public function test_eliminar_pelicula(): void
    {
        $director = Director::factory()->create();
        $film = $director->films()->create([
            'title' => 'Memento', 
            'release_date' => '2000-09-05', 
            'sinopsis' => 'Un hombre sin memoria.', 
            'duration' => 113, 
            'gendre' => 'Mystery'
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())->deleteJson("/api/peliculas/{$film->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('films', ['id' => $film->id]);
    }

    public function test_mostrar_pelicula_incluye_datos_del_director(): void
    {
        $director = Director::factory()->create(['name' => 'Christopher', 'surname' => 'Nolan']);
        $film = $director->films()->create([
            'title' => 'Dunkirk', 
            'release_date' => '2017-07-21', 
            'sinopsis' => 'Guerra.', 
            'duration' => 106, 
            'gendre' => 'War'
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())->getJson("/api/peliculas/{$film->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'title',
                     'director' => [
                         'id',
                         'name'
                     ]
                 ]);
    }
}