<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreArtistRequest;
use App\Http\Requests\API\UpdateArtistRequest;
use App\Http\Resources\ArtistCollection;
use App\Http\Resources\ArtistResource;
use App\Models\Artist;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArtistController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Artist::class, 'artist');    
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->hasAnyRole('admin', 'operator')) {
            $artists = Artist::all();
        }else{
            $artists = $user->artists()->get();
        }

        return (new ArtistCollection($artists))->response()->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArtistRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('artists', 'public');
            $data['photo'] = $path;
        }

        $artist = Artist::create($data);

        return (new ArtistResource($artist))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Artist $artist)
    {
        return (new ArtistResource($artist))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArtistRequest $request, Artist $artist)
    {
        $data = $request->validated();

        if ($request->hasFile(('photo'))) {
            if (!empty($artist->photo) && Storage::disk('public')->exists($artist->photo)) {
                Storage::disk('public')->delete($artist->photo);
            }
            $path = $request->file('photo')->store('artists', 'public');
            $data['photo'] = $path;
        } else {
            $data['photo'] = $artist->photo;
        }

        $isSuccess = $artist->update($data);

        if (!$isSuccess) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Oops... something wrong !'
                ],
            ], 500));
        }

        return new ArtistResource($artist);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artist $artist)
    {
        if (!empty($artist->photo) && Storage::disk('public')->exists($artist->photo)) {
            Storage::disk('public')->delete($artist->photo);
        }

        $artist->delete();

        return new ArtistResource($artist);
    }
}
