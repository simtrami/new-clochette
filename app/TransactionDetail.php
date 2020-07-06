<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\TransactionDetail
 *
 * @property int $transaction_id
 * @property int|null $item_id Will not remove the entry when the item is deleted.
 * @property int $quantity
 * @property-read Item|null $item
 * @property-read Transaction $transaction
 * @method static Builder|TransactionDetail newModelQuery()
 * @method static Builder|TransactionDetail newQuery()
 * @method static Builder|TransactionDetail query()
 * @method static Builder|TransactionDetail whereItemId($value)
 * @method static Builder|TransactionDetail whereQuantity($value)
 * @method static Builder|TransactionDetail whereTransactionId($value)
 * @mixin Eloquent
 */
class TransactionDetail extends Pivot
{
    public $timestamps = false;
    protected $table = 'transaction_details';
    protected $fillable = ['quantity'];

    ##
    # Relationships
    ##

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    ##
    # Extended Properties
    # Must be called with parenthesis
    ##

    public function itemName(): string
    {
        return $this->item->name;
    }
}
