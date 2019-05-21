<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static paginate(int $int)
 */
class Supplier extends Model
{
    protected $fillable = [
        'name', 'description', 'address', 'phone', 'email', 'supplier_since',
    ];

    /**
     * Relations
     */
    public function contacts()
    {
        return $this->hasMany('App\Contact');
    }
}
