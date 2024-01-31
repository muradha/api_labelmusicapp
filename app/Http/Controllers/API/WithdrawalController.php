<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Withdraws\StoreWithdrawRequest;
use App\Models\Withdraw;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $withdrawals = Withdraw::with()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWithdrawRequest $request)
    {
        $data = $request->validated();

        $withdraw = Withdraw::create([
            'name' => $data['name'],
            'amount' => $data['amount'],
            'withdraw_type' => $data['withdraw_type'],
            
        ]);

        if($data['withdraw_type'] === 'paypal') {
            $withdraw->withdrawable()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'amount' => $data['amount'],
            ]);
        }else{
            $withdraw->withdrawable()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'amount' => $data['amount'],
            ]);
        }
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
