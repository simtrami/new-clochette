<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\KitArticle
 *
 * @method static Builder|KitArticle newModelQuery()
 * @method static Builder|KitArticle newQuery()
 * @method static Builder|KitArticle query()
 * @mixin Eloquent
 */
class KitArticle extends Pivot
{
    protected $fillable = ['article_quantity'];
}
