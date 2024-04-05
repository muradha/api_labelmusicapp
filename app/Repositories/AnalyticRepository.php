<?php

namespace App\Repositories;

use App\Interfaces\AnalyticRepositoryInterface;
use App\Models\Analytic;
use Illuminate\Support\Facades\DB;

class AnalyticRepository implements AnalyticRepositoryInterface
{
  public function getArtistAnalyticsByPeriod($artistId, $userId = null)
  {
    return Analytic::where('artist_id', $artistId)->where('user_id', $userId)
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

  /**
   * Get analytics for a specific artist and user.
   *
   * @param \App\Models\Artist $artist
   * @param \App\Models\User $user
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function getAnalyticsForArtist($artistId, $userId = null)
  {
    return Analytic::with('stores', 'artist')->where('artist_id', $artistId)->where('user_id', $userId)->get();
  }
}
