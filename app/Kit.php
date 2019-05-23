<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Collection|null articles
 * @property DateTime created_at
 * @property DateTime deleted_at
 * @property Item|null item
 * @property DateTime updated_at
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
