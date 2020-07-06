<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed parameters
 * @property mixed icon_name
 * @property mixed needs_cash_drawer
 * @property mixed name
 * @property mixed id
 */
class PaymentMethodResource extends JsonResource
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
            'needsCashDrawer' => $this->needs_cash_drawer,
            'iconName' => $this->icon_name,
            'parameters' => $this->parameters,
        ];
    }
}
