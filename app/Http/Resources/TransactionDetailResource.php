<?php

namespace App\Http\Resources;

use App\Models\Barrel;
use App\Models\Bottle;
use App\Models\Bundle;
use App\Models\Food;
use App\Models\Other;
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
