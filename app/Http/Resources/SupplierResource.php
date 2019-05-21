<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed name
 * @property mixed description
 * @property mixed address
 * @property mixed phone
 * @property mixed email
 * @property mixed id
 * @property mixed supplier_since
 * @property mixed contacts
 */
class SupplierResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'supplierSince' => $this->supplier_since,
            'contacts' => ContactResource::collection($this->whenLoaded('contacts')),
        ];
    }
}
