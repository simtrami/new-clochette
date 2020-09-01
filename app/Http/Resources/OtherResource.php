<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed description
 * @property mixed unit_price
 * @property mixed quantity
 * @property mixed name
 * @property mixed id
 * @method activePrice()
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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'unitPrice' => $this->unit_price,
            'description' => $this->description,
            'price' => new PriceResource($this->activePrice()?: null),
            'priceHistory' => PriceHistoryResource::collection($this->whenLoaded('prices')),
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'bundles' => BundleResource::collection($this->whenLoaded('bundles')),
        ];
    }
}
