<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMinFareToFareCalculationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fare_calculation_settings', function (Blueprint $table) {
            $table->decimal('min_fare')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fare_calculation_settings', function (Blueprint $table) {
            $table->dropColumn('min_fare');
        });
    }
}
