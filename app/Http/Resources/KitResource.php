<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Collection articles
 * @property mixed id
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
            'id' => $this->id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'pricesHistory' => $this->pricesHistory(),
        ];
        $price = $this->price();
        $ret['price'] = [
            'id' => $price->id,
            'value' => $price->value,
        ];
        $ret['articles'] = [];
        foreach ($this->articles as $article) {
            $type = $article->type();
            $sub = [
                'id' => $article->id,
                'name' => $article->name,
                'articleQuantity' => $article->pivot->article_quantity,
                'price' => $article->price()->value,
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
            $ret['articles'][] = $sub;
        }

        return $ret;
    }
}
