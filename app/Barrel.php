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
 * @property int $article_id
 * @property float $volume
 * @property string|null $withdrawal_type
 * @property float|null $abv
 * @property float|null $ibu
 * @property string|null $variety
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Article $article
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel newQuery()
 * @method static Builder|Barrel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereAbv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereIbu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereVariety($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrel whereWithdrawalType($value)
 * @method static Builder|Barrel withTrashed()
 * @method static Builder|Barrel withoutTrashed()
 * @mixin Eloquent
 */
class Barrel extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'article_id';

    protected $fillable = ['volume', 'withdrawal_type', 'abv', 'ibu', 'variety'];

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
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'item_id');
    }

    ##
    # Functions
    ##

    /**
     * @param $value
     */
    public function changePrice($value)
    {
        $this->article->item->changePrice($value);
    }

    /**
     * @param $value
     */
    public function changeSecondPrice($value)
    {
        $this->article->item->changeSecondPrice($value);
    }

    /**
     * @param $first_value
     * @param null $second_value
     */
    public function changePrices($first_value, $second_value = null)
    {
        $this->article->item->changePrices($first_value, $second_value);
    }

    ##
    # Extended Properties
    # Must be called with parenthesis
    ##

    /**
     * @return Collection|null
     */
    public function pricesHistory()
    {
        return $this->article->item->pricesHistory();
    }

    /**
     * @return Price|null
     */
    public function price()
    {
        return $this->article->item->price();
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->article->item->name;
    }

    /**
     * @return float
     */
    public function quantity()
    {
        return $this->article->item->quantity;
    }
}
