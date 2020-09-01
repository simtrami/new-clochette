<?php

namespace App\Http\Resources;

use App\Barrel;
use App\Bottle;
use App\Food;
use App\Other;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed quantity
 * @property Barrel|Bottle|Food|Other article
 * @property mixed article_type
 * @property mixed article_id
 */
class BundleArticleResource extends JsonResource
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
            'id' => $this->article_id,
            'type' => $this->article_type,
            'name' => $this->article->name,
            'quantity' => $this->quantity,
        ];
    }
}
