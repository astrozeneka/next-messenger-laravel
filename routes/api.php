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
use App\Http\Controllers\ConfusingRythmesController;

Route::post('/auth/google', [AuthController::class, 'google']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);

// Create middleware group auth:sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/whoami', [AuthController::class, 'whoami']);
});

// Temporarily test without auth
Route::get('/confusing-rhymes/study-set', [ConfusingRythmesController::class, 'getStudySet']);