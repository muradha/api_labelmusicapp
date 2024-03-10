<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreDistributionRequest;
use App\Http\Requests\API\UpdateDistributionRequest;
use App\Http\Resources\DistributionCollection;
use App\Http\Resources\DistributionResource;
use App\Models\Distribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use function PHPSTORM_META\map;

class DistributionController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Distribution::class, 'distribution');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): DistributionCollection
    {
        $user = Auth::user();

        if ($user->hasAnyRole('admin', 'operator', 'super-admin')) {
            $distributions = Distribution::with(['artists', 'tracks'])->get();
        } else {
            $distributions = Distribution::with(['artists', 'tracks'])->where('user_id', $user->id)->get();
        }

        return new DistributionCollection($distributions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDistributionRequest $request)
    {
        DB::beginTransaction();

        $uploadedCover = null;
        $uploadedTracksFile = null;
        try {
            $data = $request->validated();

            if ($request->hasFile('cover') || !empty($data['cover'])) {
                $uploadedCover = Storage::disk('public')->put('cover', $data['cover']);
            }

            $data['cover'] = $uploadedCover;

            $data['upc'] = $data['upc'] ?: rand(1000000000000, 9999999999999);

            $data['user_id'] = Auth::user()->id;

            $distribution = Distribution::create($data);

            $tracks = collect($data['tracks'])->map(function ($item) {
                if (empty($item['ISRC'])) {
                    $item['ISRC'] = rand(100000000000000, 999999999999999);
                }
                if (!empty($item['release_file']) && $item['release_file']) {
                    $item['file'] = Storage::disk('public')->put('tracks', $item['release_file']);
                }
                return $item;
            });

            $uploadedTracksFile = $tracks->pluck('file')->toArray();

            $createdTracks = $distribution->tracks()->createMany($tracks->all());
            $distribution->artists()->sync($data['artists']);

            foreach ($createdTracks as $key => $track) {
                $track->artists()->sync($data['tracks'][$key]['artists']);
                $track->contributors()->sync($data['tracks'][$key]['contributors']);
            }

            $distribution->store()->create([
                'platforms' => $data['platforms'],
                'territories' => $data['territories'],
            ]);

            DB::commit();

            return new DistributionResource($distribution);
        } catch (\Throwable $th) {
            DB::rollBack();

            if ($uploadedCover && !empty($uploadedCover)) {
                Storage::disk('public')->exists($uploadedCover) && Storage::disk('public')->delete($uploadedCover);
            }

            if ($uploadedTracksFile && count($uploadedTracksFile) > 0) {
                foreach ($uploadedTracksFile as $key => $item) {
                    if (!empty($item) && Storage::disk('public')->exists($item)) {
                        Storage::disk('public')->delete($item);
                    }
                }
            }

            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Distribution $distribution): DistributionResource
    {
        return new DistributionResource($distribution->load('artists', 'store'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDistributionRequest $request, Distribution $distribution)
    {
        DB::beginTransaction();

        $uploadedCover = null;
        try {
            $data = $request->validated();

            $isCoverExist = Storage::disk('public')->exists($distribution->cover);

            if ((!empty($data['cover']) || $request->hasFile('cover')) && !$isCoverExist) {
                if ($distribution->cover && $isCoverExist) {
                    Storage::disk('public')->delete($distribution->cover);
                }
                $uploadedCover = Storage::disk('public')->put('cover', $data['cover']);
            } else {
                $uploadedCover = $distribution->cover;
            }

            $data['cover'] = $uploadedCover;

            if (empty($data['upc'])) {
                $data['upc'] = empty($distribution->upc) ?: rand(1000000000000, 9999999999999);
            }

            $distribution->update($data);

            $artists = collect($data['artists']);

            $artists->transform(fn ($artist) => [
                'artist_id' => $artist['id'],
                'role' => $artist['role'],
            ]);

            $distribution->artists()->sync($artists);

            $distribution->store()->update([
                'platforms' => $data['platforms'],
                'territories' => $data['territories'],
            ]);

            DB::commit();

            return new DistributionResource($distribution->load('artists', 'store'));
        } catch (\Throwable $th) {
            DB::rollBack();

            if ($uploadedCover && !empty($uploadedCover)) {
                Storage::disk('public')->exists($uploadedCover) && Storage::disk('public')->delete($uploadedCover);
            }

            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Distribution $distribution)
    {
        $distribution->delete();

        return new DistributionResource($distribution);
    }

    public function updateStatus(Request $request, Distribution $distribution): DistributionResource
    {
        $data = $request->validate([
            'verification_status' => 'required|string|in:PENDING,REJECTED,APPROVED'
        ]);

        $distribution->update($data);

        return new DistributionResource($distribution);
    }
}
