<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed article_id
 * @property mixed article
 * @property mixed is_bulk
 * @property mixed units_left
 * @method name()
 * @method quantity()
 * @method price()
 * @method pricesHistory()
 */
class FoodResource extends JsonResource
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
            'id' => $this->article_id,
            'name' => $this->name(),
            'quantity' => $this->quantity(),
            'unitPrice' => $this->article->unit_price,
        ];
        $price = $this->price();
        $ret['price'] = [
            'id' => $price->id,
            'value' => $price->value,
        ];
        $ret = array_merge($ret, [
            'pricesHistory' => $this->pricesHistory(),
//            'kits' => KitResource::collection($this->whenLoaded('kits')),
            'isBulk' => $this->is_bulk,
        ]);
        $this->is_bulk == 0 ?: $ret['unitsLeft'] = $this->units_left;
        $supplier = $this->article->supplier;
        if ($supplier) {
            $ret['supplier'] = [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'description' => $supplier->description,
                'address' => $supplier->address,
                'phone' => $supplier->phone,
                'email' => $supplier->email,
                'supplierSince' => $supplier->supplier_since,
            ];
        }

        return $ret;
    }
}