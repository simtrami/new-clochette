<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Article
 *
 * @property int $id
 * @property int|null $supplier_id
 * @property float $unit_price
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Barrel $barrel
 * @property-read Bottle $bottle
 * @property-read Food $food
 * @property-read Item $item
 * @property-read Collection|Kit[] $kits
 * @property-read Other $other
 * @property-read Supplier|null $supplier
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Article newQuery()
 * @method static Builder|Article onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Article query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereUpdatedAt($value)
 * @method static Builder|Article withTrashed()
 * @method static Builder|Article withoutTrashed()
 * @mixin Eloquent
 * @property-read int|null $kits_count
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereId($value)
 */
class Article extends Model
{
    use SoftDeletes;

    protected $fillable = ['id', 'unit_price'];

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
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'id');
    }

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
    public function kits(): BelongsToMany
    {
        return $this
            ->belongsToMany(Kit::class, 'kits_articles', 'article_id', 'kit_id')
            ->using(KitArticle::class)->withPivot('article_quantity')->withTimestamps();
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
