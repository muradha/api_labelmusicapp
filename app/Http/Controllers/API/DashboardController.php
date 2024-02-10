<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\Artist;
use App\Models\ArtworkTemplate;
use App\Models\Distribution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function admin() {
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

    public function user() {
        $user = Auth::user();
        $account = Account::with('bank')->where('user_id', $user->id)->first();

        return [  
            'account' => new AccountResource($account),
        ];
    }
}
