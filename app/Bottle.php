<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Bottle
 *
 * @property int $id
 * @property float $volume
 * @property int $is_returnable
 * @property float|null $abv
 * @property float|null $ibu
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle newQuery()
 * @method static Builder|Bottle onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle query()
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereAbv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereIbu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereIsReturnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereVariety($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereVolume($value)
 * @method static Builder|Bottle withTrashed()
 * @method static Builder|Bottle withoutTrashed()
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
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereUnitPrice($value)
 */
class Bottle extends Article
{
    protected $fillable = ['name', 'unit_price', 'quantity', 'volume', 'is_returnable', 'abv', 'ibu'];

    protected $casts = [
        'is_returnable' => 'boolean',
    ];

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
