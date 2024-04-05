<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index() {
        $permissions = Permission::all()->pluck('name')->flatten()->toArray();

        return response()->json([
            'data' => $permissions
        ]);
    }

    public function userAdmin(){
        $users = User::whereHas('roles', fn ($query) => $query->where('name', 'admin')->orWhere('name', 'operator'))->get();

        return response()->json([
            'data' => $users
        ]);
    }

    public function showAdmin(User $user) {
        $abilities = $user->getAllPermissions()->pluck('name')->flatten()->toArray();
        $role_permissions = $user->getPermissionsViaRoles()->pluck('name')->flatten()->toArray();
        $user->abilities = $abilities;
        $user->role_permissions = $role_permissions;

        return new PermissionResource($user);
    }

    public function updatePermissionByUser(User $user,Request $request) {
        $data = $request->validate([
            'abilities' => 'required|array',
        ]);

        $user->syncPermissions($data['abilities']);

        return new PermissionResource($user);
    }
}
