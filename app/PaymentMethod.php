<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = ['name', 'needs_cash_drawer', 'icon_name', 'parameters'];
}
