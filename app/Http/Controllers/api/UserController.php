<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\Video;
use App\Models\userPhoto;

use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        return 'aa';
    }

    public function userProfile(Request $request){
        return response()->json([
            "message" => "userProfile OK",
            "userData" => auth()->user()
        ], Response::HTTP_OK);
    }


    public function users($userId = null){
        if($userId){
            try{
                $videos = Video::with(['contents' => function ($query){
                    $query->where('file_type_id', 2);
                }])->where('user_id', $userId)->get();
    
                $result = [];
    
                foreach ($videos as $video) {
                    $contents = $video->contents->map(function ($content) {
                        return [
                            'video_id' => $content->video_id,
                            'url' => $content->url,
                            'file_type_id' => $content->file_type_id,
                            'id' => $content->id,
                        ];
                    });
                    $result[] = [
                        'video' => [
                            'user_id' => $video->user_id,
                            'title' => $video->title,
                            'description' => $video->description,
                            'duration' => $video->duration,
                            'updated_at' => $video->updated_at,
                            'created_at' => $video->created_at,
                            'id' => $video->id,
                        ],
                        'covers' => $contents, // Cambiar aquí para incluir múltiples covers
                    ];
                }
    
                return response()->json([
                    'message' => 'Detalles',
                    'user' => User::find($userId),
                    'videos' => $result,
                ], 200);
            } catch (QueryException $e) {
                // Captura cualquier excepción relacionada con la base de datos
                return response()->json(['error' => $e], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
            } catch (Exception $e) {
                // Captura cualquier otra excepción
                return response()->json(['error' => 'Ocurrió un error inesperado.'], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
            }
        } else {
            return response()->json([
                'message' => 'lista de usuarios',
                'users' => User::all()
            ], 200);
        }
    }  

    public function userPhoto(Request $request){
        $validator = Validator::make($request->all(),[
            'url' => 'required',
            'file_type_id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => $validator->errors()
            ], 422);
        }

        try{
            $exist = UserPhoto::where('user_id', auth()->id())->where('file_type_id', $request->file_type_id)->first();
            if($exist){
                $exist->url = $request->url;
                $exist->save();

                return response()->json([
                    'message' => 'photo saved',
                    'photo' => $exist
                ], 200);
            } else {
                $userPhoto = new UserPhoto();
                $userPhoto->user_id = auth()->id();
                $userPhoto->url = $request->url;
                $userPhoto->file_type_id = $request->file_type_id;

                $userPhoto->save();
                
                return response()->json([
                    'message' => 'photo saved',
                    'photo' => $userPhoto
                ], 200);
            }
        }catch (QueryException $e) {
            // Captura cualquier excepción relacionada con la base de datos
            return response()->json(['error' => $e], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        } catch (Exception $e) {
            // Captura cualquier otra excepción
            return response()->json(['error' => 'Ocurrió un error inesperado.'], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        }
    }
}
