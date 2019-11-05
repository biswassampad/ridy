<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    protected $fillable=[
        'user_id','mode','name','mobile','id_card_type','id_card_number','id_card_image','vehicle_number','vehicle_rc_image','driving_license_number','driving_license_image'
    ];
}
