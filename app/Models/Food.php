<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * App\Models\Food
 *
 * @property int $id
 * @property int|null $supplier_id
 * @property string $name
 * @property int $quantity
 * @property string $unit_price
 * @property bool $is_bulk
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
 * @method static Builder|Food newModelQuery()
 * @method static Builder|Food newQuery()
 * @method static Builder|Food query()
 * @method static Builder|Food whereCreatedAt($value)
 * @method static Builder|Food whereDeletedAt($value)
 * @method static Builder|Food whereId($value)
 * @method static Builder|Food whereIsBulk($value)
 * @method static Builder|Food whereName($value)
 * @method static Builder|Food whereQuantity($value)
 * @method static Builder|Food whereSupplierId($value)
 * @method static Builder|Food whereUnitPrice($value)
 * @method static Builder|Food whereUpdatedAt($value)
 * @mixin Eloquent
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
