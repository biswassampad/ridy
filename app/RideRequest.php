<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RideRequest extends Model
{
    protected $fillable=[
        'user_id','pickup','pickup_address','pickup_time','pickup_date','persons','status'
    ];
}
