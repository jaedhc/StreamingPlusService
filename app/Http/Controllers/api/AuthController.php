<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Para encriptar la contraseña
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()], 422);
        }

        try{
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
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

    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()], 422);
        }

        $credentials = $validator->validated();

        if(Auth::attempt($credentials)){
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            $cookie = cookie('cookie_token', $token, 60*24);
            return response()->json([
                'message' => 'Login successful',
                'token' => $token
            ], Response::HTTP_OK)->withoutCookie($cookie);
        } else {
            return response(['message' => 'Credenciales incorrectas'], Response::HTTP_UNAUTHORIZED);
        }
    }
}
