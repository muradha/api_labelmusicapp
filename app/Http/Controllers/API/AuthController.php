<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RulesPassword;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();
        $user->assignRole('user');

        Auth::loginUsingId($user->id);

        $user->token = $user->createToken('auth_token')->plainTextToken;
        $user->sendEmailVerificationNotification();

        return (new UserResource($user->load('profile', 'roles')))->response()->setStatusCode(201);
    }


    public function login(UserLoginRequest $request): UserResource
    {
        $data = $request->validated();

        $user = User::with('roles', 'profile')->where('email', $data['email'])->first();

        if ($user->hasAnyRole('user', 'sub-user') && Auth::attempt($data)) {
            $user->token = $user->createToken('auth_token')->plainTextToken;
        } else {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'User not found'
                ],
            ], 401));
        }

        return (new UserResource($user));
    }

    public function adminLogin(UserLoginRequest $request): UserResource
    {
        $data = $request->validated();

        $admin = User::with('roles', 'profile')->where('email', $data['email'])->first();

        if ($admin->hasAnyRole('admin', 'super-admin') && Auth::attempt($data)) {
            $admin->token = $admin->createToken('auth_token')->plainTextToken;
        } else {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'User not found'
                ],
            ], 401));
        }

        return (new UserResource($admin));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        $user->tokens()->delete();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function verify(Request $request)
    {
        $user = User::find($request->route('id'));

        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $request->route('hash'))) {
            throw new AuthorizationException();
        }

        if (!$user->hasVerifiedEmail()) {
            if ($user->markEmailAsVerified()) event(new Verified($user));
        }

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

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email'),
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => __($status)])
            : response()->json(['message' => __($status)], 422);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => ['required', 'confirmed', RulesPassword::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['status' => __($status)])
            : response()->json(['message' => __($status)], 422);
    }
}
