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
 * App\Barrel
 *
 * @property int $id
 * @property float $volume
 * @property string|null $coupler
 * @property float|null $abv
 * @property float|null $ibu
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Article $article
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel newQuery()
 * @method static Builder|Barrel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel query()
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereAbv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereCoupler($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereIbu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereVariety($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereVolume($value)
 * @method static Builder|Barrel withTrashed()
 * @method static Builder|Barrel withoutTrashed()
 * @mixin Eloquent
 */
class Barrel extends Model
{
    use SoftDeletes;

    protected $fillable = ['volume', 'coupler', 'abv', 'ibu'];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['article'];

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

    /**
     * @param $value
     */
    public function changeSecondPrice($value): void
    {
        $this->article->changeSecondPrice($value);
    }

    /**
     * @param $first_value
     * @param null $second_value
     */
    public function changePrices($first_value, $second_value = null): void
    {
        $this->article->changePrices($first_value, $second_value);
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
