<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserLoginRequest;
use App\Http\Requests\API\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();
        $user->assignRole('user');

        return (new UserResource($user))->response()->setStatusCode(201);
    }


    public function login(UserLoginRequest $request): UserResource
    {
        $data = $request->validated();

        $user = User::with('roles')->where('email', $data['email'])->first();

        if ($user->hasAnyRole('user') && Auth::attempt($data)) {
                $user->token = $user->createToken('auth_token')->plainTextToken;
        } else {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'User not found'
                ],
            ], 401));
        }

        return (new UserResource($user->load('profile')));
    }

    public function adminLogin(UserLoginRequest $request): UserResource
    {
        $data = $request->validated();

        $admin = User::with('roles')->where('email', $data['email'])->first();

        if ($admin->hasAnyRole('admin') && Auth::attempt($data)) {
            $admin->token = $admin->createToken('auth_token')->plainTextToken;
        } else {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'User not found'
                ],
            ], 401));
        }

        return (new UserResource($admin->load('profile')));
    }

    public function logout()
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function verify(Request $request)
    {
        $user = User::find($request->route('id'));

        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $request->route('hash'))) {
            throw new AuthorizationException();
        }

        if ($user->markEmailAsVerified()) event(new Verified($user));

        return view('verify_email');
    }

    public function notice()
    {
        return response()->json(['message' => 'Has not verified'], 401);
    }

    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent!']);
    }
}
