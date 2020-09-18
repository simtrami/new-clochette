<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
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
 * @property-read Collection|BundleArticle[] $articles
 * @property-read int|null $articles_count
 * @property-read Collection|Barrel[] $barrels
 * @property-read int|null $barrels_count
 * @property-read Collection|Bottle[] $bottles
 * @property-read int|null $bottles_count
 * @property-read Collection|Food[] $food
 * @property-read int|null $food_count
 * @property-read Collection|Other[] $others
 * @property-read int|null $others_count
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
    protected $fillable = ['name', 'quantity'];

    ##
    # Relationships
    ##

    public function articles(): HasMany
    {
        return $this->hasMany(BundleArticle::class);
    }

    public function barrels(): MorphToMany
    {
        return $this->morphedByMany(Barrel::class, 'article', 'bundles_articles')
            ->using(BundleArticle::class)
            ->withPivot('quantity');
    }

    public function bottles(): MorphToMany
    {
        return $this->morphedByMany(Bottle::class, 'article', 'bundles_articles')
            ->using(BundleArticle::class)
            ->withPivot('quantity');
    }

    public function food(): MorphToMany
    {
        return $this->morphedByMany(Food::class, 'article', 'bundles_articles')
            ->using(BundleArticle::class)
            ->withPivot('quantity');
    }

    public function others(): MorphToMany
    {
        return $this->morphedByMany(Other::class, 'article', 'bundles_articles')
            ->using(BundleArticle::class)
            ->withPivot('quantity');
    }
}
