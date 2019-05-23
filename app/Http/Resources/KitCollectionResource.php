<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Collection articles
 * @property mixed item_id
 * @property mixed item
 */
class KitCollectionResource extends JsonResource
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
            'name' => $this->item->name,
            'quantity' => $this->item->quantity,
        ];
        $price = $this->item->price();
        $ret['price'] = [
            'id' => $price->id,
            'value' => $price->value,
        ];
        $this->articles ? $ret['nbArticles'] = $this->articles->count() : $ret['nbArticles'] = 0;

        return $ret;
    }
}
