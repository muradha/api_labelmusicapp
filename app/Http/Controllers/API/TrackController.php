<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Tracks\StoreTrackRequest;
use App\Http\Requests\API\Tracks\UpdateTrackRequest;
use App\Http\Resources\TrackCollection;
use App\Http\Resources\TrackResource;
use App\Models\Distribution;
use App\Models\Track;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TrackController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Track::class, 'track');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tracks = Track::with('musicStores')->get();

        return new TrackCollection($tracks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTrackRequest $request)
    {
        DB::beginTransaction();

        $release_file = null;
        try {
            $data = $request->validated();

            $release_file = Storage::disk('public')->put('tracks', $data['release_file']);
            $data['file'] = $release_file;

            $data['ISRC'] = rand(100000000000000, 999999999999999);

            $track = Track::create($data);

            $track->artists()->attach(collect($data['artists'])->map(fn ($item) => ['artist_id' => $item['id'], 'role' => $item['role']])->all());
            $track->contributors()->attach(collect($data['contributors'])->map(fn ($item) => ['contributor_id' => $item['id'], 'role' => $item['role']])->all());

            DB::commit();

            return (new TrackResource($track->load('contributors', 'artists')))->response()->setStatusCode(201);
        } catch (\Throwable $th) {
            if ($release_file && Storage::disk('public')->exists($release_file)) {
                Storage::disk('public')->delete($release_file);
            }

            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Track $track)
    {
        return new TrackResource($track->load('contributors', 'artists'));
    }

    public function showTracksByDistributionId(Distribution $distribution): TrackCollection
    {
        $tracks = Track::with(['contributors', 'artists'])->where('distribution_id', $distribution->id)->get();

        return new TrackCollection($tracks);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTrackRequest $request, Track $track)
    {
        DB::beginTransaction();

        $release_file = null;
        try {
            $data = $request->validated();

            if (empty($data['release_file'])) {
                $data['file'] = $track->file;
            } else {
                if ($track->file && Storage::disk('public')->exists($track->file)) {
                    Storage::disk('public')->delete($track->file);
                }

                $release_file = Storage::disk('public')->put('tracks', $data['release_file']);
                $data['file'] = $release_file;
            }

            $data['ISRC'] = rand(100000000000000, 999999999999999);

            $track->update($data);

            $track->artists()->sync(collect($data['artists'])->map(fn ($item) => ['artist_id' => $item['id'], 'role' => $item['role']])->all());
            $track->contributors()->sync(collect($data['contributors'])->map(fn ($item) => ['contributor_id' => $item['id'], 'role' => $item['role']])->all());

            DB::commit();

            return (new TrackResource($track->load('contributors', 'artists')))->response()->setStatusCode(201);
        } catch (\Throwable $th) {
            if ($release_file && Storage::disk('public')->exists($release_file)) {
                Storage::disk('public')->delete($release_file);
            }

            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 500);
        }
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
