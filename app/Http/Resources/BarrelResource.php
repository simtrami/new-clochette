<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BarrelResource extends JsonResource
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
            'id' => $this->article->item_id,
            'name' => $this->article->name(),
            'quantity' => $this->article->quantity(),
            'unitPrice' => $this->article->unit_price,
        ];
        $price = $this->article->price();
        $ret['price'] = [
            'id' => $price->id,
            'value' => $price->value,
            'secondValue' => $price->second_value,
        ];
        $ret = array_merge($ret, [
            'pricesHistory' => $this->article->pricesHistory(),
//            'kits' => KitResource::collection($this->whenLoaded('kits')),
            'volume' => $this->volume,
            'withdrawalType' => $this->withdrawal_type,
        ]);
        $supplier = $this->article->supplier;
        if ($supplier) {
            $ret['supplier'] = [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'description' => $supplier->description,
                'address' => $supplier->address,
                'phone' => $supplier->phone,
                'email' => $supplier->email,
                'supplierSince' => $supplier->supplier_since
            ];
        }

        return $ret;
    }
}
