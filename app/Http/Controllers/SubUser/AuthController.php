<?php

namespace App\Http\Controllers\SubUser;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Mpociot\Teamwork\Facades\Teamwork;

class AuthController extends Controller
{
    public function register($token): View
    {
        if ($token) {
            $invite = Teamwork::getInviteFromAcceptToken($token);
            if (!$invite) {
                throw new InvalidSignatureException();
            }
        }

        $user = User::where('email', $invite->email)->first();

        if ($user) {
            if ($invite) {
                $user->attachTeam($invite->team);
                $invite->delete();
            } 

            $user->syncRoles(['sub-user']);

            $emails = DB::table('team_invites')->where('email', $user->email)->pluck('id')->toArray();

            DB::table('team_invites')->whereIn('id', $emails)->delete();

            return view('subuser.accept-invite');
        }

        return view('subuser.register', [
            'token' => $token,
        ]);
    }

    public function storeRegister(UserRegisterRequest $request, $token)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $invite = null;

            if ($token) {
                $invite = Teamwork::getInviteFromAcceptToken($token);
                if (!$invite) {
                    throw new InvalidSignatureException();
                }
            }

            $isExistEmail = DB::table('team_invites')->where(['email' => $data['email']], ['token' => $token])->exists();

            if (!$isExistEmail) {
                return back()->withErrors(['email' => 'Email must be same with your invite email.']);
            }

            $user = User::firstOrCreate(['email' => $request->email], $data);

            if ($invite) {
                $user->attachTeam($invite->team);
                $invite->delete();
            }

            $user->syncRoles(['sub-user']);

            $emails = DB::table('team_invites')->where('email', $data['email'])->pluck('id')->toArray();

            DB::table('team_invites')->whereIn('id', $emails)->delete();

            DB::commit();

            return redirect(URL::temporarySignedRoute(
                'subuser.accept-invite',
                now()->addMinutes(5),
                ['token' => $token]
            ));
        } catch (\Throwable $th) {
            DB::rollback();

            return back()->withErrors(['email' => $th->getMessage()]);
        }
    }
    /**
     * Accept the given invite.
     * @param $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptInvite()
    {
        return view('subuser.accept-invite');
    }
}
