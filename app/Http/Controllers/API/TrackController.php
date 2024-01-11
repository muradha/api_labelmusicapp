<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Tracks\StoreTrackRequest;
use App\Http\Requests\API\Tracks\UpdateTrackRequest;
use App\Http\Resources\TrackCollection;
use App\Http\Resources\TrackResource;
use App\Models\Track;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tracks = Track::all();

        return new TrackCollection($tracks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTrackRequest $request)
    {
        $data = $request->validated();

        if($request->hasFile('file')) {
            $data['file'] = $request->file('file')->store('tracks', 'public');
        }

        $data['ISRC'] = rand(100000000000000, 999999999999999);

        $track = Track::create($data);
        $track= Track::findOrFail(1);
        $track->distributions()->attach($data['distribution_id']);

        return (new TrackResource($track))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Track $track)
    {
        return new TrackResource($track);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTrackRequest $request, Track $track)
    {
        $data = $request->validated();

        if($request->hasFile('file')) {
            if($track->file && Storage::disk('public')->exists($track->file)){
                Storage::disk('public')->delete($track->file);
            }
            $data['file'] = $request->file('file')->store('tracks', 'public');
        }else{
            $data['file'] = $track->file;
        }

        $isSuccess = $track->update($data);

        if (!$isSuccess) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Track not found'
                ],
            ], 404));
        }
        return new TrackResource($track);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Track $track)
    {
        if ($track->file && Storage::disk('public')->exists($track->file)) {
            Storage::disk('public')->delete($track->file);
        }

        $track->delete();

        return new TrackResource($track);
    }
}
