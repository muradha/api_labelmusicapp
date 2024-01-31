<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Withdraws\StoreBankWithdrawRequest;
use App\Http\Resources\WithdrawResource;
use App\Models\BankWithdraw;
use App\Models\Withdraw;
use Illuminate\Http\Request;

class BankWithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
