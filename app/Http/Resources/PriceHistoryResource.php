<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed value
 * @property mixed second_value
 * @property mixed is_active
 * @property mixed created_at
 */
class PriceHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'secondValue' => $this->second_value,
            'isActive' => $this->is_active,
            'createdAt' => $this->created_at,
        ];
    }
}
