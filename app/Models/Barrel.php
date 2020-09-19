<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * App\Models\Barrel
 *
 * @property int $id
 * @property int|null $supplier_id
 * @property string $name
 * @property int $quantity
 * @property string $unit_price
 * @property string $volume
 * @property string|null $coupler
 * @property string|null $abv
 * @property string|null $ibu
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Bundle[] $bundles
 * @property-read int|null $bundles_count
 * @property-read Collection|Price[] $prices
 * @property-read int|null $prices_count
 * @property-read Supplier|null $supplier
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static Builder|Barrel newModelQuery()
 * @method static Builder|Barrel newQuery()
 * @method static Builder|Barrel query()
 * @method static Builder|Barrel whereAbv($value)
 * @method static Builder|Barrel whereCoupler($value)
 * @method static Builder|Barrel whereCreatedAt($value)
 * @method static Builder|Barrel whereDeletedAt($value)
 * @method static Builder|Barrel whereIbu($value)
 * @method static Builder|Barrel whereId($value)
 * @method static Builder|Barrel whereName($value)
 * @method static Builder|Barrel whereQuantity($value)
 * @method static Builder|Barrel whereSupplierId($value)
 * @method static Builder|Barrel whereUnitPrice($value)
 * @method static Builder|Barrel whereUpdatedAt($value)
 * @method static Builder|Barrel whereVolume($value)
 * @mixin Eloquent
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

    ##
    # Extended Properties
    # Must be called with parenthesis
    ##
}
