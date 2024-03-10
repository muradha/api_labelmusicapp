<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Withdraws\StorePaypalWithdrawRequest;
use App\Http\Requests\API\Withdraws\UpdatePaypalWithdrawRequest;
use App\Http\Resources\Withdraws\PaypalWithdrawCollection;
use App\Http\Resources\Withdraws\PaypalWithdrawResource;
use App\Models\Account;
use App\Models\PaypalWithdraw;
use App\Models\Transaction;
use App\Models\Withdraw;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

            return new PaypalWithdrawResource($paypalWithdraw->load('withdraw'));
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 500);
        }
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

    public function updateStatusWithdraw(PaypalWithdraw $paypal, Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validate([
                'status' => 'required|string|in:APPROVED,REJECTED',
            ]);

            $account = Account::where('user_id', $paypal->user_id)->first();

            if ($paypal->withdraw->status === 'PROCESSING') {
                if ($data['status'] === 'REJECTED') {
                    $account->update([
                        'balance' => $paypal->withdraw->amount + $account->balance,
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

            $paypal->withdraw()->update([
                'status' => $data['status'],
            ]);

            DB::commit();

            return new PaypalWithdrawResource($paypal->load('withdraw'));
        } catch (\Throwable $th) {
            DB::rollBack();

            if($th instanceof HttpResponseException) {
                throw $th;
            }

            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
