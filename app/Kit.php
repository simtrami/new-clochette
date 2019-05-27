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
 * App\Kit
 *
 * @property int $item_id
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Article[] $articles
 * @property-read Item $item
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|Kit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Kit newQuery()
 * @method static Builder|Kit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Kit query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|Kit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kit whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kit whereUpdatedAt($value)
 * @method static Builder|Kit withTrashed()
 * @method static Builder|Kit withoutTrashed()
 * @mixin Eloquent
 */
class Kit extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'item_id';

    protected $fillable = [];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['item'];

    ##
    # Relationships
    ##

    /**
     * @return BelongsTo
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function articles()
    {
        return $this
            ->belongsToMany(Article::class, 'kits_articles', 'kit_id',
                'article_id')
            ->using(KitArticle::class)->withPivot('article_quantity')->withTimestamps();
    }
}
