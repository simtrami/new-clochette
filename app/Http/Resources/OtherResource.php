<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed article
 * @property mixed description
 * @property mixed article_id
 * @method pricesHistory()
 * @method price()
 * @method quantity()
 * @method name()
 */
class OtherResource extends JsonResource
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
            'description' => $this->description,
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
                'supplierSince' => $supplier->supplier_since,
            ];
        }
        $kits = $this->article->kits;
        if ($kits) {
            $ret['kits'] = [];
            foreach ($kits as $kit) {
                array_push($ret['kits'], [
                    'id', $kit->id,
                    'name' => $kit->name,
                    'articleQuantity' => $kit->pivot->article_quantity,
                ]);
            }
        }

        return $ret;
    }
}
