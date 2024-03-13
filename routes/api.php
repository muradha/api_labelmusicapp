<?php

use App\Http\Controllers\API\AccountController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\AnalyticController;
use App\Http\Controllers\API\AnnouncementController;
use App\Http\Controllers\API\ArtistController;
use App\Http\Controllers\API\ArtworkTemplateController;
use App\Http\Controllers\API\GenreController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BankController;
use App\Http\Controllers\API\ContributorController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\DistributionController;
use App\Http\Controllers\API\LegalController;
use App\Http\Controllers\API\MusicStoreController;
use App\Http\Controllers\API\OperatorController;
use App\Http\Controllers\API\BankWithdrawController;
use App\Http\Controllers\API\PaypalWithdrawController;
use App\Http\Controllers\API\PlatformController;
use App\Http\Controllers\API\Services\PlaylistPitchController;
use App\Http\Controllers\API\Services\YoutubeOacController;
use App\Http\Controllers\SubUser\UserMemberController;
use App\Http\Controllers\API\TrackController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\SubUser\SubuserController;
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
Route::post('/admin/login', [AuthController::class, 'adminLogin']);

Route::middleware(['auth:sanctum', 'adminApproval'])->group(function () {
    Route::get('subusers', [SubuserController::class, 'index']);
    Route::get('subusers/{id}', [SubuserController::class, 'show']);
    Route::post('subusers/invite', [UserMemberController::class, 'invite']);

    Route::get('/dashboard/user', [DashboardController::class, 'user']);

    Route::get('/users/profile', [UserController::class, 'getProfile']);
    Route::put('/users/profile', [UserController::class, 'updateProfile']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/email/verify', [AuthController::class, 'notice'])->name('verification.notice');
    Route::post('/email/verification-notification', [AuthController::class, 'resend'])->middleware(['throttle:6,1'])->name('verification.send');

    /* Artworks */
    Route::get('artworks/generate', [ArtworkTemplateController::class, 'generate']);

    /* Withdraws */
    Route::put('withdraw/paypal/{paypal}/status', [PaypalWithdrawController::class, 'updateStatusWithdraw'], ['as' => 'withdraw'])->middleware(['role:super-admin']);
    Route::apiResource('withdraw/paypal', PaypalWithdrawController::class, ['as' => 'withdraw'])->except('show');

    Route::put('withdraw/banks/{bank}/status', [BankWithdrawController::class, 'updateStatusWithdraw'], ['as' => 'withdraw'])->middleware(['role:super-admin']);
    Route::apiResource('withdraw/banks', BankWithdrawController::class, ['as' => 'withdraw'])->except('show');

    Route::get('announcements/notification', [AnnouncementController::class, 'notification']);

    Route::get('users/notifications', [UserController::class, 'unreadNotification']);
    Route::post('users/notifications', [UserController::class, 'readNotification']);

    Route::patch('distributions/{distribution}/status', [DistributionController::class, 'updateStatus'])->middleware(['role:admin|super-admin']);

    Route::patch('users/{user}/status', [UserController::class, 'updateStatus'])->middleware(['role:admin']);

    Route::get('analytics/{period}/artist/{artist}', [AnalyticController::class, 'showByPeriodAndArtist']);

    Route::apiResource('contributors', ContributorController::class);

    Route::get('balance/user', [AccountController::class, 'getBalanceByUserLoggedIn']);

    Route::get('deposits', [TransactionController::class, 'getDebitTransactions'])->middleware(['role:super-admin']);
    Route::post('deposits/transaction', [TransactionController::class, 'debit'])->middleware(['role:super-admin']);

    Route::apiResources([
        'tracks' => TrackController::class,
        'distributions' => DistributionController::class,
        'analytics' => AnalyticController::class,
        'artists' => ArtistController::class,
        'transactions' => TransactionController::class,
        'services/playlist-pitches' => PlaylistPitchController::class,
        'services/youtube-oac' => YoutubeOacController::class
    ]);

    Route::get('distributions/{distribution}/tracks', [TrackController::class, 'showTracksByDistributionId']);

    Route::group(['middleware' => ['role:admin|super-admin']], function () {
        Route::get('/dashboard/admin', [DashboardController::class, 'admin']);

        Route::get('/users/log', [UserController::class, 'getUsersWithLog']);

        Route::get('announcements', [AnnouncementController::class, 'index']);
        Route::post('announcements', [AnnouncementController::class, 'store']);

        Route::apiResource('legals', LegalController::class)->except(['update', 'show']);
        Route::post('legals/bulk', [LegalController::class, 'bulkStore']);

        Route::apiResource('artworks', ArtworkTemplateController::class)->except(['show', 'generate']);

        Route::apiResources([
            'accounts' => AccountController::class,
            'stores' => MusicStoreController::class,
            'platforms' => PlatformController::class,
            'users' => UserController::class,
            'genres' => GenreController::class,
            'banks' => BankController::class,
        ]);
    });

    Route::middleware(['role:super-admin'])->group(function () {
        Route::apiResources([
            'admins' => AdminController::class,
            'operators' => OperatorController::class,
        ]);
    });
});
