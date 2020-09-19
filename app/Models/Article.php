<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * App\Models\Article
 *
 * @property-read Collection|Bundle[] $bundles
 * @property-read int|null $bundles_count
 * @property-read Collection|Price[] $prices
 * @property-read int|null $prices_count
 * @property-read Supplier $supplier
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static Builder|Article newModelQuery()
 * @method static Builder|Article newQuery()
 * @method static Builder|Article query()
 * @mixin Eloquent
 */
class Article extends Item
{
    ##
    # Relationships
    ##

    /**
     * @return BelongsTo
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * @return MorphToMany
     */
    public function bundles(): MorphToMany
    {
        return $this->MorphToMany(Bundle::class, 'article', 'bundles_articles')
            ->using(BundleArticle::class)
            ->withPivot('quantity');
    }

    ##
    # Functions
    ##

    ##
    # Extended Properties
    # Must be called with parenthesis
    # camelCase: made up property
    # snake_case: parent's attribute
    ##
}
