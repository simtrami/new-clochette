<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static paginate(int $int)
 */
class Contact extends Model
{
    protected $fillable = [
        'supplier_id', 'first_name', 'last_name', 'phone', 'email', 'role', 'notes'
    ];

    /**
     * Relations
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
