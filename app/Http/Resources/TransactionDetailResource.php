<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @method itemName()
 * @property mixed quantity
 * @property mixed item_id
 */
class TransactionDetailResource extends JsonResource
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
            'transaction' => $this->whenLoaded('transaction'),
            'itemId' => $this->item_id,
            'itemName' => $this->itemName(),
            'quantity' => $this->quantity,
        ];
    }
}
