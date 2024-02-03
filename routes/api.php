<?php

use App\Http\Controllers\API\AccountController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\AnalyticController;
use App\Http\Controllers\API\AnnouncementController;
use App\Http\Controllers\API\ArtistController;
use App\Http\Controllers\API\GenreController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BankController;
use App\Http\Controllers\API\BankWithdrawController;
use App\Http\Controllers\API\DistributionController;
use App\Http\Controllers\API\LegalController;
use App\Http\Controllers\API\MusicStoreController;
use App\Http\Controllers\API\OperatorController;
use App\Http\Controllers\API\PaypalWithdrawController;
use App\Http\Controllers\API\PlatformController;
use App\Http\Controllers\API\Services\PlaylistPitchController;
use App\Http\Controllers\API\Services\YoutubeOacController;
use App\Http\Controllers\API\TrackController;
use App\Http\Controllers\API\TransactionController;
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
Route::apiResource('accounts', AccountController::class);
Route::apiResource('transactions', TransactionController::class);
Route::get('announcements', [AnnouncementController::class, 'index']);
Route::post('announcements', [AnnouncementController::class, 'store']);

Route::apiResource('withdraw/paypal', PaypalWithdrawController::class)->except(['update', 'show']);
Route::apiResource('withdraw/banks', BankWithdrawController::class)->except(['update', 'show']);

Route::apiResource('legals', LegalController::class)->except(['update', 'show']);
Route::post('legals/bulk', [LegalController::class, 'bulkStore']);

Route::apiResource('services/playlist-pitches', PlaylistPitchController::class);
Route::apiResource('services/youtube-oac', YoutubeOacController::class);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/email/verify', [AuthController::class, 'notice'])->name('verification.notice');
    Route::post('/email/verification-notification', [AuthController::class, 'resend'])->middleware(['throttle:6,1'])->name('verification.send');
    Route::get('users/notification', [UserController::class, 'unreadNotification']);
    Route::post('users/notification', [UserController::class, 'readNotification']);

    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::apiResources([
        'tracks' => TrackController::class,
        'distributions' => DistributionController::class,
        'analytics' => AnalyticController::class,
    ]);

    Route::get('analytics/{period}/artist/{artist}', [AnalyticController::class, 'showByPeriodAndArtist']);

    Route::group(['middleware' => ['role:user|admin']], function () {
        Route::apiResources([
            'artists' => ArtistController::class,
        ]);
    });

    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('/users/log', [UserController::class, 'getUsersWithLog']);
        Route::apiResources([
            'users' => UserController::class,
            'genres' => GenreController::class,
            'banks' => BankController::class,
            'operators' => OperatorController::class,
        ]);
    });

    
    // Route::group(['middleware' => ['role:admin|operator']], function () {
    //     Route::apiResources([
    //         'operators' => OperatorController::class,
    //     ]);
    // });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
