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
 * App\Other
 *
 * @property int $id
 * @property string|null $description
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Other newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Other newQuery()
 * @method static Builder|Other onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Other query()
 * @method static \Illuminate\Database\Eloquent\Builder|Other whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Other whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Other whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Other whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Other whereUpdatedAt($value)
 * @method static Builder|Other withTrashed()
 * @method static Builder|Other withoutTrashed()
 * @mixin Eloquent
 * @property int|null $supplier_id
 * @property string $name
 * @property int $quantity
 * @property float $unit_price
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Bundle[] $bundles
 * @property-read int|null $bundles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Price[] $prices
 * @property-read int|null $prices_count
 * @property-read \App\Supplier|null $supplier
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Other whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Other whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Other whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Other whereUnitPrice($value)
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
