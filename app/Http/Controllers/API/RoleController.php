<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): JsonResponse {
        $roles = Role::whereKeyNot(Role::findByName('super-admin', 'web')->id)->get();
        
        return response()->json([
            'data' => $roles
        ]);
    }

    public function permissions(Role $role): JsonResponse {
        $permissions = $role->permissions()->pluck('name');

        return response()->json([
            'data' => $permissions
        ]);
    }

    public function updatePermissions(Request $request, Role $role) {
        $data = $request->validate([
            'abilities' => 'required|array'
        ]);

        $role->syncPermissions($data['abilities']);

        return response()->json([
            'data' => $role
        ]);
    }
}
