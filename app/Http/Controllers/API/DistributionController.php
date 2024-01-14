<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreDistributionRequest;
use App\Http\Requests\API\UpdateDistributionRequest;
use App\Http\Resources\DistributionCollection;
use App\Http\Resources\DistributionResource;
use App\Models\Distribution;
use Illuminate\Http\Request;

class DistributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): DistributionCollection
    {
        $distributions = Distribution::with(['artist', 'tracks'])->get();

        return new DistributionCollection($distributions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDistributionRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('cover', 'public');
        }
        $data['UPC'] = rand(1000000000000, 9999999999999);
        
        $distribution = Distribution::create($data);

        return new DistributionResource($distribution);
    }

    /**
     * Display the specified resource.
     */
    public function show(Distribution $distribution): DistributionResource
    {
        return new DistributionResource($distribution);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDistributionRequest $request, Distribution $distribution)
    {
        $distribution->update($request->validated());

        return new DistributionResource($distribution);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Distribution $distribution)
    {
        $distribution->delete();

        return new DistributionResource($distribution);
    }
}
