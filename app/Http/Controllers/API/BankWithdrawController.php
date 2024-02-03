<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Withdraws\StoreBankWithdrawRequest;
use App\Http\Resources\Withdraws\BankWithdrawCollection;
use App\Http\Resources\Withdraws\BankWithdrawResource;
use App\Models\BankWithdraw;
use App\Models\Withdraw;

class BankWithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $withdraw = BankWithdraw::with('withdraw')->get();

        return new BankWithdrawCollection($withdraw);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBankWithdrawRequest $request)
    {
        $data = $request->validated();

        $bankWithdraw = BankWithdraw::create($data);

        if($bankWithdraw){
            $data['withdrawable_id'] = $bankWithdraw->id;
            $data['withdrawable_type'] = BankWithdraw::class;
            $withdraw = Withdraw::create($data);
        }

        return new BankWithdrawResource($bankWithdraw->load('withdraw'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankWithdraw $bank)
    {
        $data = $bank->load('withdraw');
        $bank->delete();

        return new BankWithdrawResource($data);
    }
}
