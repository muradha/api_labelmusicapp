<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Tracks\StoreTrackRequest;
use App\Http\Requests\API\Tracks\UpdateTrackRequest;
use App\Http\Resources\TrackCollection;
use App\Http\Resources\TrackResource;
use App\Models\Author;
use App\Models\Composer;
use App\Models\Contributor;
use App\Models\Distribution;
use App\Models\Featuring;
use App\Models\Producer;
use App\Models\Track;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
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
        $tracks = Track::all();

        return new TrackCollection($tracks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTrackRequest $request)
    {
        $data = $request->validated();

        $distribution = Distribution::with('tracks')->findOrFail($data['distribution_id']);

        if ($distribution->tracks->count() >= 1 && $distribution->release_type === 'SINGLE') {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Track already exists in distribution'
                ],
            ], 404));
        }

        if ($request->hasFile('release_file')) {
            $data['file'] = $request->file('release_file')->store('tracks', 'public');
        }

        $data['ISRC'] = rand(100000000000000, 999999999999999);

        $track = Track::create($data);

        if (!empty($data['authors'])) {
            foreach ($request->authors as $key => $item) {
                $authors[] = [
                    'id' => $key,
                    'name' => $item['name'],
                    'track_id' => $track->id,
                ];
            }

            if (!empty($authors[0]['name'])) {
                $authors_id = collect($authors)->pluck('id');
                $track->authors()->whereNotIn('id', $authors_id)->delete();
                Author::upsert($authors, ['id', 'track_id'], ['name']);
            }
        }

        
        if (!empty($data['featurings'])) {
            foreach ($request->featurings as $key => $item) {
                $featurings[] = [
                    'id' => $key,
                    'name' => $item['name'],
                    'track_id' => $track->id,
                ];
            }
            
            if (!empty($featurings[0]['name'])) {
                $featurings_id = collect($featurings)->pluck('id');
                $track->featurings()->whereNotIn('id', $featurings_id)->delete();
                Featuring::upsert($featurings, ['id', 'track_id'], ['name']);
            }
        }

        
        if (!empty($data['producers'])) {
            foreach ($request->producers as $key => $item) {
                $producers[] = [
                    'id' => $key,
                    'name' => $item['name'],
                    'track_id' => $track->id,
                ];
            }
            
            if (!empty($producers[0]['name'])) {
                $producers_id = collect($producers)->pluck('id');
                $track->producers()->whereNotIn('id', $producers_id)->delete();
                Producer::upsert($producers, ['id', 'track_id'], ['name']);
            }
        }

        if (!empty($data['contributors'])) {
            foreach ($request->contributors as $key => $item) {
                $contributors[] = [
                    'id' => $key,
                    'name' => $item['name'],
                    'track_id' => $track->id,
                ];
            }

            if (!empty($contributors[0]['name'])) {
                $contributors_id = collect($contributors)->pluck('id');
                $track->contributors()->whereNotIn('id', $contributors_id)->delete();
                Contributor::upsert($contributors, ['id', 'track_id'], ['name']);
            }
        }

        if (!empty($data['composers'])) {
            foreach ($request->composers as $key => $item) {
                $composers[] = [
                    'id' => $key,
                    'name' => $item['name'],
                    'track_id' => $track->id,
                ];
            }

            if (!empty($composers[0]['name'])) {
                $composers_id = collect($composers)->pluck('id');
                $track->composers()->whereNotIn('id', $composers_id)->delete();
                Composer::upsert($composers, ['id', 'track_id'], ['name']);
            }
        }
        
        if(empty($data['music_stores'])) {
            $data['music_stores'] = [];
        }

        $track->musicStores()->sync($data['music_stores']);

        return (new TrackResource($track))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Track $track)
    {
        $data = $track->load('authors', 'featurings', 'producers', 'contributors', 'composers', 'musicStores');
        return new TrackResource($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTrackRequest $request, Track $track)
    {
        $data = $request->validated();

        if ($request->hasFile('release_file')) {
            if ($track->file && Storage::disk('public')->exists($track->file)) {
                Storage::disk('public')->delete($track->file);
            }
            $data['file'] = $request->file('release_file')->store('tracks', 'public');
        } else {
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
        
        if (!empty($data['authors'])) {
            foreach ($request->authors as $key => $item) {
                $authors[] = [
                    'id' => $key,
                    'name' => $item['name'],
                    'track_id' => $track->id,
                ];
            }

            if (!empty($authors[0]['name'])) {
                $authors_id = collect($authors)->pluck('id');
                $track->authors()->whereNotIn('id', $authors_id)->delete();
                Author::upsert($authors, ['id', 'track_id'], ['name']);
            }
        }

        
        if (!empty($data['featurings'])) {
            foreach ($request->featurings as $key => $item) {
                $featurings[] = [
                    'id' => $key,
                    'name' => $item['name'],
                    'track_id' => $track->id,
                ];
            }
            
            if (!empty($featurings[0]['name'])) {
                $featurings_id = collect($featurings)->pluck('id');
                $track->featurings()->whereNotIn('id', $featurings_id)->delete();
                Featuring::upsert($featurings, ['id', 'track_id'], ['name']);
            }
        }

        
        if (!empty($data['producers'])) {
            foreach ($request->producers as $key => $item) {
                $producers[] = [
                    'id' => $key,
                    'name' => $item['name'],
                    'track_id' => $track->id,
                ];
            }
            
            if (!empty($producers[0]['name'])) {
                $producers_id = collect($producers)->pluck('id');
                $track->producers()->whereNotIn('id', $producers_id)->delete();
                Producer::upsert($producers, ['id', 'track_id'], ['name']);
            }
        }

        if (!empty($data['contributors'])) {
            foreach ($request->contributors as $key => $item) {
                $contributors[] = [
                    'id' => $key,
                    'name' => $item['name'],
                    'track_id' => $track->id,
                ];
            }

            if (!empty($contributors[0]['name'])) {
                $contributors_id = collect($contributors)->pluck('id');
                $track->contributors()->whereNotIn('id', $contributors_id)->delete();
                Contributor::upsert($contributors, ['id', 'track_id'], ['name']);
            }
        }

        if (!empty($data['composers'])) {
            foreach ($request->composers as $key => $item) {
                $composers[] = [
                    'id' => $key,
                    'name' => $item['name'],
                    'track_id' => $track->id,
                ];
            }

            if (!empty($composers[0]['name'])) {
                $composers_id = collect($composers)->pluck('id');
                $track->composers()->whereNotIn('id', $composers_id)->delete();
                Composer::upsert($composers, ['id', 'track_id'], ['name']);
            }
        }

        if(empty($data['music_stores'])) {
            $data['music_stores'] = [];
        }

        $track->musicStores()->sync($data['music_stores']);


        return new TrackResource($track->load('musicStores'));
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
