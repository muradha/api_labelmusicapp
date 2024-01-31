<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            'email' => $this->whenNotNull($this->withdrawable->email),
            'bank_type' => $this->whenNotNull($this->withdrawable->bank_type),
            'ach_code' => $this->whenNotNull($this->withdrawable->ach_code),
            'swift_code' => $this->whenNotNull($this->withdrawable->swift_code),
            'ifsc_code' => $this->whenNotNull($this->withdrawable->ifsc_code),
            'currency' => $this->whenNotNull($this->withdrawable->currency),
            'address' => $this->address,
            'province' => $this->province,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'created_at' => $this->created_at,
        ];
    }
}
