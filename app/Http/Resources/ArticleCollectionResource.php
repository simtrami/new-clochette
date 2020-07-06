<?php

namespace App\Http\Resources;

use App\Barrel;
use App\Bottle;
use App\Food;
use App\Item;
use App\Other;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Item item
 * @property int id
 * @property float unit_price
 * @property Barrel barrel
 * @property Bottle bottle
 * @property Food food
 * @property Other other
 * @method type()
 */
class ArticleCollectionResource extends JsonResource
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
            'name' => $this->item->name,
            'quantity' => $this->item->quantity,
            'unitPrice' => $this->unit_price,
        ];
        $price = $this->item->price();
        $ret['price'] = [
            'id' => $price->id,
            'value' => $price->value,
        ];
        $ret['supplier'] = SupplierResource::collection(
            $this->whenLoaded('supplier'));

        switch ($this->type()) {
            case 'barrel':
                $ret['volume'] = $this->barrel->volume;
                $ret['coupler'] = $this->barrel->coupler;
                $ret['abv'] = $this->barrel->abv;
                $ret['ibu'] = $this->barrel->ibu;
                $ret['variety'] = $this->barrel->variety;
                $ret['price']['secondValue'] = $price->second_value;
                break;
            case 'bottle':
                $ret['volume'] = $this->bottle->volume;
                $ret['isReturnable'] = $this->bottle->is_returnable;
                $ret['abv'] = $this->bottle->abv;
                $ret['ibu'] = $this->bottle->ibu;
                $ret['variety'] = $this->bottle->variety;
                break;
            case 'food':
                $ret['isBulk'] = $this->food->is_bulk;
                $this->food->is_bulk === 0 ?: $ret['unitsLeft'] = $this->food->units_left;
                break;
            case 'other':
                $ret['description'] = $this->other->description;
                break;
            default:
                break;
        }

        return $ret;
    }
}
