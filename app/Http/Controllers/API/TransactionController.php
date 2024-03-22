<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Transactions\StoreTransactionRequest;
use App\Http\Requests\API\Transactions\UpdateTransactionRequest;
use App\Http\Resources\TransactionCollection;
use App\Http\Resources\TransactionResource;
use App\Models\Deposit;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasAnyRole('admin', 'super-admin', 'operator')) {
            $transactions = Transaction::all();
        } else {
            $transactions = Transaction::whereHas('account', fn (Builder $query) => $query->where('user_id', $user->id))->get();
        }

        return new TransactionCollection($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();

        $transaction = Transaction::create($data);
        $user = Auth::user();

        return new TransactionResource($transaction);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $data = $request->validated();

        $transaction->update($data);

        return new TransactionResource($transaction);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return new TransactionResource($transaction);
    }

    public function debit(Request $request, Transaction $transaction, StoreTransactionRequest $transaction_request)
    {
        $data_transaction = $transaction_request->validated();
        $data_deposit = $request->validate([
            'label_name' => 'required|string|max:100',
            'artist_name' => 'required|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $deposit = Deposit::create($data_deposit);

            $data_transaction['transactionable_type'] = Deposit::class;
            $data_transaction['transactionable_id'] = $deposit->id;

            $transaction = Transaction::create($data_transaction);

            if ($transaction->income > 0 && $transaction->pay > 0) {
                $total = $transaction->income - $transaction->pay;

                $transaction->account()->update(
                    [
                        'balance' => $transaction->account->balance + $total
                    ]
                );
            } elseif ($transaction->income > 0) {
                $transaction->account()->update(
                    [
                        'balance' => $transaction->account->balance + $transaction->income
                    ]
                );
            } else {
                $transaction->account()->update(
                    [
                        'balance' => $transaction->account->balance - $transaction->pay
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $transaction->account,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
