<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Item|null item
 * @property Barrel|null barrel
 * @property Bottle|null bottle
 * @property DateTime created_at
 * @property DateTime deleted_at
 * @property Food|null food
 * @property Collection|null kits
 * @property Other|null other
 * @property Supplier|null supplier
 * @property float unit_price
 * @property DateTime updated_at
 * @method price()
 */
class Article extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'item_id';

    protected $fillable = ['unit_price'];

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

    /**
     * @return BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * @return BelongsToMany
     */
    public function kits()
    {
        return $this
            ->belongsToMany(Kit::class, 'kits_articles', 'article_id', 'kit_id')
            ->using(KitArticle::class)->withPivot('quantity')->withTimestamps();
    }

    /**
     * @return HasOne
     */
    public function barrel()
    {
        return $this->hasOne(Barrel::class, 'article_id', 'item_id');
    }

    /**
     * @return HasOne
     */
    public function bottle()
    {
        return $this->hasOne(Bottle::class, 'article_id', 'item_id');
    }

    /**
     * @return HasOne
     */
    public function food()
    {
        return $this->hasOne(Food::class, 'article_id', 'item_id');
    }

    /**
     * @return HasOne
     */
    public function other()
    {
        return $this->hasOne(Other::class, 'article_id', 'item_id');
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
    public function type()
    {
        if ($this->barrel) {
            return 'barrel';
        } elseif ($this->bottle) {
            return 'bottle';
        } elseif ($this->food) {
            return 'food';
        } elseif ($this->other) {
            return 'other';
        } else {
            return null;
        }
    }
}
