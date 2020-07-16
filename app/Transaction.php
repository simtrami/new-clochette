<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
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
 * @property-read Collection|Article[] $articles
 * @property-read int|null $articles_count
 * @property-read Customer|null $customer
 * @property-read Collection|TransactionDetail[] $details
 * @property-read int|null $details_count
 * @property-read Collection|Kit[] $kits
 * @property-read int|null $kits_count
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
    protected $fillable = ['user_id', 'customer_id', 'payment_method_id', 'value', 'comments'];

    /**
     * The relationships that should always be loaded.
     * Only loading relations for which the model has the foreign key (belongs_to)
     *
     * @var array
     */
    protected $with = ['user', 'customer', 'paymentMethod'];

    ##
    # Relationships
    ##

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function articles(): MorphToMany
    {
        return $this->morphedByMany(Article::class, 'item', 'transaction_details')
            ->using(TransactionDetail::class)
            ->withPivot('quantity');
    }

    public function kits(): MorphToMany
    {
        return $this->morphedByMany(Kit::class, 'item', 'transaction_details')
            ->using(TransactionDetail::class)
            ->withPivot('quantity');
    }
}

