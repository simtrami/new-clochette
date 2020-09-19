<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Bundle
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
 * @method static Builder|Bundle newModelQuery()
 * @method static Builder|Bundle newQuery()
 * @method static Builder|Bundle query()
 * @method static Builder|Bundle whereCreatedAt($value)
 * @method static Builder|Bundle whereDeletedAt($value)
 * @method static Builder|Bundle whereId($value)
 * @method static Builder|Bundle whereName($value)
 * @method static Builder|Bundle whereQuantity($value)
 * @method static Builder|Bundle whereUpdatedAt($value)
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
