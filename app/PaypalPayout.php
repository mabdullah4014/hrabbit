<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaypalPayout extends Model
{
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
