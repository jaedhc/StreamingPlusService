<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\UserPhoto;
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
            

            $profile = new UserPhoto();
            $profile->user_id = $user->id;
            $profile->url = "https://firebasestorage.googleapis.com/v0/b/streampl-f7a40.appspot.com/o/Users%2Fdefault%2Fdefault.png?alt=media&token=d272d7e2-9aa8-4b22-9904-9f574404a78e";
            $profile->file_type_id = 1;

            $profile->save();

            $cover = new UserPhoto();
            $cover->user_id = $user->id;
            $cover->url = "https://firebasestorage.googleapis.com/v0/b/streampl-f7a40.appspot.com/o/Users%2Fdefault%2Fdefault_cover.jpg?alt=media&token=b638c420-350e-476a-88a2-d0794e353055";
            $cover->file_type_id = 2;

            $cover->save();
            return response([
                'message' => 'User created correctly',
                'user' => $user,
                'profile' => $profile,
                'cover' => $cover
            ], Response::HTTP_CREATED);
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

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response(['message' => 'Credenciales incorrectas'], Response::HTTP_UNAUTHORIZED);
        } else {
            $token = $user->createToken('token')->plainTextToken;
            $cookie = cookie('cookie_token', $token, 60*24);
            return response()->json([
                'message' => 'Login successful',
                'token' => $token
            ], Response::HTTP_OK)->withoutCookie($cookie);
        }

        /*if(Auth::attempt($credentials)){
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            $cookie = cookie('cookie_token', $token, 60*24);
            return response()->json([
                'message' => 'Login successful',
                'token' => $token
            ], Response::HTTP_OK)->withoutCookie($cookie);
        } else {
            return response(['message' => 'Credenciales incorrectas'], Response::HTTP_UNAUTHORIZED);
        } */
    }

}
