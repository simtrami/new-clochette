<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Item
 *
 * @property int $id
 * @property string $name
 * @property int $quantity
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Article $article
 * @property-read Kit $kit
 * @property-read Collection|Price[] $prices
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Item newQuery()
 * @method static Builder|Item onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Item query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereUpdatedAt($value)
 * @method static Builder|Item withTrashed()
 * @method static Builder|Item withoutTrashed()
 * @mixin Eloquent
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $prices_count
 * @property-read int|null $transactions_count
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
    public function article(): HasOne
    {
        return $this->hasOne(Article::class, 'id');
    }

    /**
     * @return HasOne
     */
    public function kit(): HasOne
    {
        return $this->hasOne(Kit::class, 'id');
    }

    /**
     * @return Collection|null
     */
    public function inactivePrices(): ?Collection
    {
        return $this->prices->where('is_active', false);
    }

    public function transactions(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class, 'transaction_details',
            'item_id', 'transaction_id')
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
        return $this->prices()->where('is_active', true)->latest()->first();
    }

    /**
     * @return HasMany
     */
    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    /**
     * @return Collection|null
     */
    public function pricesHistory(): ?Collection
    {
        return $this->prices->sortByDesc('updated_at');
    }

    ##
    # Functions
    ##

    /**
     * @param Price $price
     */
    public function switchActivePrice(Price $price): void
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

    /**
     * @param $value
     */
    public function changeSecondPrice($value): void
    {
        if (!$this->activePrice()) {
            throw new ModelNotFoundException(
                "Item does not have an active price.");
        }
        $this->changePrices($this->activePrice()->value, $value);
    }
}
