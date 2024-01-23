<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Log;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

     public function register(Request $request)
     {
         $payload = $request->validate([
             "name" => "required|string|min:2|max:60",
             "email" => "required|email|unique:users,email",
             "password" => "required|min:6|max:50|confirmed",
         ]);

         try {
            $payload["password"] = Hash::make($payload["password"]);
            User::create($payload);
            return ["status" => 200, "message" => "Usuário criado com sucesso"];
         } catch (\Exception $err){
             Log::info("register_err => ".$err->getMessage());
            return response()->json(["message" => "Ocorreu um erro"],500);
         }
     }

     public function login(Request  $request)
     {
         $payload = $request->validate([
             "email" => "required|email",
             "password" => "required",
         ]);

         try {
            $user = User::where("email", $payload["email"])->first();
            if($user){
                if(!Hash::check($payload["password"], $user->password))
                {
                    return response()->json(["status"=> 401, "message"=> "Invalid credentials"],401);
                }

                $token = $user->createToken("web")->plainTextToken;
                $authRes = array_merge($user->toArray(), ["token" => $token]);
                return ["status"=> 200, "message" => "Logado com sucesso", "user" => $authRes];
            }
             return ["status" => 401, "message" => "Nenhum usuário com esse e-mail"];
         } catch (\Exception $err){
             Log::info("register_err => ".$err->getMessage());
             return response()->json(["message" => "Ocorreu um erro"],500);
         }
     }

    public function checkCredentials(Request  $request)
    {
        $payload = $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        try {
            $user = User::where("email", $payload["email"])->first();
            if($user){
                if(!Hash::check($payload["password"], $user->password))
                {
                    return ["status"=> 401, "message"=> "Credenciais inválidas"];
                }

                return ["status"=> 200, "message" => "Logado com sucesso"];
            }
            return ["status" => 401, "message" => "Nenhum usuário com esse e-mail"];
        } catch (\Exception $err){
            Log::info("verify_credentials_err => ".$err->getMessage());
            return response()->json(["message" => "Ocorreu um erro"],500);
        }
    }


     public function logout(Request $request)
     {
         try{
            $request->user()->currentAccessToken()->delete();
            return ["status"=> 200, "message"=> "Logout realizado com sucesso"];
         } catch (\Exception $err){
            Log::info("logout_err => ".$err->getMessage());
            return response()->json(["message"=> "Não foi possível realizar o logout"],500);
         }
     }

}
