<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Article|null article
 * @property DateTime created_at
 * @property DateTime deleted_at
 * @property integer id
 * @property string name
 * @property Collection prices
 * @property integer quantity
 * @property DateTime updated_at
 */
class Item extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'quantity'];

    ##
    # Relationships
    ##

    /**
     * @return HasOne
     */
    public function article()
    {
        return $this->hasOne(Article::class);
    }

    /**
     * @return HasOne
     */
    public function kit()
    {
        return $this->hasOne(Kit::class);
    }

    /**
     * @return Collection|null
     */
    public function inactivePrices()
    {
        return $this->prices->where('is_active', false);
    }

    ##
    # Extended Properties
    # Must be called with parenthesis
    # camelCase: made up property
    # snake_case: parent's attribute
    ##

    /**
     * @return Price|null
     */
    public function price()
    {
        return $this->activePrice();
    }

    /**
     * @return Price|Model|HasMany|object|null
     */
    public function activePrice()
    {
        return $this->prices()->where('is_active', true)->latest()->first();
    }

    /**
     * @return HasMany
     */
    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    /**
     * @return Collection|null
     */
    public function pricesHistory()
    {
        return $this->prices->sortByDesc('updated_at');
    }

    ##
    # Functions
    ##

    /**
     * @param $value
     */
    public function changePrice($value)
    {
        $this->changePrices($value);
    }

    /**
     * @param $first_value
     * @param $second_value = null
     */
    public function changePrices($first_value, $second_value = null)
    {
        if ($this->prices()->where(['value' => $first_value, 'second_value' => $second_value])->doesntExist()) {
            $activePrice = $this->activePrice();

            // First value is the same, second value is null and has to be set
            // Active price is updated in this case only
            if ($activePrice) {
                !($activePrice->value == $first_value &&
                    !$activePrice->second_value &&
                    $second_value) ?:
                    $activePrice->update(['second_value' => $second_value]);
                return;
            }

            // In other cases, a new Price is created and the current active price is deactivated afterward
            $newPrice = new Price([
                'item_id' => $this->id,
                'value' => $first_value,
                'second_value' => $second_value,
            ]);

            $this->prices()->save($newPrice);

            $activePrice->deactivate();
        } else {
            // The values already exist in a price related to this item.
            // It is activated if necessary.
            $existingPrice = $this->prices()
                ->where([
                    'value' => $first_value,
                    'second_value' => $second_value])
                ->first();
            /** @var Price $existingPrice */
            $this->switchActivePrice($existingPrice);
        }
    }

    /**
     * @param Price $price
     */
    public function switchActivePrice(Price $price)
    {
        // Given price has to be one of the item's prices
        if (!$this->prices->contains('id', $price->id)) {
            throw new ModelNotFoundException(
                "Price not found in item's prices.");
        }

        // Deactivate currently activated price if it is not the given price
        if (!$price->isActive() && $this->activePrice()) {
            $this->activePrice()->deactivate();
        }
        // This function doesn't do anything if the given price is already activated
        $price->activate();
    }

    /**
     * @param $value
     */
    public function changeSecondPrice($value)
    {
        if (!$this->activePrice()) {
            throw new ModelNotFoundException(
                "Item does not have an active price.");
        }
        $this->changePrices($this->activePrice()->value, $value);
    }
}
