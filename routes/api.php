<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\ArtistController;
use App\Http\Controllers\API\GenreController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BankController;
use App\Http\Controllers\API\DistributionController;
use App\Http\Controllers\API\MusicStoreController;
use App\Http\Controllers\API\PlatformController;
use App\Http\Controllers\API\TrackController;
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
Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('platforms', PlatformController::class);
Route::apiResource('stores', MusicStoreController::class);
Route::apiResource('admins', AdminController::class);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('tracks', TrackController::class);
    Route::apiResource('distributions', DistributionController::class);
    Route::group(['middleware' => ['role:admin']], function () {
        Route::apiResources([
            'users' => UserController::class,
            'genres' => GenreController::class,
            'banks' => BankController::class,
            'artists' => ArtistController::class,
        ]);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
