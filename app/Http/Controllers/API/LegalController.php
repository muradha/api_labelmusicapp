<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Legal\StoreLegalRequest;
use App\Http\Resources\LegalCollection;
use App\Http\Resources\LegalResource;
use App\Models\Legal;
use App\Models\User;
use App\Notifications\ConflictNotification;
use Illuminate\Database\Query\Builder;

class LegalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): LegalCollection
    {
        $legals = Legal::with('user')->get();

        return new LegalCollection($legals);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLegalRequest $request): LegalResource
    {
        $data = $request->validated();

        $legal = Legal::create($data);

        $legal->user->notify(new ConflictNotification($data['message'] ,$data['conflict_type']));

        return new LegalResource($legal->load('user'));
    }

    public function destroy(Legal $legal) : LegalResource {
        $user = User::findOrFail($legal->user_id);

        $user->notifications(function (Builder $query) {
            return $query->where('type', 'App\Notifications\ConflictNotification');
        })->delete();

        $legal->delete();

        return new LegalResource($legal->load('user'));
    }

    public function bulkStore(StoreLegalRequest $request)
    {
        $data = $request->validate([
            'legal' => 'required|array',
            'legal.*.message' => 'required|string|max:254',
            'legal.*.conflict_type' => 'required|string|in:URGENT,COPYRIGHT,LEGAL PENALTY',
            'legal.*.user_id' => 'required|numeric|exists:users,id',
        ]);

        foreach ($data as $key => $value) {
            foreach ($value as $item) {
                $user = User::find($item['user_id'])->first();

                $legals[] = [
                    'message' => $item['message'],
                    'conflict_type' => $item['conflict_type'],
                    'user_id' => $item['user_id'],
                    'username' => $user->name
                ];

                $user->notify(new ConflictNotification($item['message'] ,$item['conflict_type']));
            }
        }

        $removedUsername = collect($legals)->map(function ($item) {
            return [
                'message' => $item['message'],
                'conflict_type' => $item['conflict_type'],
                'user_id' => $item['user_id'],
            ];
        });

        $legal = Legal::insert($removedUsername->toArray());

        return response()->json([
            'data' => $legals,
        ]);
    }
}
