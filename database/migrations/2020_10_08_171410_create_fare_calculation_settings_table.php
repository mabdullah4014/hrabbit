<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFareCalculationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fare_calculation_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pick_mileage')->nullable();
            $table->string('pick_time')->nullable();
            $table->string('drive_mileage')->nullable();
            $table->string('wait_time')->nullable();
            $table->string('drive_time')->nullable();
            $table->string('mileage_limit')->nullable();
            $table->string('drive_mileage_al')->nullable();
            $table->string('wait_time_al')->nullable();
            $table->string('drive_time_al')->nullable();
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
        Schema::dropIfExists('fare_calculation_settings');
    }
}
