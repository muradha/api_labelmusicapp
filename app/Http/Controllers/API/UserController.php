<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreUserRequest;
use App\Http\Requests\API\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\ConflictNotification;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('profile')->whereHas('roles', fn ($query) => $query->where('name', 'user'))->get();

        return new UserCollection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);
        $user->assignRole('user');

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (empty($data['password'])) unset($data['password']);

        $user->update($data);

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return new UserResource($user);
    }

    public function getUsersWithLog()
    {
        $users = User::whereNotNull('last_login_time')->whereNotNull('last_login_ip')->get();

        return response()->json([
            'data' => $users,
        ]);
    }

    public function unreadNotification()
    {
        $notifications = Auth::user()->unreadNotifications->where('type', ConflictNotification::class);

        $data = null;
        if (!empty($notifications) && $notifications->count() > 0) {
            foreach ($notifications as $value) {
                $data[] = [
                    'id' => $value->id,
                    'message' => $value->data['message'],
                    'conflict_type' => $value->data['conflict_type'],
                ];
            }
        }

        return response()->json([
            'data' => $data
        ]);
    }

    public function readNotification(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:notifications,id',
        ]);

        $notification = DB::table('notifications')->where('id', $data['id'])->limit(1);

        if (!($notification->exists())) {
            throw new HttpResponseException(response()->json([
                'message' => 'Notification not found'
            ], 404));
        }

        $notification->update(['read_at' => now()]);

        return response()->json([
            'message' => 'Read announcement successfully',
        ]);
    }

    public function getProfile(): UserResource
    {
        $user = User::with('profile')->findOrFail(Auth::user()->id);

        return new UserResource($user);
    }

    public function updateProfile(Request $request): UserResource
    {
        $user_id = Auth::user()->id;
        $data = $request->validate([
            'name' => 'nullable|max:254|string|min:5',
            'email' => ['nullable', 'email', 'max:254', Rule::unique('users', 'email')->ignore($user_id)],
            'password' => ['nullable', 'current_password:sanctum'],
            'new_password' => ['nullable', 'sometimes', 'required_with:password', Password::defaults(), 'max:254', 'different:password'],
            'password_confirmation' => 'nullable|sometimes|required_with:password|same:new_password',
            'birth_date' => 'nullable|date',
            'phone_number' => 'nullable|max:20|string',
            'company_name' => 'nullable|max:100|string',
            'street' => 'nullable|max:100|string',
            'city' => 'nullable|max:100|string',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100'
        ]);

        $user = User::findOrFail($user_id);
        $data['password'] = $data['new_password'] ?? $user->password;
        $user->update($data);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return new UserResource($user);
    }
}
