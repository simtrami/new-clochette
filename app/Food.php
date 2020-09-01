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
 * App\Food
 *
 * @property int $id
 * @property int $is_bulk
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Food newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Food newQuery()
 * @method static Builder|Food onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Food query()
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereIsBulk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereUnitsLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereUpdatedAt($value)
 * @method static Builder|Food withTrashed()
 * @method static Builder|Food withoutTrashed()
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Food whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Food whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Food whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Food whereUnitPrice($value)
 */
class Food extends Article
{
    protected $table = 'food';

    protected $fillable = ['name', 'unit_price', 'quantity', 'is_bulk'];

    protected $casts = [
        'is_bulk' => 'boolean',
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
