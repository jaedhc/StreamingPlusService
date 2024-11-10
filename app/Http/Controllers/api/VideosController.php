<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator; // Para encriptar la contraseña
use Illuminate\Database\QueryException;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoContent;
use App\Models\Comments;
use App\Models\UserFiles;
use App\Models\UserPhoto;

class VideosController extends Controller
{
    public function createVideo(Request $request){
        $validator = Validator::make($request->all(),[
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
            $video->user_id = auth()->id();
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

    public function getUserVideos($userId = null){
        if($userId){
            try{
                $user =  User::find($userId);
                $videos = Video::join('video_contents as vc', 'vc.video_id', '=', 'videos.id')
                ->join('users as u', 'videos.user_id', '=', 'u.id')
                ->join('user_photos as up', 'u.id', '=', 'up.user_id')
                ->select(
                    'videos.id',
                    'videos.title',
                    'videos.duration',
                    'vc.url as thumbnail',
                    'u.name',
                    'up.url as profile'
                )
                ->where('vc.file_type_id', 1)
                ->where('up.file_type_id', 2)
                ->where('u.id', $userId)
                ->get();
                return response()->json([
                    'userInfo' => $user,
                    'videos' => $videos
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

    public function getVideo($videoId = null){
        if($videoId){
            try{
                $video = Video::find($videoId);
                
                $videoContent = VideoContent::where('video_id', $video->id)->where('file_type_id', 1)->get();
                
                $user = User::find($video->user_id);

                $userFile = UserPhoto::where('user_id', $user->id)->where('file_type_id',1)->get();

                return response()->json([
                    'video' => $video,
                    'videoContent' => $videoContent,
                    'user' => $user,
                    'userPic' => $userFile
                ], 200);
            }catch (QueryException $e) {
            // Captura cualquier excepción relacionada con la base de datos
                return response()->json(['error' => $e], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
            } catch (Exception $e) {
                // Captura cualquier otra excepción
                return response()->json(['error' => 'Ocurrió un error inesperado.'], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
            }
        }
    }

    public function getVideos(){

        try{
            $videos = Video::join('video_contents as vc', 'vc.video_id', '=', 'videos.id')
            ->join('users as u', 'videos.user_id', '=', 'u.id')
            ->join('user_photos as up', 'u.id', '=', 'up.user_id')
            ->select(
                'videos.id',
                'videos.title',
                'videos.duration',
                'vc.url as thumbnail',
                'u.name',
                'up.url as profile'
            )
            ->where('vc.file_type_id', 1)
            ->where('up.file_type_id', 2)
            ->get();
            return response()->json($videos, 200);
        }catch (QueryException $e) {
        // Captura cualquier excepción relacionada con la base de datos
            return response()->json(['error' => $e], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        } catch (Exception $e) {
            // Captura cualquier otra excepción
            return response()->json(['error' => 'Ocurrió un error inesperado.'], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        }
        
    }

    public function deleteVideo(Request $request){
        $validator = Validator::make($request->all(),[
            'video_id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()], 422);
        }

        $video = Video::find($request->video_id);
        
        if($video && $video->user_id == auth()->id()){
            $video->delete();
            return response()->json([
                'message' => 'Video deleted'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Video not found'
            ], 200);
        }
    }

    public function search(Request $request){
        $validator = Validator::make($request->all(),[
            'query' => 'required|string|min:3'
        ]);

        $query = $request->input('query');

        $videos = Video::where('title', 'LIKE', '%' . $query . '%')->get();

        return response()->json([
            'message' => 'videos found',
            'videos' => $videos
        ], 200);
    }
}
