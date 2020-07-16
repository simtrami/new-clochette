<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Price
 *
 * @property int $id
 * @property int $item_id
 * @property string $item_type
 * @property float $value
 * @property float|null $second_value
 * @property int $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $item
 * @method static Builder|Price newModelQuery()
 * @method static Builder|Price newQuery()
 * @method static Builder|Price query()
 * @method static Builder|Price whereCreatedAt($value)
 * @method static Builder|Price whereId($value)
 * @method static Builder|Price whereIsActive($value)
 * @method static Builder|Price whereItemId($value)
 * @method static Builder|Price whereItemType($value)
 * @method static Builder|Price whereSecondValue($value)
 * @method static Builder|Price whereUpdatedAt($value)
 * @method static Builder|Price whereValue($value)
 * @mixin Eloquent
 */
class Price extends Model
{

    protected $fillable = ['value', 'second_value', 'is_active'];
    ##
    # Relationships
    ##

    /**
     * @return MorphTo
     */
    public function item(): MorphTo
    {
        return $this->morphTo(Item::class);
    }

    ##
    # Functions
    ##

    /**
     * @return void
     */
    public function activate(): void
    {
        $this->isActive() ?: $this->update(['is_active' => true]);
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool)$this->is_active;
    }

    /**
     * @return void
     */
    public function deactivate(): void
    {
        !$this->isActive() ?: $this->update(['is_active' => false]);
    }
}
