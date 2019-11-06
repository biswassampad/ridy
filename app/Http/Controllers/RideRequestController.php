<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\RideRequest;
use App\Ride;
use Illuminate\Support\Facades\Input;


class RideRequestController extends Controller
{
    public function create(Request $request){
    $lat1=20.354716;
    $lon1=-85.823550;
    $lat2=20.3052504;
    $lon2=-85.8537231;
    $vehicle = $request->vehicle_type;
    $pickup_addr_lower = strtolower($request->pickup);
    $drop_addr_lower = strtolower($request->drop);
    $pickup = str_replace(' ','',$pickup_addr_lower);
    $drop=str_replace(' ','',$drop_addr_lower);
    $time = \Carbon\Carbon::now()->toDateTimeString();
    $distance = $this->FinalDistanceCalculater($lat1, $lon1, $lat2, $lon2,'K');
    $fare = $this->FareCalculator($distance,$vehicle);
        RideRequest::insert([
            'user_id'=>$request->user_id,
            'pickup'=>$pickup,
            'pickup_address'=>$request->pickup_address,
            'pickup_time'=>$request->pickup_time,
            'pickup_date'=>$request->pickup_date,
            'persons'=>$request->persons,
            'drop'=>$drop,
            'drop_address'=>$request->drop_address,
            'vehicle_type'=>$vehicle,
            'distance'=>$distance,
            'fare'=>$fare,
            'locality'=>$request->locality,
            'ride_type'=>$request->ride_type,
            'payment_mode'=>$request->payment_mode,
            'status'=>'Pending',
            'created_at'=>$time,
            'updated_at'=>$time
        ]);

        return response()->json("The Ride Request has been sent successfully");
    }

    public function MesaureDistance($pickup,$drop,$unit=''){
        $api_key = 'AIzaSyAgiEt8nnfzzXPlgnIDijOvc77t7RkBPOw';

        // adress formating
        $formattedAddrFrom = str_replace(' ','+',$pickup);
        $formattedAddrTo = str_replace(' ','+',$drop);

        // geo coding api for pickup address
        $geocodefrom = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddrFrom.'&sensor=false&key='.$api_key);
        $outputfrom=json_decode($geocodefrom);
        if(!empty($outputfrom->error_message)){
            return $outputfrom ->error_message;
        } 

