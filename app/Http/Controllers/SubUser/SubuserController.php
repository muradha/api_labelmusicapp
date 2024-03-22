<?php

namespace App\Http\Controllers\SubUser;

use App\Http\Controllers\Controller;
use App\Http\Resources\OwnerResource;
use App\Models\SubUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SubuserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasAnyRole('super-admin', 'admin')) {
            $owners = User::has('owner')->get();
            $data = $owners;
        } else {
            $owners = SubUser::where('owner_id', $user->id)->first();
            $data = $owners->users;
        };

        return OwnerResource::collection($owners);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $owner = SubUser::where('owner_id', $id)->first();

        return response()->json([
            'data' => $owner->users ?? []
        ]);
    }

    public function subUserParents()
    {
        $parents = User::doesnthave('owner')->whereHas('roles', fn ($query) => $query->where('name', 'user')->orWhere('name', 'sub-user'))->get();

        return OwnerResource::collection($parents);
    }
}
