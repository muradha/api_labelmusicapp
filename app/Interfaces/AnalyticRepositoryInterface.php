<?php

namespace App\Interfaces;

interface AnalyticRepositoryInterface
{
  public function getArtistAnalyticsByPeriod($artistId, $userId = null);
  public function getAnalyticsForArtist($artistId, $userId = null);
}
