<?php

namespace App\Http\Controllers\SubUser;

use App\Http\Controllers\Controller;
use App\Http\Resources\OwnerResource;
use App\Models\SubUser;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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

        return OwnerResource::collection($data);
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

    public function detachSubuser(User $user, Request $request) {
        $request->validate([
            'owner_id' => [Rule::requiredIf($user->hasAnyRole('admin', 'super-admin', 'operator')), 'numeric', 'max_digits:10', 'exists:teams,owner_id'],
        ]);

        if($user->hasAnyRole('super-admin', 'admin', 'operator')){
            $team = SubUser::where('owner_id', $request->only('owner_id'))->first();
        }else{
            $team = SubUser::where('owner_id', Auth::user()->id)->first();
        }

        if(!$team){
            throw new HttpResponseException(response()->json(['message' => 'User is not a subusers parent'], 409));
        }

        $user->detachTeam($team);

        return response()->json(['message' => 'User detached'], 200);
    }
}
