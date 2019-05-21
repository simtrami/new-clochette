<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Article|null article
 * @property DateTime created_at
 * @property DateTime updated_at
 * @property float volume
 * @property string withdrawal_type
 * @method static paginate(int $int)
 */
class Barrel extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'article_id';

    protected $fillable = ['volume', 'withdrawal_type'];

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
    public function setPrice($value)
    {
        $this->article->item->setPrice($value);
    }

    /**
     * @param $value
     */
    public function setSecondPrice($value)
    {
        $this->article->item->setSecondPrice($value);
    }

    /**
     * @param $first_value
     * @param null $second_value
     */
    public function setPrices($first_value, $second_value = null)
    {
        $this->article->item->setPrices($first_value, $second_value);
    }

    ##
    # Extended Properties
    # Must be called with parenthesis
    # camelCase: made up property
    # snake_case: parent's attribute
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
     * @return Collection|null
     */
    public function kits()
    {
        return $this->article->kits;
    }

    /**
     * @return Supplier|null
     */
    public function supplier()
    {
        return $this->article->supplier;
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
    public function unit_price()
    {
        return $this->article->unit_price;
    }

    /**
     * @return float
     */
    public function quantity()
    {
        return $this->article->item->quantity;
    }
}
