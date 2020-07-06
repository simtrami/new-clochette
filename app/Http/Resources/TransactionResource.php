<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed comments
 * @property mixed value
 * @property mixed id
 */
class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'comments' => $this->comments,
            'user' => new UserResource($this->whenLoaded('user')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'paymentMethod' => new PaymentMethodResource(
                $this->whenLoaded('paymentMethod')),
            'items' => TransactionDetailResource::collection(
                $this->whenLoaded('details')),
        ];
    }
}
