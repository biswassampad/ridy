<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRideRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ride_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_id');
            $table->string('pickup');
            $table->string('pickup_address');
            $table->string('pickup_time');
            $table->string('pickup_date');
            $table->string('drop');
            $table->string('drop_address');
            $table->string('persons');
            $table->string('distance');
            $table->string('fare');
            $table->string('vehicle_type');
            $table->string('payment_mode');
            $table->string('locality');
            $table->string('ride_type');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ride_requests');
    }
}
