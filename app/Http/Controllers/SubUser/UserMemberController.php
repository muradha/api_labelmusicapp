<?php

namespace App\Http\Controllers\SubUser;

use App\Http\Controllers\Controller;
use App\Http\Resources\OwnerResource;
use App\Models\SubUser;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Mpociot\Teamwork\Facades\Teamwork;

class UserMemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function inviteParent(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'user_id' => 'required|numeric|exists:users,id'
            ]);

            $user = User::where('id', $request->only('user_id'))->first();

            if ($user->isTeamOwner()) {
                throw new HttpResponseException(response()->json([
                    'message' => 'User is a subusers parent'
                ], 409));
            }

            if ($user->hasAnyRole('super-admin', 'admin')) {
                throw new HttpResponseException(response()->json([
                    'message' => 'User is not right roles'
                ], 409));
            }

            $team = SubUser::create([
                'owner_id' => $user->id,
                'name' => $user->name . "'s Team",
            ]);

            $user->attachTeam($team);

            DB::commit();

            return new OwnerResource($user);
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function invite(Request $request)
    {
        try {
            DB::beginTransaction();
            $owner_id = null;
            $user = Auth::user();

            $data = $request->validate([
                'email' => 'required|email|max:150',
                'owner_id' => [Rule::requiredIf($user->hasAnyRole('admin', 'super-admin')), 'numeric', 'max_digits:10', 'exists:teams,owner_id'],
            ]);

            $invitedUser = User::where('email', $data['email'])->first();

            if ($invitedUser && $invitedUser->isTeamOwner()) {
                throw new HttpResponseException(response()->json([
                    'message' => 'User is a subusers parent'
                ]));
            }

            if ($user->hasAnyRole('admin', 'super-admin')) {
                $owner_id = $data['owner_id'];
            } else {
                $owner_id = $user->id;
            }

            $teamModel = config('teamwork.team_model');
            $team = $teamModel::where('owner_id', $owner_id)->first();

            if (!$team) {
                throw new HttpResponseException(response()->json([
                    'message' => 'Owner not found'
                ], 409));
            }

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
