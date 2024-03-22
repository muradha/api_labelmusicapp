<?php

namespace App\Http\Controllers\API\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Services\StoreYoutubeOacRequest;
use App\Http\Requests\API\Services\UpdateYoutubeOacRequest;
use App\Http\Resources\Services\YoutubeOacCollection;
use App\Http\Resources\Services\YoutubeOacResource;
use App\Models\Service;
use App\Models\YoutubeOac;

class YoutubeOacController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(YoutubeOac::class, 'youtube_oac');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = YoutubeOac::with('service')->get();

        return new YoutubeOacCollection($services);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreYoutubeOacRequest $request)
    {
        $data = $request->validated();

        $youtubeOac = YoutubeOac::create($data);

        if ($youtubeOac) {
            $data['serviceable_id'] = $youtubeOac->id;
            $data['serviceable_type'] = YoutubeOac::class;

            $service = Service::create($data);
        }

        return new YoutubeOacResource($youtubeOac);
    }

    /**
     * Display the specified resource.
     */
    public function show(YoutubeOac $youtube_oac)
    {
        return new YoutubeOacResource($youtube_oac);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateYoutubeOacRequest $request, YoutubeOac $youtube_oac)
    {
        $data = $request->validated();

        $isSuccess = $youtube_oac->update($data);

        if ($isSuccess && $youtube_oac->service) {
            $youtube_oac->service->update($data);
        }

        return new YoutubeOacResource($youtube_oac);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(YoutubeOac $youtube_oac)
    {
        $youtube_oac->delete();

        return new YoutubeOacResource($youtube_oac);
    }
}
