<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rides', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ride_id');
            $table->string('requested_user');
            $table->string('pooled_user');
            $table->string('destination');
            $table->string('destination_address');
            $table->string('pickup');
            $table->string('pickup_address');
            $table->string('pickup_date');
            $table->string('pickup_time');
            $table->string('ride_duration')->nullable();
            $table->string('ride_fare');
            $table->string('otp');
            $table->string('status');
            $table->string('cancel_reason')->nullable();
            $table->string('payment_status');
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
        Schema::dropIfExists('rides');
    }
}
