<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Withdraws\StorePaypalWithdrawRequest;
use App\Http\Requests\API\Withdraws\UpdatePaypalWithdrawRequest;
use App\Http\Resources\Withdraws\PaypalWithdrawCollection;
use App\Http\Resources\Withdraws\PaypalWithdrawResource;
use App\Models\PaypalWithdraw;
use App\Models\Withdraw;
use Illuminate\Support\Facades\Auth;

class PaypalWithdrawController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(PaypalWithdraw::class, 'paypal');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasAnyRole('admin', 'super-admin', 'operator')) {
            $withdrawals = PaypalWithdraw::with('withdraw')->get();
        } else {
            $withdrawals = PaypalWithdraw::with('withdraw')->where('user_id', $user->id)->get();
        }

        return new PaypalWithdrawCollection($withdrawals);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaypalWithdrawRequest $request)
    {
        $data = $request->validated();

        $paypalWithdraw = PaypalWithdraw::create([
            'email' => $data['email'],
            'user_id' => Auth::user()->id,
        ]);

        if ($paypalWithdraw) {
            $withdraw = Withdraw::create([
                'name' => $data['name'],
                'amount' => $data['amount'],
                'address' => $data['address'],
                'country' => $data['country'],
                'province' => $data['province'],
                'city' => $data['city'],
                'postal_code' => $data['postal_code'],
                'withdrawable_id' => $paypalWithdraw->id,
                'withdrawable_type' => PaypalWithdraw::class
            ]);
        }

        return new PaypalWithdrawResource($paypalWithdraw->load('withdraw'));
    }

    public function update(UpdatePaypalWithdrawRequest $request, PaypalWithdraw $paypal)
    {
        $data = $request->validated();

        $paypal->update($data);
        $paypal->withdraw->update($data);

        return new PaypalWithdrawResource($paypal->load('withdraw'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaypalWithdraw $paypal)
    {
        $data = $paypal->load('withdraw');
        $paypal->delete();

        return new PaypalWithdrawResource($data);
    }
}
