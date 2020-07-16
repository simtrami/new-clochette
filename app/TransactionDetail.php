<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\TransactionDetail
 *
 * @property int $transaction_id
 * @property int $item_id
 * @property string $item_type
 * @property int $quantity
 * @property-read Model|Eloquent $item
 * @method static Builder|TransactionDetail newModelQuery()
 * @method static Builder|TransactionDetail newQuery()
 * @method static Builder|TransactionDetail query()
 * @method static Builder|TransactionDetail whereItemId($value)
 * @method static Builder|TransactionDetail whereItemType($value)
 * @method static Builder|TransactionDetail whereQuantity($value)
 * @method static Builder|TransactionDetail whereTransactionId($value)
 * @mixin Eloquent
 */
class TransactionDetail extends MorphPivot
{
    public $timestamps = false;
    protected $table = 'transaction_details';
    protected $with = ['item'];

    ##
    # Relationships
    ##

    public function item(): MorphTo
    {
        return $this->morphTo();
    }
}
