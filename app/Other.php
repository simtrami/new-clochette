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
 * App\Other
 *
 * @property int $id
 * @property string|null $description
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Article $article
 * @method static \Illuminate\Database\Eloquent\Builder|Other newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Other newQuery()
 * @method static Builder|Other onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Other query()
 * @method static \Illuminate\Database\Eloquent\Builder|Other whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Other whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Other whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Other whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Other whereUpdatedAt($value)
 * @method static Builder|Other withTrashed()
 * @method static Builder|Other withoutTrashed()
 * @mixin Eloquent
 */
class Other extends Model
{
    use SoftDeletes;

    protected $fillable = ['description'];

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
        $this->article->changePrice($value);
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
        return $this->article->pricesHistory();
    }

    /**
     * @return Price|null
     */
    public function price(): ?Price
    {
        return $this->article->price();
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->article->name;
    }

    /**
     * @return float
     */
    public function quantity(): float
    {
        return $this->article->quantity;
    }
}
