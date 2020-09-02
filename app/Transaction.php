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
 * @property string|null $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Article[] $articles
 * @property-read int|null $articles_count
 * @property-read Customer|null $customer
 * @property-read Collection|TransactionDetail[] $details
 * @property-read int|null $details_count
 * @property-read Collection|Bundle[] $bundles
 * @property-read int|null $bundles_count
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Barrel[] $barrels
 * @property-read int|null $barrels_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Bottle[] $bottles
 * @property-read int|null $bottles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Food[] $food
 * @property-read int|null $food_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Other[] $others
 * @property-read int|null $others_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transaction whereComment($value)
 */
class Transaction extends Model
{
    protected $fillable = ['user_id', 'customer_id', 'payment_method_id', 'value', 'comment'];

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

    public function barrels(): MorphToMany
    {
        return $this->morphedByMany(Barrel::class, 'item', 'transaction_details')
            ->using(TransactionDetail::class)
            ->withPivot('quantity', 'value');
    }

    public function bottles(): MorphToMany
    {
        return $this->morphedByMany(Bottle::class, 'item', 'transaction_details')
            ->using(TransactionDetail::class)
            ->withPivot('quantity', 'value');
    }

    public function bundles(): MorphToMany
    {
        return $this->morphedByMany(Bundle::class, 'item', 'transaction_details')
            ->using(TransactionDetail::class)
            ->withPivot('quantity', 'value');
    }

    public function food(): MorphToMany
    {
        return $this->morphedByMany(Food::class, 'item', 'transaction_details')
            ->using(TransactionDetail::class)
            ->withPivot('quantity', 'value');
    }

    public function others(): MorphToMany
    {
        return $this->morphedByMany(Other::class, 'item', 'transaction_details')
            ->using(TransactionDetail::class)
            ->withPivot('quantity', 'value');
    }
}

