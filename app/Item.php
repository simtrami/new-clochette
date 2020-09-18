<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    use SoftDeletes, HasFactory;

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
     * @return Price|Model|HasMany|object|null
     */
    public function activePrice()
    {
        return $this->prices()->latest()->orderByDesc('id')->first();
    }

    /**
     * @return ?Collection
     */
    public function inactivePrices(): Collection
    {
        // Key 0 is the latest chronologically (cf. activePrice())
        return $this->prices()->latest()->orderByDesc('id')->get()->except(0);
    }

    /**
     * @return ?Collection
     */
    public function priceHistory(): Collection
    {
        return $this->prices()->latest()->orderByDesc('id')->get();
    }

    ##
    # Functions
    ##

    /**
     * @param Price $newPrice
     */
    public function setActivePrice(Price $newPrice): void
    {
        if ($this->activePrice() === null) {
            $this->prices()->save($newPrice);
        }
        if (!$newPrice->equals($this->activePrice())) {
            $this->prices()->save($newPrice);
        }
    }
}
