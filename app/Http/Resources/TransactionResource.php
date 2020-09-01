<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string comment
 * @property float value
 * @property int id
 */
class TransactionResource extends JsonResource
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
            'value' => $this->value,
            'comment' => $this->comment,
            'user' => new UserResource($this->whenLoaded('user')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'paymentMethod' => new PaymentMethodResource($this->whenLoaded('paymentMethod')),
            'items' => TransactionDetailResource::collection($this->whenLoaded('details')),
//            'articles' => ArticleCollectionResource::collection($this->articles),
//            'bundles' => BundleResource::collection($this->bundles),
        ];
    }
}
