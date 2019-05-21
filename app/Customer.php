<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static paginate(int $int)
 */
class Customer extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'nickname', 'balance',
        'is_staff', 'staff_nickname',
    ];
}
