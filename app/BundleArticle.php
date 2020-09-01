<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\BundleArticle
 *
 * @method static Builder|BundleArticle newModelQuery()
 * @method static Builder|BundleArticle newQuery()
 * @method static Builder|BundleArticle query()
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
