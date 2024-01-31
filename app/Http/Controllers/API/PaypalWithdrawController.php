<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Withdraws\StorePaypalWithdrawRequest;
use App\Http\Resources\PaypalWithdrawCollection;
use App\Http\Resources\WithdrawCollection;
use App\Http\Resources\WithdrawResource;
use App\Models\PaypalWithdraw;
use App\Models\Withdraw;
use Illuminate\Http\Request;

class PaypalWithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $withdrawals = Withdraw::with('withdrawable')->get();

        return new WithdrawCollection($withdrawals);
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
        
        return new WithdrawResource($withdraw);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
