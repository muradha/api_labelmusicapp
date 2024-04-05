<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\Analytic;
use App\Models\Artist;
use App\Models\ArtworkTemplate;
use App\Models\Distribution;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function admin()
    {
        $user = Auth::user();
        $account = Account::with('bank')->where('user_id', $user->id)->first();
        $totalUsers = User::count();
        $totalDistribution = Distribution::count();
        $totalArtist = Artist::count();
        $totalArtworkTemplate = ArtworkTemplate::count();

        $pendingUsers = User::where('admin_approval', 'PENDING')->take(3)->latest()->get();

        return [
            'account' => new AccountResource($account),
            'pendingUsers' => $pendingUsers,
            'totalUsers' => $totalUsers,
            'totalDistribution' => $totalDistribution,
            'totalArtist' => $totalArtist,
            'totalArtworkTemplate' => $totalArtworkTemplate
        ];
    }

    public function user()
    {
        $user = Auth::user();

        $account = Account::with('bank')->where('user_id', $user->id)->first();

        $year = Date('Y');
        $startDate = Carbon::createFromDate($year, 1, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, 12, 31)->endOfMonth();

        if ($user->hasAnyRole('admin', 'super-admin', 'operator')) {
            $analytics = Analytic::whereBetween('period', [$startDate, $endDate])
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
        } else {
            $analytics = Analytic::where('user_id', $user->id)->whereBetween('period', [$startDate, $endDate])
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
        }

        return [
            'account' => new AccountResource($account),
            'analytics' => $analytics,
        ];
    }
}
