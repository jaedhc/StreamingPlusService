<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator; // Para encriptar la contraseña
use Illuminate\Database\QueryException;
use App\Models\Subscription;
use App\Models\User;

class SubscriptionsController extends Controller
{
    public function subscribeTo(Request $request){
        $validator = Validator::make($request->all(),[
            'id_subscribed' => 'required'
        ]);

        if($validator->fails()){
            return response()->json(["error"=>$validator->errors()], 422);
        }

        try{
            $sub = new Subscription();
            $sub->subscriber_id = auth()->id();
            $sub->subscribed_id = $request->id_subscribed;

            $sub->save();
            
            $subscribed = User::find($request->id_subscribed);
            $subscribed->subs = $subscribed->subs + 1;

            $subscribed->save();
            return response()->json([
                'message' => 'Suscrto correctamente'
            ], 200);
        }catch (QueryException $e) {
            // Captura cualquier excepción relacionada con la base de datos
            return response()->json(['error' => $e], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        } catch (Exception $e) {
            // Captura cualquier otra excepción
            return response()->json(['error' => 'Ocurrió un error inesperado.'], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        }
    }

    public function removeSubscription(Request $request){
        $validator = Validator::make($request->all(),[
            'id_subscribed' => 'required'
        ]);

        if($validator->fails()){
            return response()->json(["error"=>$validator->errors()], 422);
        }

        try{
            $sub = Subscription::where('subscriber_id', auth()->id())
                                ->where('subscribed_id', $request->id_subscribed)
                                ->first();

            if(!$sub){
                return response()->json(['error' => "no encontrado"], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
            }

            Subscription::destroy($sub->id);
            $subscribed = User::find($request->id_subscribed);
            $subscribed->subs = $subscribed->subs - 1;

            $subscribed->save();
            return response()->json([
                'message' => 'removida'
            ], 200);
        }catch (QueryException $e) {
            // Captura cualquier excepción relacionada con la base de datos
            return response()->json(['error' => $e], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        } catch (Exception $e) {
            // Captura cualquier otra excepción
            return response()->json(['error' => 'Ocurrió un error inesperado.'], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        }
    }

    public function isSuscribed(Request $request){
        $validator = Validator::make($request->all(),[
            'id_subscribed' => 'required'
        ]);

        if($validator->fails()){
            return response()->json(["error"=>$validator->errors()], 422);
        }
        
        try{
            $userInfo = User::join('user_photos as up', 'up.user_id', '=', 'users.id')
                            ->select(
                            'users.name',
                            'users.subs',
                            'up.url')
                            ->where('up.file_type_id', 1)
                            ->where('users.id', $request->id_subscribed)
                            ->get();

            $subscribed = Subscription::where('subscriber_id', auth()->id())
                                        ->where('subscribed_id', $request->id_subscribed)
                                        ->get();
            
            $found = false;
            if (! $subscribed->isEmpty()) {
                $found = true;
            }

            return response()->json([
                'subscribed' => $found,
                'user' => $userInfo, 
            ], 200);
        }   catch (QueryException $e) {
            // Captura cualquier excepción relacionada con la base de datos
            return response()->json(['error' => $e], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        } catch (Exception $e) {
            // Captura cualquier otra excepción
            return response()->json(['error' => 'Ocurrió un error inesperado.'], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        }
        

    }
}
