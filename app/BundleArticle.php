<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\BundleArticle
 *
 * @method static Builder|BundleArticle newModelQuery()
 * @method static Builder|BundleArticle newQuery()
 * @method static Builder|BundleArticle query()
 * @mixin Eloquent
 */
class BundleArticle extends Pivot
{
    protected $fillable = ['article_quantity'];
}
