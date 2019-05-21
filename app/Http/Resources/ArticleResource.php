<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $ret = [
            'id' => $this->item_id,
            'name' => $this->name(),
            'quantity' => $this->quantity(),
            'unitPrice' => $this->unit_price,
        ];
        $price = $this->price();
        $ret['price'] = [
            'id' => $price->id,
            'value' => $price->value,
            'second_value' => $price->second_value,
        ];
        $ret = array_merge($ret, [
            'pricesHistory' => $this->pricesHistory(),
//            'kits' => KitResource::collection($this->whenLoaded('kits')),
            'supplier' => SupplierResource::collection($this->whenLoaded('supplier')),
        ]);

        switch ($this->type()) {
            case 'barrel':
                $ret['volume'] = $this->barrel->volume;
                $ret['withdrawalType'] = $this->barrel->withdrawal_type;
                break;
            case 'bottle':
                $ret['volume'] = $this->bottle->volume;
                $ret['isReturnable'] = $this->bottle->is_returnable;
                break;
            case 'food':
                $ret['isBulk'] = $this->food->is_bulk;
                $ret['unitsLeft'] = $this->food->units_left;
                break;
            case 'other':
                $ret['description'] = $this->other->description;
                break;
            default:
                break;
        }

        return $ret;
    }
}
