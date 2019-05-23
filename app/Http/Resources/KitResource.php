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
class KitResource extends JsonResource
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
            'pricesHistory' => $this->item->pricesHistory(),
        ];
        $price = $this->item->price();
        $ret['price'] = [
            'id' => $price->id,
            'value' => $price->value,
        ];
        $ret['articles'] = [];
        foreach ($this->articles as $article) {
            $type = $article->type();
            $sub = [
                'id' => $article->item_id,
                'name' => $article->item->name,
                'articleQuantity' => $article->pivot->article_quantity,
                'price' => $article->item->price()->value,
                'type' => $type,
            ];
            switch ($type) {
                case 'barrel':
                    $sub['volume'] = $article->barrel->volume;
                    break;
                case 'bottle':
                    $sub['volume'] = $article->bottle->volume;
                    break;
                default:
                    break;
            }
            array_push($ret['articles'], $sub);
        }

        return $ret;
    }
}
