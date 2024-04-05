<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Analytic\StoreAnalyticRequest;
use App\Http\Requests\API\Analytic\UpdateAnalyticRequest;
use App\Http\Resources\AnalyticCollection;
use App\Http\Resources\AnalyticResource;
use App\Interfaces\AnalyticRepositoryInterface;
use App\Models\Analytic;
use App\Models\Artist;
use App\Policies\AnalyticPolicy;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AnalyticController extends Controller
{
    private AnalyticRepositoryInterface $analyticRepository;

    public function __construct(AnalyticRepositoryInterface $analyticRepository)
    {
        $this->authorizeResource(Analytic::class, 'analytic');
        $this->analyticRepository = $analyticRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $artists = Artist::all();
        return response()->json([
            'data' => $artists
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

        $user = Auth::user();

        if ($user->hasAnyRole('admin', 'super-admin', 'operator')) {
            $analytics = $this->analyticRepository->getArtistAnalyticsByPeriod($analytic->artist_id);
        } else {
            $analytics = $this->analyticRepository->getArtistAnalyticsByPeriod($analytic->artist_id, $user->id);
        }

        return (new AnalyticResource($analytic->load('artist', 'stores')))->additional(['analytics' => $analytics])->response()->setStatusCode(201);
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

        $user = Auth::user();

        if ($user->hasAnyRole('admin', 'super-admin', 'operator')) {
            $analytics = $this->analyticRepository->getArtistAnalyticsByPeriod($analytic->artist_id);
        } else {
            $analytics = $this->analyticRepository->getArtistAnalyticsByPeriod($analytic->artist_id, $user->id);
        }

        return (new AnalyticResource($analytic->load('artist', 'stores')))->additional(['analytics' => $analytics]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Analytic $analytic)
    {
        $analytic->delete();

        return new AnalyticResource($analytic);
    }

    public function showByArtist(Artist $artist)
    {
        $user = Auth::user();

        if ($user->hasAnyRole('super-admin', 'admin', 'operator')) {
            $artists = $this->analyticRepository->getAnalyticsForArtist($artist->id);

            $analytics = $this->analyticRepository->getArtistAnalyticsByPeriod($artist->id);
        } else {
            $artists = $this->analyticRepository->getAnalyticsForArtist($artist->id, $user->id);

            $analytics = $this->analyticRepository->getArtistAnalyticsByPeriod($artist->id, $user->id);
        }

        if (empty($analytics)) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Analytic Not Found'
                ]
            ], 404));
        }

        return (new AnalyticCollection($artists))->additional(['analytics' => $analytics]);
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

        $artists = Analytic::with('stores', 'artist')->whereBetween('period', [$startDate, $endDate])->where('artist_id', $artist->id)->get();

        $analytics = Analytic::where('artist_id', $artist->id)->whereBetween('period', [$startDate, $endDate])
            ->select(
                DB::raw('YEAR(period) as year'),
                DB::raw('MONTHNAME(period) as month'),
                DB::raw('SUM(analytic_store.revenue) as total_revenue'),
                DB::raw('SUM(analytic_store.streaming) as total_streaming'),
                DB::raw('SUM(analytic_store.download) as total_download'),
            )
            ->join('analytic_store', 'analytic_store.analytic_id', '=', 'analytics.id')
            ->groupBy(DB::raw('YEAR(period)'), DB::raw('MONTH(period)'))
            ->orderBy(DB::raw('YEAR(period)'), 'ASC')
            ->orderBy(DB::raw('MONTH(period)'), 'ASC')
            ->get();

        if (empty($analytics)) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Analytic Not Found'
                ]
            ], 404));
        }

        return (new AnalyticCollection($artists))->additional(['analytics' => $analytics]);
    }
}
