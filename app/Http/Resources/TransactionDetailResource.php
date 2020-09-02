<?php

namespace App\Http\Resources;

use App\Barrel;
use App\Bottle;
use App\Bundle;
use App\Food;
use App\Other;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int quantity
 * @property int item_id
 * @property string item_type
 * @property Barrel|Bottle|Bundle|Food|Other item
 * @property mixed value
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
            'id' => $this->item_id,
            'type' => $this->item_type,
            'name' => $this->item->name,
            'quantity' => $this->quantity,
            'value' => $this->value,
        ];
    }
}
