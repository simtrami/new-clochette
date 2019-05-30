<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Transaction
 *
 * @property int $id
 * @property float $value
 * @property int|null $user_id
 * @property int|null $payment_method_id
 * @property int|null $customer_id
 * @property string|null $comments
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Customer|null $customer
 * @property-read Collection|TransactionDetail[] $details
 * @property-read Collection|Item[] $items
 * @property-read PaymentMethod|null $paymentMethod
 * @property-read User|null $user
 * @method static Builder|Transaction newModelQuery()
 * @method static Builder|Transaction newQuery()
 * @method static Builder|Transaction query()
 * @method static Builder|Transaction whereComments($value)
 * @method static Builder|Transaction whereCreatedAt($value)
 * @method static Builder|Transaction whereCustomerId($value)
 * @method static Builder|Transaction whereId($value)
 * @method static Builder|Transaction wherePaymentMethodId($value)
 * @method static Builder|Transaction whereUpdatedAt($value)
 * @method static Builder|Transaction whereUserId($value)
 * @method static Builder|Transaction whereValue($value)
 * @mixin Eloquent
 */
class Transaction extends Model
{
    protected $fillable = ['value', 'comments'];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['user', 'paymentMethod', 'details'];

    ##
    # Relationships
    ##

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'transaction_details',
            'transaction_id', 'item_id')
            ->using(TransactionDetail::class)
            ->withPivot('quantity');
    }
}

