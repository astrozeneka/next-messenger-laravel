<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;

Route::post('/auth/google', [AuthController::class, 'google']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/conversations/{conversationId}/users', [ConversationController::class, 'getUsers']);
});
