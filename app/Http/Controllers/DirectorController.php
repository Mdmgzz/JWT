<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Director;
use Illuminate\Database\QueryException; // Controla los frenos de integridad de la BD

class DirectorController extends Controller
{
    /**
     * GET /api/directores (Listar)
     */
    public function index(Request $request)
    {
        $directores = Director::all();

        // Si la petición viene de Postman/API esperando JSON:
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($directores, 200);
        }

        // Si viene del navegador web (Renderiza tu Blade):
        $tableData = [];
        foreach($directores as $director){
            $tableData[$director->id] = [
                $director->name, 
                $director->surname ?? '', 
                $director->birth_date ?? ''
            ];
        }
        $tableData = collect($tableData);
        $header = collect(['Nombre', 'Apellido', 'Fecha nacimiento']);
    
        return view('director.index', compact('tableData', 'header'));
    }

    /**
     * POST /api/directores (Crear)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255', // Cambiado a required por restricción NOT NULL en BD
            'biography' => 'nullable|string',
            'birthdate' => 'nullable|date',
        ]);

        $director = Director::create($validated);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($director, 201);
        }

        return redirect()->route('directors.index');
    }

    /**
     * GET /api/directores/{id} (Ver perfil)
     */
    public function show(Request $request, Director $directore)
    {
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($directore->load('films'), 200);
        }

        $headerPeliculas = collect(['Title', 'Sinopsis', 'Duration']);
        $films = $directore->films ?? [];
        $tableData = [];
        
        foreach($films as $film){
            $tableData[$film->id] = [
                $film->title, $film->sinopsis, $film->duration
            ];
        }
        $tableData = collect($tableData);
        
        // Renombramos la variable para tu vista show.blade.php que espera $director
        $director = $directore; 
        
        return view('director.show', compact('director', 'headerPeliculas', 'tableData'));
    }

    /**
     * PUT /api/directores/{id} (Actualizar)
     */
    public function update(Request $request, $id)
    {
        // Busca el director, si no existe lanza el 404 que pide el test
        $director = Director::findOrFail($id);

        // Valida los datos que vienen del test
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Actualiza y guarda
        $director->update($validated);

        return response()->json($director, 200);
    }

    /**
     * DELETE /api/directores/{id} (Eliminar)
     */
    public function destroy($id)
    {
        // Busca el director o lanza un 404 si no existe
        $director = Director::findOrFail($id);

        try {
            // Intenta borrarlo de la base de datos
            $director->delete();
            return response()->json(['message' => 'Director eliminado correctamente'], 200);
            
        } catch (QueryException $e) {
            // Si salta la restricción de la BD por tener películas asociadas (onDelete('restrict'))
            return response()->json([
                'error' => 'Conflict',
                'message' => 'No se puede eliminar el director porque tiene películas asociadas.'
            ], 409);
        }
    }
}