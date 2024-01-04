<?php

use App\Http\Controllers\API\Admin\ArtistController;
use App\Http\Controllers\API\Admin\GenreController;
use App\Http\Controllers\API\Admin\UserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BankController;
use App\Http\Controllers\API\DistributionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('users/login', [AuthController::class, 'login']);

Route::apiResources([
    'users' => UserController::class,
    'genres' => GenreController::class,
    'banks' => BankController::class,
    'artists' => ArtistController::class,
    'distributions' => DistributionController::class
]);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
