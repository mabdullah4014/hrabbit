<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToDriverTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_trips', function (Blueprint $table) {
            $table->dateTime('driver_wait_end_time')->nullable();
            $table->dateTime('driver_wait_start_time')->nullable();
            $table->dateTime('driver_pick_start_time')->nullable();
            $table->dateTime('driver_pick_end_time')->nullable();
			$table->string('driver_pick_start_lat')->nullable();
			$table->string('driver_pick_start_lon')->nullable();
			$table->string('driver_wait_start_lat')->nullable();
			$table->string('driver_wait_start_lon')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_trips', function (Blueprint $table) {
            $table->dropColumn('driver_wait_end_time');
            $table->dropColumn('driver_wait_start_time');
            $table->dropColumn('driver_pick_start_time');
            $table->dropColumn('driver_pick_end_time');
            $table->dropColumn('driver_pick_start_lat');
            $table->dropColumn('driver_pick_start_lon');
            $table->dropColumn('driver_wait_start_lat');
            $table->dropColumn('driver_wait_start_lon');
        });
    }
}
