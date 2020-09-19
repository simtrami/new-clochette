<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * App\Models\Bottle
 *
 * @property int $id
 * @property int|null $supplier_id
 * @property string $name
 * @property int $quantity
 * @property string $unit_price
 * @property string $volume
 * @property bool $is_returnable
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
 * @method static Builder|Bottle newModelQuery()
 * @method static Builder|Bottle newQuery()
 * @method static Builder|Bottle query()
 * @method static Builder|Bottle whereAbv($value)
 * @method static Builder|Bottle whereCreatedAt($value)
 * @method static Builder|Bottle whereDeletedAt($value)
 * @method static Builder|Bottle whereIbu($value)
 * @method static Builder|Bottle whereId($value)
 * @method static Builder|Bottle whereIsReturnable($value)
 * @method static Builder|Bottle whereName($value)
 * @method static Builder|Bottle whereQuantity($value)
 * @method static Builder|Bottle whereSupplierId($value)
 * @method static Builder|Bottle whereUnitPrice($value)
 * @method static Builder|Bottle whereUpdatedAt($value)
 * @method static Builder|Bottle whereVolume($value)
 * @mixin Eloquent
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
