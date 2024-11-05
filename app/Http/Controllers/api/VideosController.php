<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator; // Para encriptar la contraseña
use Illuminate\Database\QueryException;
use App\Models\Video;
use App\Models\VideoContent;

class VideosController extends Controller
{
    public function createVideo(Request $request){
        $validator = Validator::make($request->all(),[
            'id_user' => 'required',
            'title' => 'required',
            'description' => 'required',
            'url_video' => 'required',
            'url_thumbnail' => 'required',
            'duration' => 'required'
        ]);

        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()], 422);
        }

        try{
            $video = new Video();   
            $video->user_id = $request->id_user;
            $video->title = $request->title;
            $video->description = $request->description;
            $video->duration = $request->duration;

            $video->save();

            $videoId = $video->id;

            $videoCover = new VideoContent();
            $videoCover->video_id = $videoId;
            $videoCover->url = $request->url_thumbnail;
            $videoCover->file_type_id = 2;

            $videoCover->save();

            $videoFile = new VideoContent();
            $videoFile->video_id = $videoId;
            $videoFile->url = $request->url_video;
            $videoFile->file_type_id = 1;

            $videoFile->save();

            return response([
                'message' => 'Creado con exito',
                'video' => $video,
                'cover' => $videoCover,
                'file' => $videoFile
            ], Response::HTTP_CREATED);

        } catch (QueryException $e) {
            // Captura cualquier excepción relacionada con la base de datos
            return response()->json(['error' => $e], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        } catch (Exception $e) {
            // Captura cualquier otra excepción
            return response()->json(['error' => 'Ocurrió un error inesperado.'], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        }
    }

    public function getUserVideos(Request $request){
        $validator = Validator::make($request->all(),[
            'id_user' => 'required'
        ]);

        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()], 422);
        }

        try{
            $videos = Video::with('contents')->where('user_id', $request->id_user)->get();

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
                'id_user' => $request->id_user,
                'result' => $result
            ], 200);
        } catch (QueryException $e) {
            // Captura cualquier excepción relacionada con la base de datos
            return response()->json(['error' => $e], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        } catch (Exception $e) {
            // Captura cualquier otra excepción
            return response()->json(['error' => 'Ocurrió un error inesperado.'], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        }
    }
}
