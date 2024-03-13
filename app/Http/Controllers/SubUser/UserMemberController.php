<?php

namespace App\Http\Controllers\SubUser;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Mpociot\Teamwork\Facades\Teamwork;

class UserMemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function invite(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validate([
                'email' => 'required|email',
                'owner_id' => 'required|numeric|max_digits:10|exists:teams,owner_id',
            ]);

            $teamModel = config('teamwork.team_model');
            $team = $teamModel::where('owner_id', $data['owner_id'])->first();

            if (!Teamwork::hasPendingInvite($request->email, $team)) {
                Teamwork::inviteToTeam($request->email, $team, function ($invite) {
                    Mail::send('subuser.invite', ['team' => $invite->team, 'invite' => $invite], function ($m) use ($invite) {
                        $m->to($invite->email)->subject('Invitation to join team ' . $invite->team->name);
                    });
                    // Send email to user
                });
            } else {
                return response()->json(['message' => 'The email address is already invited to the team.'], 409);
            }

            DB::commit();

            return response()->json(['message' => 'Invited successfully.'], 201);
        } catch (\Throwable $th) {
            DB::rollback();
            
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}
