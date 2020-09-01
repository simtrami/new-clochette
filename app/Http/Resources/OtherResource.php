<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed article
 * @property mixed description
 * @property mixed id
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
            'id' => $this->id,
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
//            'bundles' => BundleResource::collection($this->whenLoaded('bundles')),
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
        $bundles = $this->article->bundles;
        if ($bundles) {
            $ret['bundles'] = [];
            foreach ($bundles as $bundle) {
                $ret['bundles'][] = [
                    'id', $bundle->id,
                    'name' => $bundle->name,
                    'articleQuantity' => $bundle->pivot->article_quantity,
                ];
            }
        }

        return $ret;
    }
}
