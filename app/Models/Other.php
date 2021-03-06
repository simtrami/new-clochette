<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * App\Models\Other
 *
 * @property int $id
 * @property int|null $supplier_id
 * @property string $name
 * @property int $quantity
 * @property string $unit_price
 * @property string|null $description
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
 * @method static Builder|Other newModelQuery()
 * @method static Builder|Other newQuery()
 * @method static Builder|Other query()
 * @method static Builder|Other whereCreatedAt($value)
 * @method static Builder|Other whereDeletedAt($value)
 * @method static Builder|Other whereDescription($value)
 * @method static Builder|Other whereId($value)
 * @method static Builder|Other whereName($value)
 * @method static Builder|Other whereQuantity($value)
 * @method static Builder|Other whereSupplierId($value)
 * @method static Builder|Other whereUnitPrice($value)
 * @method static Builder|Other whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Other extends Article
{
    protected $fillable = ['name', 'unit_price', 'quantity', 'description'];

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
