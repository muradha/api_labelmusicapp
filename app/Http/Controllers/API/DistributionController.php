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
use Illuminate\Support\Facades\Storage;

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

        if($user->hasAnyRole('admin', 'operator')) {
            $distributions = Distribution::with(['artist', 'tracks'])->get();
        } else {
            $distributions = Distribution::with(['artist', 'tracks'])->where('user_id', $user->id)->get();
        }

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

        $data['upc'] = rand(1000000000000, 9999999999999);

        $data['user_id'] = Auth::user()->id;
        
        $distribution = Distribution::create($data);

        return new DistributionResource($distribution);
    }

    /**
     * Display the specified resource.
     */
    public function show(Distribution $distribution): DistributionResource
    {
        return new DistributionResource($distribution->load('artist'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDistributionRequest $request, Distribution $distribution)
    {
        $data = $request->validated();

        if ($request->hasFile('cover')) {
            if($distribution->cover && Storage::disk('public')->exists($distribution->cover)) {
                Storage::disk('public')->delete($distribution->cover);
            }
            $data['cover'] = $request->file('cover')->store('cover', 'public');
        }else{
            $data['cover'] = $distribution->cover;
        }

        if(!$distribution->upc){
            $data['upc'] = rand(1000000000000, 9999999999999);
        }

        $distribution->update($data);

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

    public function updateStatus(Request $request, Distribution $distribution) : DistributionResource {
        $data = $request->validate([
            'verification_status' => 'required|string|in:PENDING,REJECTED,APPROVED'
        ]);

        $distribution->update($data);

        return new DistributionResource($distribution);
    }
}
