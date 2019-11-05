<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\RideRequest;
use Illuminate\Support\Facades\Input;


class RideRequestController extends Controller
{
    public function create(Request $request){
        RideRequests::insert([
            'user_id'=>$request->user_id,
            'pickup'=>$request->pickup,
            'pickup_address'=>$request->pickup_address,
            'pickup_time'=>$request->pickup_timw,
            'pickup_date'=>$request->pickup_date,
            'persons'=>$request->persons,
            'status'=>'Confirmed',
        ]);

        return response()->json("The Ride Request has been sent successfully");
    }
}
