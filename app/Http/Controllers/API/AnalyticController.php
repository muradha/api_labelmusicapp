<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Analytic\StoreAnalyticRequest;
use App\Http\Requests\API\Analytic\UpdateAnalyticRequest;
use App\Http\Resources\AnalyticCollection;
use App\Http\Resources\AnalyticResource;
use App\Models\Analytic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $analytics = Analytic::with('stores')->get();

        return new AnalyticCollection($analytics);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAnalyticRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = Auth::user()->id;
        
        $analytic = Analytic::create($data);

        return (new AnalyticResource($analytic))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Analytic $analytic)
    {
        return new AnalyticResource($analytic);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAnalyticRequest $request, Analytic $analytic)
    {
        $data = $request->validated();

        $analytic->update($data);

        return new AnalyticResource($analytic);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Analytic $analytic)
    {
        $analytic->delete();

        return new AnalyticResource($analytic);
    }
}
