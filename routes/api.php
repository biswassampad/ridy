<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
// user details routes
Route::post('/addUserDetails','UserDetailsController@create');

// creating a ride routes

Route::post('/requestRide','RideRequestController@create');

// getting ride requests

Route::post('/rideRequests','RideRequestController@RideRequestList');
Route::post('/confirmRide','RideRequestController@confirmRequest');
Route::post('/cancelRide','RideRequestController@CancelRideRequest');
Route::post('/updateRide','RideRequestController@updateRideRequest');
Route::post('/updateRidePayment','RideRequestController@UpdateRidePayment');
Route::post('/getrquestsByUser','RideRequestController@getRideRequestsByUser');

// ride apis
Route::post('/startRide','RideController@StartRide');
Route::post('/completeRide','RideController@CompleteRide');
Route::post('/cancelRide','RideController@cancelRide');
Route::post('/validateotp','RideController@validateOtp');
Route::post('/rateuser','RideController@RateUser');