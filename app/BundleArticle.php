<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\BundleArticle
 *
 * @property int $bundle_id
 * @property int $article_id
 * @property string $article_type
 * @property int $quantity
 * @property-read Model|Eloquent $article
 * @method static Builder|BundleArticle newModelQuery()
 * @method static Builder|BundleArticle newQuery()
 * @method static Builder|BundleArticle query()
 * @method static Builder|BundleArticle whereArticleId($value)
 * @method static Builder|BundleArticle whereArticleType($value)
 * @method static Builder|BundleArticle whereBundleId($value)
 * @method static Builder|BundleArticle whereQuantity($value)
 * @mixin Eloquent
 */
class BundleArticle extends MorphPivot
{
    public $timestamps = false;

    protected $table = 'bundles_articles';

    protected $with = ['article'];

    ##
    # Relationships
    ##

    public function article(): MorphTo
    {
        return $this->morphTo();
    }
}
