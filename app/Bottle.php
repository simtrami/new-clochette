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
 * App\Bottle
 *
 * @property int $article_id
 * @property float $volume
 * @property int $is_returnable
 * @property float|null $abv
 * @property float|null $ibu
 * @property string|null $variety
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Article $article
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle newQuery()
 * @method static Builder|Bottle onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereAbv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereIbu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereIsReturnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereVariety($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bottle whereVolume($value)
 * @method static Builder|Bottle withTrashed()
 * @method static Builder|Bottle withoutTrashed()
 * @mixin Eloquent
 */
class Bottle extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'article_id';

    protected $fillable = ['volume', 'is_returnable', 'abv', 'ibu', 'variety'];

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
