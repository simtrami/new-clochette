<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Price
 *
 * @property int $id
 * @property int|null $item_id
 * @property string $item_type
 * @property string $value
 * @property string|null $second_value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $item
 * @method static Builder|Price newModelQuery()
 * @method static Builder|Price newQuery()
 * @method static Builder|Price query()
 * @method static Builder|Price whereCreatedAt($value)
 * @method static Builder|Price whereId($value)
 * @method static Builder|Price whereItemId($value)
 * @method static Builder|Price whereItemType($value)
 * @method static Builder|Price whereSecondValue($value)
 * @method static Builder|Price whereUpdatedAt($value)
 * @method static Builder|Price whereValue($value)
 * @mixin Eloquent
 */
class Price extends Model
{
    use HasFactory;

    protected $fillable = ['value', 'second_value'];

    ##
    # Relationships
    ##

    /**
     * @return MorphTo
     */
    public function item(): MorphTo
    {
        return $this->morphTo();
    }

    ##
    # Functions
    ##

    public function equals(Price $other): bool
    {
        return $this->value === $other->value && $this->second_value === $other->second_value;
    }
}
