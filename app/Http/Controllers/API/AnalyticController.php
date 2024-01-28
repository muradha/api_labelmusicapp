<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Analytic\StoreAnalyticRequest;
use App\Http\Requests\API\Analytic\UpdateAnalyticRequest;
use App\Http\Resources\AnalyticCollection;
use App\Http\Resources\AnalyticResource;
use App\Models\Analytic;
use App\Models\Artist;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AnalyticController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $analytics = Analytic::orderBy('period', 'asc')->with('stores', 'artist')->get();

        $year = Date('Y');
        $startDate = Carbon::createFromDate($year, 1, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, 12, 31)->endOfMonth();

        $analytics = Analytic::with('artist')->whereBetween('period', [$startDate, $endDate])
            ->orderBy('period')
            ->get()
            ->groupBy(function ($analytic) {
                return $analytic->period->format('F Y');
            });

        foreach ($analytics as $key => $value) {
            foreach ($value as $item) {
                $analytic[] = [
                    'period' => $key,
                    'artist' => [
                        'id' => $item['artist']['id'],
                        'first_name' => $item['artist']['first_name'],
                        'last_name' => $item['artist']['last_name'],
                    ],
                ];
            }
        }

        $uniqueAnalytic = collect($analytic)->unique()->values()->all();

        return response()->json([
            'data' => $uniqueAnalytic
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAnalyticRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = Auth::user()->id;
        $data['period'] = $data['period'] . '-01';

        $analytic = Analytic::create($data);

        if ($analytic && $data['shops']) {
            $analytic->stores()->sync($data['shops']);
        }

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
        $data['period'] = $data['period'] . '-01';

        $isSuccess = $analytic->update($data);

        if ($isSuccess && $data['shops']) {
            $analytic->stores()->sync($data['shops']);
        }

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

    public function showByPeriodAndArtist(string $period, Artist $artist)
    {
        $validator = Validator::make(['period' => $period], [
            'period' => 'required|date_format:Y-m',
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'errors' => $validator->messages()
            ], 422));
        };

        $startDate = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $period)->endOfMonth();

        $analytics = Analytic::with('stores', 'artist')->whereBetween('period', [$startDate, $endDate])->where('artist_id', $artist->id)->get();

        if (empty($analytics)) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Analytic Not Found'
                ]
            ], 404));
        }

        return new AnalyticCollection($analytics);
    }
}
