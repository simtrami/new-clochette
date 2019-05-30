<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\PaymentMethod
 *
 * @property int $id
 * @property string $name
 * @property int $needs_cash_drawer
 * @property string $icon_name
 * @property string $parameters
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Transaction[] $transactions
 * @method static Builder|PaymentMethod newModelQuery()
 * @method static Builder|PaymentMethod newQuery()
 * @method static Builder|PaymentMethod query()
 * @method static Builder|PaymentMethod whereCreatedAt($value)
 * @method static Builder|PaymentMethod whereIconName($value)
 * @method static Builder|PaymentMethod whereId($value)
 * @method static Builder|PaymentMethod whereName($value)
 * @method static Builder|PaymentMethod whereNeedsCashDrawer($value)
 * @method static Builder|PaymentMethod whereParameters($value)
 * @method static Builder|PaymentMethod whereUpdatedAt($value)
 * @mixin Eloquent
 */
class PaymentMethod extends Model
{
    protected $fillable = ['name', 'needs_cash_drawer', 'icon_name', 'parameters'];

    ##
    # Relationships
    ##

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
