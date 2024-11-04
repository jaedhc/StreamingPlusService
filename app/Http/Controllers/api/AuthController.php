<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash; // Para encriptar la contraseña
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        try{
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = $request->password;
            $user->role_id = 2;
            $user->subs = 10;

            $user->save();
            
            return response($user, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            // Captura cualquier excepción relacionada con la base de datos
            return response()->json(['error' => 'Error al crear el usuario.'], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        } catch (Exception $e) {
            // Captura cualquier otra excepción
            return response()->json(['error' => 'Ocurrió un error inesperado.'], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        }
    }
}
