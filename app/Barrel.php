<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Barrel
 *
 * @property int $id
 * @property float $volume
 * @property string|null $coupler
 * @property float|null $abv
 * @property float|null $ibu
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel newQuery()
 * @method static Builder|Barrel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel query()
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereAbv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereCoupler($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereIbu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereVariety($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereVolume($value)
 * @method static Builder|Barrel withTrashed()
 * @method static Builder|Barrel withoutTrashed()
 * @mixin Eloquent
 * @property int|null $supplier_id
 * @property string $name
 * @property int $quantity
 * @property float $unit_price
 * @property-read Collection|Bundle[] $bundles
 * @property-read int|null $bundles_count
 * @property-read Collection|Price[] $prices
 * @property-read int|null $prices_count
 * @property-read Supplier|null $supplier
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereUnitPrice($value)
 */
class Barrel extends Article
{
    protected $fillable = ['name', 'unit_price', 'quantity', 'volume', 'coupler', 'abv', 'ibu'];

    ##
    # Relationships
    ##



    ##
    # Functions
    ##

    /**
     * @param $value
     */
    public function changeSecondPrice($value): void
    {
        if (!$this->activePrice()) {
            throw new ModelNotFoundException(
                "Barrel does not have an active price.");
        }
        $this->changePrices($this->activePrice()->value, $value);
    }

    ##
    # Extended Properties
    # Must be called with parenthesis
    ##
}
