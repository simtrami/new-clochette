<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed first_name
 * @property mixed last_name
 * @property mixed nickname
 * @property mixed balance
 * @property mixed is_staff
 */
class CustomerResource extends JsonResource
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
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'nickname' => $this->nickname,
            'balance' => $this->balance,
            'isStaff' => $this->is_staff,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
