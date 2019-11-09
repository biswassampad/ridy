<?php

namespace App\Http\Controllers;
use App\Credit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function update(Request $request){
        $time = \Carbon\Carbon::now()->toDateTimeString();
        $user_id = $request->user_id;
        Credit::where('user_id',$user_id)->update([
            'credits'=>$request->credit,
            'updated_at'=>$time,
        ]);
    }
}
