<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Admin\ArtistStoreRequest;
use App\Http\Requests\API\Admin\UpdateArtistRequest;
use App\Http\Resources\ArtistCollection;
use App\Http\Resources\ArtistResource;
use App\Models\Artist;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ArtistCollection
    {
        $artists = Artist::all();

        return new ArtistCollection($artists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ArtistStoreRequest $request)
    {
        $data = $request->validated();

        $artist = Artist::create($data);

        return (new ArtistResource($artist))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $artist)
    {
        $data = Artist::where('id', $artist)->first();

        if (empty($data)) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Artist not found'
                ],
            ], 404));
        }

        return (new ArtistResource($data))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArtistRequest $request, string $id)
    {
        $artist = Artist::where('id', $id)->first();

        if (empty($artist)) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Artist not found'
                ],
            ], 404));
        }

        $data = $request->validated();

        $isSuccess = $artist->update($data);

        if (!$isSuccess) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ''
                ],
            ], 500));
        }

        $updatedArtist = Artist::where('id', $id)->first();

        return new ArtistResource($updatedArtist);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $artist = Artist::where('id', $id)->first();

        if (empty($artist)) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Artist not found'
                ],
            ], 404));
        }

        $artist->delete();

        return response()->json([
            'success' => true
        ], 200);
    }
}
