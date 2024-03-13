<?php

namespace App\Http\Controllers\SubUser;

use App\Http\Controllers\Controller;
use App\Models\SubUser;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SubuserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $owners = User::has('owner')->get();

        return response()->json([
            'data' => $owners
        ]);
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
}
