<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Genre\StoreGenreRequest;
use App\Http\Requests\API\Genre\UpdateGenreRequest;
use App\Http\Resources\GenreCollection;
use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view genres|create genres|edit genres|delete genres'], ['only' => ['index', 'show']]);
        $this->middleware(['permission:view genres'], ['only' => ['index', 'show']]);
        $this->middleware(['permission:create genres'], ['only' => ['store']]);
        $this->middleware(['permission:edit genres'], ['only' => ['update']]);
        $this->middleware(['permission:delete genres'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $genres = Genre::all();

        return new GenreCollection($genres);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGenreRequest $request)
    {
        $data = $request->validated();

        $genres = Genre::create($data);

        return (new GenreResource($genres))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Genre $genre)
    {
        if (empty($genre)) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Genre not found'
                ],
            ], 404));
        }

        return new GenreResource($genre);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        if (empty($genre)) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Genre not found'
                ],
            ], 404));
        }

        $data = $request->validated();
        $isSuccess = $genre->update($data);

        if (!$isSuccess) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Oops... something wrong !'
                ],
            ], 500));
        }

        return new GenreResource($genre);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Genre $genre)
    {
        if (empty($genre)) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Genre not found'
                ],
            ], 404));
        }

        $genre->delete();

        return new GenreResource($genre);
    }
}
