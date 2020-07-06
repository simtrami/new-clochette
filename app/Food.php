<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Food
 *
 * @property int $id
 * @property int $is_bulk
 * @property int|null $units_left
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Article $article
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|Food newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Food newQuery()
 * @method static Builder|Food onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Food query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereIsBulk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereUnitsLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereUpdatedAt($value)
 * @method static Builder|Food withTrashed()
 * @method static Builder|Food withoutTrashed()
 * @mixin Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereId($value)
 */
class Food extends Model
{
    use SoftDeletes;

    protected $table = 'food';

    protected $fillable = ['is_bulk', 'units_left'];

    ##
    # Relationships
    ##

    /**
     * @return BelongsTo
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'id');
    }

    ##
    # Functions
    ##

    /**
     * @param $value
     */
    public function changePrice($value): void
    {
        $this->article->item->changePrice($value);
    }

    ##
    # Extended Properties
    # Must be called with parenthesis
    ##

    /**
     * @return Collection|null
     */
    public function pricesHistory(): ?Collection
    {
        return $this->article->item->pricesHistory();
    }

    /**
     * @return Price|null
     */
    public function price(): ?Price
    {
        return $this->article->item->price();
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->article->item->name;
    }

    /**
     * @return float
     */
    public function quantity(): float
    {
        return $this->article->item->quantity;
    }
}
