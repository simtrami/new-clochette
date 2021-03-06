<?php

namespace App\Http\Resources;

use App\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Article article
 * @property float volume
 * @property string coupler
 * @property mixed id
 * @property mixed abv
 * @property mixed ibu
 * @method name()
 * @method quantity()
 * @method priceHistory()
 * @method price()
 * @method activePrice()
 */
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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'unitPrice' => $this->unit_price,
            'volume' => $this->volume,
            'coupler' => $this->coupler,
            'abv' => $this->abv,
            'ibu' => $this->ibu,
            'price' => new PriceResource($this->activePrice()?: null),
            'priceHistory' => PriceHistoryResource::collection($this->whenLoaded('prices')),
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'bundles' => BundleResource::collection($this->whenLoaded('bundles')),
        ];
    }
}
