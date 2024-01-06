<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreBankRequest;
use App\Http\Requests\API\UpdateBankRequest;
use App\Http\Resources\BankCollection;
use App\Http\Resources\BankResource;
use App\Models\Bank;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banks = Bank::all();

        return (new BankCollection($banks))->response()->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBankRequest $request)
    {
        $data = $request->validated();

        $bank = Bank::create($data);

        return (new BankResource($bank))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Bank $bank)
    {
        if (empty($bank)) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Bank not found'
                ],
            ], 404));
        }

        return (new BankResource($bnak))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBankRequest $request, Bank $bank)
    {
        if (empty($bank)) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Bank not found'
                ],
            ], 404));
        }

        $data = $request->validated();

        $isSuccess = $bank->update($data);

        if (!$isSuccess) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Oops... something wrong !'
                ],
            ], 500));
        }

        return new BankResource($bank);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
        if (empty($bank)) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Bank not found'
                ],
            ], 404));
        }

        $bank->delete();

        return new BankResource($bank);
    }
}
