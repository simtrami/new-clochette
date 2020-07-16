<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @method itemName()
 * @property mixed quantity
 * @property mixed item_id
 * @property mixed item_type
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
            'id' => $this->item_id,
            'type' => $this->item_type,
            'name' => $this->item->name,
            'quantity' => $this->quantity,
        ];
    }
}
