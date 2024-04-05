<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlatformCollection;
use App\Http\Resources\PlatformResource;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlatformController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view platfomrs|create platfomrs|edit platfomrs|delete platfomrs'], ['only' => ['index', 'show']]);
        $this->middleware(['permission:view platfomrs'], ['only' => ['index', 'show']]);
        $this->middleware(['permission:create platfomrs'], ['only' => ['store']]);
        $this->middleware(['permission:edit platfomrs'], ['only' => ['update']]);
        $this->middleware(['permission:delete platfomrs'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $platforms = Platform::all();

        return new PlatformCollection($platforms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:platforms,name',
        ]);

        $platform = Platform::create($data);

        return (new PlatformResource($platform))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Platform $platform)
    {
        return new PlatformResource($platform);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Platform $platform)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('platforms', 'name')->ignore($platform->id)],
        ]);

        $platform->update($data);

        return new PlatformResource($platform);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Platform $platform)
    {
        $platform->delete();

        return new PlatformResource($platform);
    }
}
