<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverPayout extends Model
{ public function driver()
    {
        return $this->belongsTo(Driver::class,'driver_id');
    }  
}
