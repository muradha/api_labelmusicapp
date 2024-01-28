<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreUserRequest;
use App\Http\Requests\API\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $users = User::whereHas('roles', fn ($query) => $query->where('name', 'user'))->get();

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
        $notifications = Auth::user()->unreadNotifications->first();

        $data = [];
        if ($notifications) {
            $data = [
                'id' => $notifications->id,
                'title' => $notifications->data['title'],
                'content' => $notifications->data['content'],
            ];
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
}