        // geocoding api for drop address
        $geocodeto = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddrTo.'&sensor=false&key='.$api_key);
        $outputto=json_decode($geocodeto);
        if(!empty($outputfrom->error_message)){
            return $outputfrom ->error_message;
        } 
        
        //getting latitude and longitude from the geo data 
        $latitude_from = $outputfrom->results[0]->geometry->location->lat ;
        $longitude_from = $outputfrom->results[0]->geometry->location->lng;
        $latitude_to = $outputto->results[0]->geometry->location->lat;
        $longitude_to = $outputto->results[0]->geometry->location->lng;

        // calculating distance using algorithm
        $theta = $longitude_from-$longitude_to;
        $distance = sin(deg2rad($latitude_from))*sin(deg2rad($latitude_to)) +  cos(deg2rad($latitude_from)) * cos(deg2rad($latitude_to)) * cos(deg2rad($theta));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $miles = $distance *60 * 1.1515;

        // converting unit and returning the distance 
        $unit = strtoupper($unit);
        if($unit=="K"){
            return round($miles * 1.609344,2).'km';
        }else if ($unit == "M"){
            return round($miles *1609.344,2).'meters';
        }else{
            return round($miles,2).'miles';
        }
    }
    public function FinalDistanceCalculater($lat1, $lon1, $lat2, $lon2, $unit){
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
          }
          else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);
        
            if ($unit == "K") {
              return round($miles * 1.609344);
            } else if ($unit == "N") {
              return round($miles * 0.8684);
            } else {
              return round($miles);
            }
          }
    }
    public function FareCalculator($distance,$vehicle){
        if($vehicle == '1'){
            $fare = $distance*8.5+30;
        }else if($vehicle == '2'){
            $fare = $distance*4.5+15;
        } 
        return $fare;
    }
    public function RideRequestList(Request $request){
        $pickup_addr_lower = strtolower($request->pickup);
        $pickup = $pickup = str_replace(' ','',$pickup_addr_lower);
        $ride_type = $request->ride_type;
        $requestList =RideRequest::where('status','Pending')->where('ride_type',$ride_type)->where('pickup',$pickup)->get();

        return response()->json($requestList);
    }

    public function confirmRequest(Request $request){
    //    getting the required values from form
        $request_id = $request->request_id;
        $pooler_id = $request->pooler_id;
        // fetching requested user details 
        $ride_details = $this->RideRequestDetails($request_id);
        $user_id ='';
        $response='';
        $time = \Carbon\Carbon::now()->toDateTimeString();
        $otp = rand ( 10000 , 99999 );
        // iterating over user details 
        foreach($ride_details as $item){
            $user_id = $item['user_id'];
            $destination =$item['drop'];
            $destination_address = $item['drop_address'];
            $pickup = $item['pickup'];
            $pickup_address = $item['pickup_address'];
            $pickup_date = $item['pickup_date'];
            $pickup_time = $item['pickup_time'];
            $ride_fare = $item['fare'];
            $payment_status = $item['payment_mode'];

            RideRequest::where('id',$request_id)->update([
            'status'=>'Accepted'
            ]);
            // checking if ride is already accepted
            $ride = Ride::where('ride_id',$request_id)->get();
            // if not accepting the ride by inserting the ride details into ride table 
            if(sizeof($ride)==0){
                Ride::insert([
                'requested_user'=>$user_id,
                'ride_id'=>$request_id,
                'pooled_user'=>$pooler_id,
                'destination'=>$destination,
                'destination_address'=>$destination_address,
                'pickup'=>$pickup,
                'pickup_address'=>$pickup_address,
                'pickup_date'=>$pickup_date,
                'pickup_time'=>$pickup_time,
                'ride_fare'=>$ride_fare,
                'otp'=>$otp,
                'status'=>'Accepted',
                'payment_status'=>$payment_status,
                'created_at'=>$time,
            ]);
            
            $response = 'Ride has been accepted';
            }else{
            $response = 'Oops !! Ride has been accepted by someone else';
            }
            

        }
          return response()->json($response);
       
    }

    public function RideRequestDetails($request_id){
        $details = RideRequest::where('id',$request_id)->get();

        return $details;
    }

    public function CancelRideRequest(Request $request){
        $request_id=$request->request_id;
        $cancel_reason = $request->cancel_reason;
        $status = RideRequest::where('id',$request_id)->get('status');
        $status_status=$status[0];
        $time = \Carbon\Carbon::now()->toDateTimeString();
        $response='';
        if($status_status['status']=='Accepted'){
            $response= 'Sorry !! Ride is already accepted';
        }else{
            if($cancel_reason){
                RideRequest::where('id',$request_id)->update([
                    'status'=>'Cancelled',
                    'cancel_reason'=>$cancel_reason,
                    'updated_at'=>$time
                    ]);
                $response = 'Ride has been cancelled successfully';
    
            }else{
                $response = 'Please Provide a cancel Reason';
            }
        }
        return response($response);
    }
    
    public function UpdateRideRequest(Request $request){
        $request_id = $request->request_id;
        $time = \Carbon\Carbon::now()->toDateTimeString();
        $status = RideRequest::where('id',$request_id)->get('status');
        $status_status=$status[0];
        $lat1=20.354716;
        $lon1=-85.823550;
        $lat2=20.3052504;
        $lon2=-85.8537231;
        $vehicle = $request->vehicle_type;
        $pickup_addr_lower = strtolower($request->pickup);
        $drop_addr_lower = strtolower($request->drop);
        $pickup = str_replace(' ','',$pickup_addr_lower);
        $drop=str_replace(' ','',$drop_addr_lower);
        $distance = $this->FinalDistanceCalculater($lat1, $lon1, $lat2, $lon2,'K');
        $fare = $this->FareCalculator($distance,$vehicle);

        $response='';
        if($status_status['status']=='Accepted'){
            $response = 'Sorry !! Ride is already accepted , Please make a new request';
        }else if($status_status['status']=='Cancelled'){
            $response = 'Sorry !! Ride is already cancelled,Please make a new request';
        }else{
            RideRequest::where('id',$request_id)->update([
                'user_id'=>$request->user_id,
                'pickup'=>$pickup,
                'pickup_address'=>$request->pickup_address,
                'pickup_time'=>$request->pickup_time,
                'pickup_date'=>$request->pickup_date,
                'persons'=>$request->persons,
                'drop'=>$drop,
                'drop_address'=>$request->drop_address,
                'vehicle_type'=>$vehicle,
                'distance'=>$distance,
                'fare'=>$fare,
                'locality'=>$request->locality,
                'ride_type'=>$request->ride_type,
                'payment_mode'=>$request->payment_mode,
                'status'=>'Pending',
                'updated_at'=>$time
                ]);
                $response = 'Ride Details have been updated successfully';
        }

        return response()->json($response);
    }

    public function UpdateRidePayment(Request $request){
        $request_id = $request->request_id;
        $time = \Carbon\Carbon::now()->toDateTimeString();
        $status = RideRequest::where('id',$request_id)->get('status');
        $status_status=$status[0];
        $response='';
        if($status_status['status']=='Accepted'){
            $response = 'Sorry !! Payment mode can not be changed when ride is accepted';
        }else if($status_status['status']=='Cancelled'){
            $response = 'Sorry !! Ride is already cancelled,Please make a new request';
        }else{
            RideRequest::where('id',$request_id)->update([
                'payment_mode'=>$request->payment_mode,
                'updated_at'=>$time
                ]);
                $response = 'Payment mode has been updated successfully';
        }

        return response()->json($response);
       
    }
    // It is to be done when google api is enabled
    // public function FareEstimater(Request $request){
    //     $pickup = $request->pickup;
    //     $drop = $request->drop;
    //     $vehicle = $request->vehicle_type;
    //     $distance = $this->FinalDistanceCalculater()
    //     if($vehicle == '1'){
    //         $fare = $distance*8.5+30;
    //     }else if($vehicle == '2'){
    //         $fare = $distance*4.5+15;
    //     } 
    //     return $fare;
    // }
}
