<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\PaymentMethod
 *
 * @property int $id
 * @property string $name
 * @property bool $debit_customer
 * @property string|null $icon_name
 * @property mixed|null $parameters
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static Builder|PaymentMethod newModelQuery()
 * @method static Builder|PaymentMethod newQuery()
 * @method static Builder|PaymentMethod query()
 * @method static Builder|PaymentMethod whereCreatedAt($value)
 * @method static Builder|PaymentMethod whereDebitCustomer($value)
 * @method static Builder|PaymentMethod whereIconName($value)
 * @method static Builder|PaymentMethod whereId($value)
 * @method static Builder|PaymentMethod whereName($value)
 * @method static Builder|PaymentMethod whereParameters($value)
 * @method static Builder|PaymentMethod whereUpdatedAt($value)
 * @mixin Eloquent
 */
class PaymentMethod extends Model
{
    protected $fillable = ['name', 'debit_customer', 'icon_name', 'parameters'];

    protected $casts = [
        'debit_customer' => 'boolean',
    ];

    ##
    # Relationships
    ##

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
