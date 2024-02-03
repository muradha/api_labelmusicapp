<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Withdraws\StorePaypalWithdrawRequest;
use App\Http\Resources\Withdraws\PaypalWithdrawCollection;
use App\Http\Resources\Withdraws\PaypalWithdrawResource;
use App\Models\PaypalWithdraw;
use App\Models\Withdraw;

class PaypalWithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $withdrawals = PaypalWithdraw::with('withdraw')->get();

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
        ]);

        if($paypalWithdraw){
            $withdraw = Withdraw::create([
                'name' => $data['name'],
                'amount' => $data['amount'],
                'address' => $data['address'],
                'province' => $data['province'],
                'city' => $data['city'],
                'postal_code' => $data['postal_code'],
                'withdrawable_id' => $paypalWithdraw->id,
                'withdrawable_type' => PaypalWithdraw::class
            ]);
        }
        
        return new PaypalWithdrawResource($paypalWithdraw->load('withdraw'));
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
