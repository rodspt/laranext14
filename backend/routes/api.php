<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{AuthController, PostController, CommentController, SearchController};
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// * Auth routes

Route::middleware('auth:sanctum')->group(function(){
    Route::post("/auth/logout",[AuthController::class,"logout"]);

    Route::get("/user/posts",[PostController::class,"fetchUserPosts"]);
    Route::apiResource("post",PostController::class)->except(["index","show"]);
    Route::apiResource("comment",CommentController::class)->except(["index","show"]);
});

Route::apiResource("post",PostController::class)->only(["index","show"]);
Route::apiResource("comment",CommentController::class)->only(["index","show"]);
Route::get("/search",[SearchController::class,"search"]);
Route::post("/auth/register", [AuthController::class,"register"]);
Route::post("/auth/login", [AuthController::class,"login"]);
Route::post("/auth/checkCredentials", [AuthController::class,"checkCredentials"]);
