<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::select("id", "user_id", "title", "short_description", "image", "created_at")->with("user")->orderByDesc("id")->get();
        return ["status" => 200, "posts" => $posts];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            "title" => "required|min:5|max:190",
            "content" => "nullable|max:20000",
            "short_description" => "min:5|max:250",
            "image" => "nullable|image|mimes:png,jpg,svg,webp,jpeg,gif|max:2048"
        ]);
        try {
            $user = $request->user();
            $payload["user_id"] = $user->id;

           if(isset($payload["image"])){
               $payload["image"] = $payload["image"]->store($user->id);
           }

           Post::create($payload);
           return ["status"=>200, "message"=> "Publicação realizada com sucesso"];

       }catch (\Exception $err){
           Log::info("post_store_err => ".$err->getMessage());
           return response()->json(["message"=> "Ocorreu um erro na publicação"],500);
       }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $post = Post::select("id", "user_id", "title", "image", "content", "short_description", "created_at")
                ->with("user")
                ->where("id", $id)->first();
            return ["status" => 200, "post" => $post];
        } catch (\Exception $err) {
            Log::info("post_show_err =>" . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Ocorreu um erro ao exibir a publicação!"], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $payload = $request->validate([
            "title" => "required|min:5|max:190",
            "content" => "nullable|max:20000",
        ]);
        try {
            Post::where("id", $id)->update($payload);
            return ["status" => 200, "message" => "Publicação alterada com sucesso"];
        } catch (\Exception $err) {
            Log::info("post_update_err =>" . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Ocorreu um erro durante a alteração da publicação"], 500);

        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $post = Post::find($id);
            $user = $request->user();
            if ($post->user_id != $user->id) {
                return response()->json(["status" => 401, "message" => "Não autorizado"]);
            }

            // * delete image
            if ($post->image) {
                Storage::delete($post->image);
            }

            $post->delete();
            return response()->json(["status" => 200, "message" => "Publicação excluída com sucesso"]);
        } catch (\Exception $err) {
            Log::info("post_delete_err =>" . $err->getMessage());
            return response()->json(["status" => 500, "message" => "Ocorreu um erro durante a exclusão da publicação"], 500);
        }

    }

    public function fetchUserPosts(Request $request){
        $user = $request->user();
        $posts = Post::select("id", "user_id", "title", "image", "created_at")->with("user")
          ->where("user_id", $user->id)->get();
        return ["status"=>200,'posts'=> $posts];
    }
}
