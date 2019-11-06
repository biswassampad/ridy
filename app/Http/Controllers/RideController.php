<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\RideRequest;
use App\Ride;
use DateTime;
use Illuminate\Http\Request;

class RideController extends Controller
{
    public function StartRide(Request $request){
        $ride_id =$request->ride_id;
        $time = \Carbon\Carbon::now()->toDateTimeString();
        Ride::where('ride_id',$ride_id)->update([
            'status'=>'Started',
            'updated_at'=>$time,
        ]);
        RideRequest::where('id',$ride_id)->update([
            'status'=>'Started',
            'updated_at'=>$time,
        ]);

        return response()->json('Ride has been started');
    }
    public function CompleteRide(Request $request){
        $ride_id = $request->ride_id;
        $time = \Carbon\Carbon::now()->toDateTimeString();
        $startingtime = Ride::where('ride_id',$ride_id)->first('updated_at');
        $filtered_starting_time = $startingtime['updated_at'];
        $startingtimefinal =new DateTime($filtered_starting_time);
        $endingtimefinal = new Datetime($time);

        $interval = date_diff($startingtimefinal,$endingtimefinal);
        $interval = $interval->format('%h:%i:%s');
        Ride::where('ride_id',$ride_id)->update([
                'ride_duration'=>$interval,
                'updated_at'=>$time,
                'status'=>'Completed',
        ]);
        RideRequest::where('id',$ride_id)->update([
            'status'=>'Completed',
            'updated_at'=>$time
        ]);

        return response()->json('Thanks for the ride..');
    }
    public function CancelRide(Request $request){
        $ride_id = $request->ride_id;
        $time = \Carbon\Carbon::now()->toDateTimeString();
        $cancel_reason = $request->cancel_reason;
        $status = RideRequest::where('id',$request_id)->first('status');
        $status_status=$status['status'];
        if($status_status=='Started'){
            $response= 'Sorry !! Ride is already started';
        }else{
            if($cancel_reason){
                Ride::where('ride_id',$ride_id)->update([
                    'status'=>'Cancelled',
                    'cancel_rason'=>$cancel_reason,
                    'updated_at'=>$time
                    ]);

                RideRequest::where('id',$ride_id)->update([
                    'status'=>'Pending',
                    'updated_at'=>$time
                    ]);
                $response = 'Ride has been cancelled successfully';
    
            }else{
                $response = 'Please Provide a cancel Reason';
            }
        }
        return response($response);
    }
    
    public function RateUser(Request $request){

    }
    public function validateOtp(Request $request){
        $ride_id = $request->ride_id;
        $user_otp = $request->otp;
        $otp = Ride::where('ride_id',$ride_id)->get('otp');
        $sub_filtered_otp = $otp[0];
        $filtered_otp = $sub_filtered_otp['otp'];
        $reponse = "";
        if($filtered_otp == $user_otp){
            $response = "Access";
        }else{
            $response="Not Accessed";
        }
        return response()->json($response);
    }
    public function rateRide(Request $request){
        
    }
    public function getRidesByPooler(Request $request){
        $pooler_id = $request->id;
        $list=Ride::where('pooled_user',$pooler_id)->where('status','Accepted')->get();

        return response()->json($list);
    }

    public function getAllRidesByPooler(Request $request){
        $pooler_id = $request->id;
        $list=Ride::where('pooled_user',$pooler_id)->get();

        return response()->json($list);
    }
}
