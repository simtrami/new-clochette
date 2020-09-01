<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed quantity
 * @property mixed name
 * @property mixed id
 * @method activePrice()
 */
class BundleResource extends JsonResource
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
            'price' => new PriceResource($this->activePrice()?: null),
            'priceHistory' => PriceHistoryResource::collection($this->whenLoaded('prices')),
            'articles' => BundleArticleResource::collection($this->whenLoaded('articles')),
        ];
    }
}
