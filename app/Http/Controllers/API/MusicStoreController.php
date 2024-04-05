<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MusicStoreCollection;
use App\Http\Resources\MusicStoreResource;
use App\Models\MusicStore;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MusicStoreController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view shops|create shops|edit shops|delete shops'], ['only' => ['index', 'show']]);
        $this->middleware(['permission:view shops'], ['only' => ['index', 'show']]);
        $this->middleware(['permission:create shops'], ['only' => ['store']]);
        $this->middleware(['permission:edit shops'], ['only' => ['update']]);
        $this->middleware(['permission:delete shops'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = MusicStore::all();

        return new MusicStoreCollection($stores);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:music_stores,name',
        ]);

        $store = MusicStore::create($data);

        return new MusicStoreResource($store);
    }

    /**
     * Display the specified resource.
     */
    public function show(MusicStore $store)
    {
        return new MusicStoreResource($store);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MusicStore $store)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('music_stores', 'name')->ignore($store->id)],
        ]);

        $store->update($data);

        return new MusicStoreResource($store);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MusicStore $store)
    {
        $store->delete();

        return new MusicStoreResource($store);
    }
}
