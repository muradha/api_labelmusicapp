<?php

namespace App\Http\Resources\Withdraws;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaypalWithdrawResource extends JsonResource
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
            'name' => $this->whenLoaded('withdraw', $this->withdraw->name),
            'amount' => $this->whenLoaded('withdraw', $this->withdraw->amount),
            'email' => $this->email,
            'address' => $this->whenLoaded('withdraw', $this->withdraw->address),
            'province' => $this->whenLoaded('withdraw', $this->withdraw->province),
            'city' => $this->whenLoaded('withdraw', $this->withdraw->city),
            'postal_code' => $this->whenLoaded('withdraw', $this->withdraw->postal_code),
            'created_at' => $this->whenLoaded('withdraw', $this->withdraw->created_at),
        ];
    }
}
