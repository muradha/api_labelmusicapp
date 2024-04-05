<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Account\StoreAccountRequest;
use App\Http\Requests\API\Account\UpdateAccountRequest;
use App\Http\Resources\AccountCollection;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view banks accounts|create banks accounts|edit banks accounts|delete banks accounts'], ['only' => ['index', 'show']]);
        $this->middleware(['permission:view banks accounts'], ['only' => ['index', 'show']]);
        $this->middleware(['permission:create banks accounts'], ['only' => ['store']]);
        $this->middleware(['permission:edit banks accounts'], ['only' => ['update']]);
        $this->middleware(['permission:delete banks accounts'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = Account::with(['user', 'bank'])->get();

        return new AccountCollection($accounts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccountRequest $request)
    {
        $data = $request->validated();

        $account = Account::firstOrCreate(['user_id' => $data['user_id']], $data);

        return (new AccountResource($account))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        return new AccountResource($account);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountRequest $request, Account $account)
    {
        $data = $request->validated();

        $account->update($data);

        return new AccountResource($account);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        $account->delete();

        return new AccountResource($account);
    }

    public function getBalanceByUserLoggedIn()
    {
        $user = Auth::user();

        $account = Account::where('user_id', $user->id)->first();

        return new AccountResource($account);
    }
}
