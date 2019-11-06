<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_id');
            $table->string('mode');
            $table->string('name');
            $table->string('mobile');
            $table->string('id_card_type')->nullable();
            $table->string('id_card_number')->nullable();
            $table->string('id_card_image')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('vehicle_rc_image')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('driving_license_number')->nullable();
            $table->string('driving_license_image')->nullable();
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
        Schema::dropIfExists('user_details');
    }
}
