<?php

namespace App\Http\Controllers\API;

use App\Models\Comment;
use Illuminate\Http\Request;
use Log;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request  $request)
    {
        try {
            $postId = $request->query("post_id");
            //$comments = Comment::select("*")->where("post_id", $postId)->get();
            $comments = Comment::select("id","user_id","content","created_at")->with("user")->where("post_id", $postId)->get();
            return ["status" => 200, "comments" => $comments];
        } catch (\Exception $err) {
            Log::info("comment_fetch_err =>" . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Ocorreu um erro durante a listagem de comentários!"], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            "post_id" => "required",
            "content" => "required|max:10000"
        ]);
        try {
            $user = $request->user();
            $payload["user_id"] = $user->id;
            $comment = Comment::create($payload);
            return ["status" => 200, "message" => "Comentário adicionado com sucesso!", "comment" => $comment];
        } catch (\Exception $err) {
            Log::info("comment_create_err =>" . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Ocorreu um erro inesperado!"], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $comment = Comment::select("id", "user_id", "content")->where("id", $id)->get();
        return ["status"=> 200, "comments" => $comment];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $payload = $request->validate([
            "post_id" => "required|exists:posts,id",
            "content" => "required|max:10000",
        ]);


        $user = $request->user();
        $payload["user_id"] = $user->id;
        Comment::where('id', $id)->update($payload);

        return ["status"=> 200, "message" => "Comentário alterado com sucesso"];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $user = $request->user();
            $comment = Comment::find($id);
            if ($user->id != $comment->user_id) {
                return ["status" => 401, "message" => "Não autorizado"];
            }
            $comment->delete();
            return response()->json(["status" => 200, "message" => "Comentário excluido com sucesso!"]);
        } catch (\Exception $err) {
            Log::info("comment_delete_err =>" . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Ocorreu um erro inesperado!"], 500);
        }

    }
}
