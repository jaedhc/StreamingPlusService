<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function createComment(Request $request){
        $validator = Validator::make($request->all(),[
            'video_id' => 'required',
            'content' => 'required'
        ]);

        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()], 422);
        }

        try{
            $comment = new Comment();
            $comment->user_id = auth()->id();
            $comment->video_id = $request->video_id;
            $comment->content = $request->content;

            $comment->save();

            return response()->json([
                'message' => 'Comentario creado con éxito',
                'comment' => $comment
            ], 201);
        } catch (QueryException $e) {
            // Captura cualquier excepción relacionada con la base de datos
            return response()->json(['error' => $e], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        } catch (Exception $e) {
            // Captura cualquier otra excepción
            return response()->json(['error' => 'Ocurrió un error inesperado.'], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
        }
    }
}
