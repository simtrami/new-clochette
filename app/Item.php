<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * App\Item
 *
 * @property-read Collection|Price[] $prices
 * @property-read int|null $prices_count
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static Builder|Item newModelQuery()
 * @method static Builder|Item newQuery()
 * @method static Builder|Item query()
 * @mixin Eloquent
 */
class Item extends Model
{
    ##
    # Relationships
    ##

    /**
     * @return MorphMany
     */
    public function prices(): MorphMany
    {
        return $this->morphMany(Price::class, 'item');
    }

    /**
     * @return MorphToMany
     */
    public function transactions(): MorphToMany
    {
        return $this->morphToMany(Transaction::class, 'item', 'transaction_details')
            ->using(TransactionDetail::class)
            ->withPivot('quantity');
    }

    ##
    # Extended Properties
    # Must be called with parenthesis
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
        return $this->prices()->where('is_active', '=', true)->latest()->first();
    }

    /**
     * @return Collection|null
     */
    public function inactivePrices(): ?Collection
    {
        return $this->prices->where('is_active', '=', false);
    }

    /**
     * @return Collection|null
     */
    public function priceHistory(): ?Collection
    {
        return $this->prices->sortByDesc('updated_at');
    }

    ##
    # Functions
    ##

    /**
     * @param Price $price
     */
    private function switchActivePrice(Price $price): void
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
     * @param $first_value
     * @param $second_value = null
     */
    public function changePrices($first_value, $second_value = null): void
    {
        // TODO: optimize?
        if ($this->prices()->where(['value' => $first_value, 'second_value' => $second_value])->doesntExist()) {
            $activePrice = $this->activePrice();

            // First value is the same, second value is null and has to be set
            // Active price is updated in this case only
            if ($activePrice) {
                if ($activePrice->value === $first_value && !$activePrice->second_value && $second_value) {
                    $activePrice->update(['second_value' => $second_value]);
                    return;
                }
            }

            // In other cases, a new Price is created and the current active price is deactivated afterward
//            $newPrice = new Price();
            $this->prices()->create([
                'value' => $first_value,
                'second_value' => $second_value,
            ]);
            !$activePrice ?: $activePrice->deactivate();
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
     * @param $value
     */
    public function changePrice($value): void
    {
        $this->changePrices($value);
    }
}
