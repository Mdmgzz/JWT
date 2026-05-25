<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * POST /api/auth/login
     */
    public function login(Request $request)
    {
        // validamos manualmente para asegurarnos de que devuelva un 422 con los errores JSON exactos
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors'  => $validator->errors()
            ], 422);
        }

        // si la validación pasa, procedemos con el intento de login
        $credentials = $request->only('email', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * GET /api/auth/me
     */
    public function me()
    {
        // Usamos el guard 'api' para recuperar al usuario
        return response()->json(Auth::guard('api')->user());
    }

    /**
     * POST /api/auth/logout
     */
    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Logged out']);
    }

    /**
     * POST /api/auth/refresh
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::guard('api')->refresh());
    }

    /**
     * Estructura de respuesta (Tarea 2 y 4)
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            // Accedemos a la factory del guard 'api' para el tiempo de expiración
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60 
        ]);
    }
}