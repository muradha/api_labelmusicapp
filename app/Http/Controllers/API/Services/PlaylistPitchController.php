<?php

namespace App\Http\Controllers\API\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Services\StorePlaylistPitchRequest;
use App\Http\Resources\Services\PlaylistPitchCollection;
use App\Http\Resources\Services\PlaylistPitchResource;
use App\Models\PlaylistPitch;
use App\Models\Service;

class PlaylistPitchController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin|user']);
        $this->middleware(['role:user'], ['only' => ['store']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = PlaylistPitch::with('service')->get();

        return new PlaylistPitchCollection($services);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlaylistPitchRequest $request)
    {
        $data = $request->validated();

        $playlistPitch = PlaylistPitch::create($data);

        if ($playlistPitch) {
            $data['serviceable_id'] = $playlistPitch->id;
            $data['serviceable_type'] = PlaylistPitch::class;

            $service = Service::create($data);
        }

        return new PlaylistPitchResource($playlistPitch);
    }

    /**
     * Display the specified resource.
     */
    public function show(PlaylistPitch $playlist_pitch)
    {
        return new PlaylistPitchResource($playlist_pitch);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePlaylistPitchRequest $request, PlaylistPitch $playlist_pitch)
    {
        $data = $request->validated();

        $isSuccess = $playlist_pitch->update($data);

        if ($isSuccess && $playlist_pitch->service) {
            $playlist_pitch->service->update($data);
        }

        return new PlaylistPitchResource($playlist_pitch);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlaylistPitch $playlist_pitch)
    {
        $playlist_pitch->delete();

        return new PlaylistPitchResource($playlist_pitch);
    }
}
