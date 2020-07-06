<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Customer
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $nickname
 * @property float $balance
 * @property int $is_staff
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Customer newModelQuery()
 * @method static Builder|Customer newQuery()
 * @method static Builder|Customer query()
 * @method static Builder|Customer whereBalance($value)
 * @method static Builder|Customer whereCreatedAt($value)
 * @method static Builder|Customer whereFirstName($value)
 * @method static Builder|Customer whereId($value)
 * @method static Builder|Customer whereIsStaff($value)
 * @method static Builder|Customer whereLastName($value)
 * @method static Builder|Customer whereNickname($value)
 * @method static Builder|Customer whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @property-read User|null $user
 */
class Customer extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'nickname', 'balance', 'is_staff',
    ];

    ##
    # Relationships
    ##

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
