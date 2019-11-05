<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoolRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pool_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_id');
            $table->string('ride_id');
            $table->string('pickup_time');
            $table->string('pickup_date');
            $table->string('pickup_address');
            $table->string('pickup');
            $table->string('drop');
            $table->string('drop_address');
            $table->string('drop_time')->nullable();
            $table->string('status')->nullable();
            $table->string('rating')->nullable();
            $table->string('reviews')->nullable();
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
        Schema::dropIfExists('pool_requests');
    }
}
