<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Article
 *
 * @property int $id
 * @property string $name
 * @property int $quantity
 * @property int|null $supplier_id
 * @property float $unit_price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Barrel|null $barrel
 * @property-read Bottle|null $bottle
 * @property-read Food|null $food
 * @property-read Collection|Bundle[] $bundles
 * @property-read int|null $bundles_count
 * @property-read Other|null $other
 * @property-read Collection|Price[] $prices
 * @property-read int|null $prices_count
 * @property-read Supplier|null $supplier
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static Builder|Article newModelQuery()
 * @method static Builder|Article newQuery()
 * @method static Builder|Article query()
 * @method static Builder|Article whereCreatedAt($value)
 * @method static Builder|Article whereId($value)
 * @method static Builder|Article whereName($value)
 * @method static Builder|Article whereQuantity($value)
 * @method static Builder|Article whereSupplierId($value)
 * @method static Builder|Article whereUnitPrice($value)
 * @method static Builder|Article whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Article extends Item
{
    protected $fillable = ['id', 'unit_price', 'name', 'quantity'];

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
     * @return BelongsToMany
     */
    public function bundles(): BelongsToMany
    {
        return $this
            ->belongsToMany(Bundle::class, 'bundles_articles', 'article_id', 'bundle_id')
            ->using(BundleArticle::class)->withPivot('article_quantity')->withTimestamps();
    }

    /**
     * @return HasOne
     */
    public function barrel(): HasOne
    {
        return $this->hasOne(Barrel::class, 'id');
    }

    /**
     * @return HasOne
     */
    public function bottle(): HasOne
    {
        return $this->hasOne(Bottle::class, 'id');
    }

    /**
     * @return HasOne
     */
    public function food(): HasOne
    {
        return $this->hasOne(Food::class, 'id');
    }

    /**
     * @return HasOne
     */
    public function other(): HasOne
    {
        return $this->hasOne(Other::class, 'id');
    }

    ##
    # Functions
    ##

    // Write something here...

    ##
    # Extended Properties
    # Must be called with parenthesis
    # camelCase: made up property
    # snake_case: parent's attribute
    ##

    /**
     * @return string|null
     */
    public function type(): ?string
    {
        if ($this->bottle) {
            return 'bottle';
        }

        if ($this->barrel) {
            return 'barrel';
        }

        if ($this->food) {
            return 'food';
        }

        if ($this->other) {
            return 'other';
        }

        return null;
    }
}
