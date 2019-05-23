<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class KitArticle extends Pivot
{
    protected $fillable = ['article_quantity'];
}
