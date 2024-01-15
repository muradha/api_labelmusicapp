<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreUserRequest;
use App\Http\Requests\API\UpdateUserRequest;
use App\Http\Resources\AdminCollection;
use App\Http\Resources\AdminResource;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = User::whereHas('roles', fn ($query) => $query->where('name', 'admin'))->get();

        return new AdminCollection($admins);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $admin = User::create($data);
        $admin->assignRole('admin');

        return (new AdminResource($admin))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $admin)
    {
        return new AdminResource($admin);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $admin)
    {
        $data = $request->validated();

        $admin->update($data);

        return new AdminResource($admin);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $admin)
    {
        $admin->delete();

        return new AdminResource($admin);
    }
}
