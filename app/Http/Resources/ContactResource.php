<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed first_name
 * @property mixed phone
 * @property mixed last_name
 * @property mixed email
 * @property mixed role
 * @property mixed notes
 * @property mixed supplier
 */
class ContactResource extends JsonResource
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
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'role' => $this->role,
            'notes' => $this->notes,
        ];
    }
}
