<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Legal\StoreLegalRequest;
use App\Http\Resources\LegalCollection;
use App\Http\Resources\LegalResource;
use App\Models\Legal;
use App\Models\User;
use App\Notifications\ConflictNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class LegalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $legals = Legal::all();

        return new LegalCollection($legals);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLegalRequest $request)
    {
        $data = $request->validated();

        foreach ($data as $key => $value) {
            foreach ($value as $item) {
                $legals[] = [
                    'message' => $item['message'],
                    'conflict_type' => $item['conflict_type'],
                    'user_id' => $item['user_id'],
                ];

                $user = User::find($item['user_id']);

                $user->notify(new ConflictNotification($item['message'] ,$item['conflict_type']));
            }
        }

        $legal = Legal::insert($legals);

        return response()->json([
            'data' => $legals,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
