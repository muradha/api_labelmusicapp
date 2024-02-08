<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Withdraws\StoreBankWithdrawRequest;
use App\Http\Requests\API\Withdraws\UpdateBankWithdrawRequest;
use App\Http\Resources\Withdraws\BankWithdrawCollection;
use App\Http\Resources\Withdraws\BankWithdrawResource;
use App\Models\BankWithdraw;
use App\Models\Withdraw;
use Illuminate\Support\Facades\Auth;

class BankWithdrawController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(BankWithdraw::class, 'bank');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasAnyRole('admin')) {
            $withdrawals = BankWithdraw::with('withdraw')->get();
        } else {
            $withdrawals = BankWithdraw::with('withdraw')->where('user_id', $user->id)->get();
        }

        return new BankWithdrawCollection($withdrawals);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBankWithdrawRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = Auth::user()->id;
        $bankWithdraw = BankWithdraw::create($data);

        if ($bankWithdraw) {
            $data['withdrawable_id'] = $bankWithdraw->id;
            $data['withdrawable_type'] = BankWithdraw::class;
            $withdraw = Withdraw::create($data);
        }

        return new BankWithdrawResource($bankWithdraw->load('withdraw'));
    }

    public function update(UpdateBankWithdrawRequest $request, BankWithdraw $bank)
    {
        $data = $request->validated();

        $bank->update($data);
        $bank->withdraw->update($data);

        return new BankWithdrawResource($bank->load('withdraw'));
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
