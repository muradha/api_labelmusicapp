<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Withdraws\StoreBankWithdrawRequest;
use App\Http\Requests\API\Withdraws\UpdateBankWithdrawRequest;
use App\Http\Resources\Withdraws\BankWithdrawCollection;
use App\Http\Resources\Withdraws\BankWithdrawResource;
use App\Models\Account;
use App\Models\BankWithdraw;
use App\Models\Transaction;
use App\Models\Withdraw;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        if ($user->hasAnyRole('admin', 'super-admin', 'operator')) {
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
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $user = Auth::user();
            $account = Account::where('user_id', $user->id)->first();

            if (!$account || empty($account)) {
                throw new HttpResponseException(response()->json(['message' => 'Account not found'], 404));
            }

            if ($account->balance < $data['amount']) {
                throw new HttpResponseException(response()->json(['message' => 'Insufficient balance.'], 400));
            };

            $data['user_id'] = $user->id;
            $bankWithdraw = BankWithdraw::create($data);

            if ($bankWithdraw) {
                $data['withdrawable_id'] = $bankWithdraw->id;
                $data['withdrawable_type'] = BankWithdraw::class;
                $withdraw = Withdraw::create($data);

                $transaction = Transaction::create([
                    'period' => now(),
                    'pay' => $data['amount'],
                    'transactionable_type' => Withdraw::class,
                    'transactionable_id' => $withdraw->id,
                    'account_id' => $account->id
                ]);

                if ($transaction->pay > 0) {
                    $transaction->account()->update(
                        [
                            'balance' => $transaction->account->balance - $transaction->pay
                        ]
                    );
                }
            }

            DB::commit();

            return new BankWithdrawResource($bankWithdraw->load('withdraw'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
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

    public function updateStatusWithdraw(BankWithdraw $bank, Request $request) {
        DB::beginTransaction();
        try {
            $data = $request->validate([
                'status' => 'required|string|in:APPROVED,REJECTED',
            ]);

            $account = Account::where('user_id', $bank->user_id)->first();

            if ($bank->withdraw->status === 'PROCESSING') {
                if ($data['status'] === 'REJECTED') {
                    $account->update([
                        'balance' => $bank->withdraw->amount + $account->balance,
                    ]);
                }
            } else {
                throw new HttpResponseException(
                    response()->json(
                        ['message' => 'The withdrawal has already been processed.'],
                        400
                    )
                );
            }

            $bank->withdraw()->update([
                'status' => $data['status']
            ]);

            DB::commit();

            return new BankWithdrawResource($bank->load('withdraw'));
        } catch (\Throwable $th) {
            DB::rollBack();

            if($th instanceof HttpResponseException) {
                throw $th;
            }

            return response()->json(['message' => $th->getMessage()], 500);
        }

    }
    
}
