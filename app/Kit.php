<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Kit
 *
 * @property int $id
 * @property string $name
 * @property int $quantity
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Article[] $articles
 * @property-read int|null $articles_count
 * @property-read Collection|Price[] $prices
 * @property-read int|null $prices_count
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Kit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Kit newQuery()
 * @method static Builder|Kit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Kit query()
 * @method static \Illuminate\Database\Eloquent\Builder|Kit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kit whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kit whereUpdatedAt($value)
 * @method static Builder|Kit withTrashed()
 * @method static Builder|Kit withoutTrashed()
 * @mixin Eloquent
 */
class Kit extends Item
{
    use SoftDeletes;

    protected $fillable = ['name', 'quantity'];

    ##
    # Relationships
    ##

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'kits_articles',
            'kit_id', 'article_id')
            ->using(KitArticle::class)->withPivot('article_quantity')->withTimestamps();
    }
}
