<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Credit;
use Illuminate\Support\Facades\Storage;
use App\UserDetails;
use App\Earning;
use Illuminate\Support\Facades\Input;

class UserDetailsController extends Controller
{
    public function create(Request $request){
        $time = \Carbon\Carbon::now()->toDateTimeString();
        $user_id = $request->user_id;
        $mode = $request->mode;
        
        // condition to check the user details mode 
        if($mode == 1){
            UserDetails::insert([
                'user_id'=>$user_id,
                'mobile'=>$request->mobile,
                'name'=>$request->name,
                'mode'=>$request->mode,
                'created_at'=>$time,
                'updated_at'=>$time,
               ]);
            Credit::insert([
                'usser_id'=>$user_id,
                'credits'=>0,
                'created_at'=>$time,
                'updated_at'=>$time
            ]);    
            // building response
            $response = "User Data has been updated for Ride";
       
        }else if($mode == 2){
            // file insertion method for id card
        $id_card = $request->file('id_card_image');
        $new_id_card_name=$id_card->store('id_cards');
       
        // file insertion method for vehicle rc book
        $rc_book = $request->file('vehicle_rc_image');
        $new_vehicle_rc_name = $rc_book->store('rc_books');

        // file insertion method for driving license
        $license = $request->file('license_image');
        $new_license_name = $license->store('licenses');


        // inserting Data to database
        UserDetails::insert([
            'user_id'=>$user_id,
            'name'=>$request->name,
            'mode'=>$request->mode,
            'mobile'=>$request->mobile,
            'id_card_type'=>$request->id_card_type,
            'id_card_number'=>$request->id_card_number,
            'id_card_image'=>$new_id_card_name,
            'vehicle_number'=>$request->vehicle_number,
            'vehicle_rc_image'=>$new_vehicle_rc_name,
            'driving_license_number'=>$request->driving_license_number,
            'driving_license_image'=>$new_license_name,
            'created_at'=>$time,
            'updated_at'=>$time
        ]);
        Earning::insert([
            'usser_id'=>$user_id,
            'balance'=>0,
            'created_at'=>$time,
            'updated_at'=>$time
        ]);    
        
        // building response 
        $response = "User Data has been updated for pool";
        
        }else{
            $response = "Undefined Mode.Details Can not be updated.";
        }
        return response($response);
    }

    public function update(){
        
    }
}
