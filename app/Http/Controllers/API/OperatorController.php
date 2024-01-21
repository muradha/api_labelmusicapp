<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\OperatorCollection;
use App\Http\Resources\OperatorResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class OperatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $operators = User::whereHas('roles', fn ($query) => $query->where('name', 'operator'))->get();

        return new OperatorCollection($operators);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:254|string|min:5',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', Password::defaults()],
        ]);

        $operator = User::create($data);
        $operator->assignRole('operator');

        return new OperatorResource($operator);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $operator)
    {
        return new OperatorResource($operator);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $operator)
    {
        $data = $request->validate([
            'name' => 'required|max:254|string|min:5',
            'email' => [
                'required', 'email',
                Rule::unique('users', 'email')->ignore($operator->id)
            ],
            'password' => ['nullable', Password::defaults()],
        ]);

        $operatorExist = $operator->wherehas('roles', fn ($query) => $query->where('name', 'operator'))->count();
        
        if($operatorExist === 0) {
            throw new HttpResponseException(response()->json(['message' => 'Operator not found'], 404));
        }

        if(empty($data['password'])) unset($data['password']);

        $operator->update($data);

        return new OperatorResource($operator);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $operator)
    {
        $operatorExist = $operator->wherehas('roles', fn ($query) => $query->where('name', 'operator'))->count();
        
        if($operatorExist === 0) {
            throw new HttpResponseException(response()->json(['message' => 'Operator not found'], 404));
        }

        $operator->delete();
        
        return new OperatorResource($operator);
    }
}
