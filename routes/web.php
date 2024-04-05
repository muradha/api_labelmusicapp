<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\SubUser\AuthController as SubUserAuthController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->middleware(['signed'])->name('verification.verify');

Route::get('/register/subuser/{token}', [SubUserAuthController::class, 'register'])->name('subuser.register');
Route::post('/register/subuser/{token}', [SubUserAuthController::class, 'storeRegister'])->name('subuser.register.store');
Route::get('/accept-invite/subuser/{token}', [SubUserAuthController::class, 'acceptInvite'])->name('subuser.accept-invite')->middleware(['signed']);

Route::get('test', function() {
    $role = User::find(9);
    $role->update([
        'password' => bcrypt('Password123#')
    ]);
    // $role->assignRole('admin');
    // dd($role->permissions->pluck('name'));
    // dd($users);
});