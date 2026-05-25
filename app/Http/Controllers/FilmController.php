<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    public function index()
    {
        return response()->json(Film::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'release_date' => 'required|date',
            'sinopsis' => 'required|string',
            'duration' => 'required|integer',
            'gendre' => 'required|string',
            'director_id' => 'required|exists:directors,id',
        ]);

        $film = Film::create($validated);

        return response()->json($film, 201); // Retorna 201 Created
    }

    public function show($id)
    {
        // Cargamos la relación 'director' para que el JSON incluya los datos
        $film = Film::with('director')->findOrFail($id);

        return response()->json($film, 200);
    }

    public function update(Request $request, $id)
    {
        $film = Film::findOrFail($id);

        // Validamos solo lo que vamos a cambiar
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
        ]);

        $film->update($validated);

        return response()->json($film, 200);
    }

    public function destroy($id)
    {
        $film = Film::findOrFail($id);
        $film->delete();

        return response()->json(['message' => 'Eliminado'], 200);
    }
}
