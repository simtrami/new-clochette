<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Bundle
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
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle newQuery()
 * @method static Builder|Bundle onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle query()
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bundle whereUpdatedAt($value)
 * @method static Builder|Bundle withTrashed()
 * @method static Builder|Bundle withoutTrashed()
 * @mixin Eloquent
 */
class Bundle extends Item
{
    use SoftDeletes;

    protected $fillable = ['name', 'quantity'];

    ##
    # Relationships
    ##

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'bundles_articles',
            'bundle_id', 'article_id')
            ->using(BundleArticle::class)->withPivot('article_quantity')->withTimestamps();
    }
}
