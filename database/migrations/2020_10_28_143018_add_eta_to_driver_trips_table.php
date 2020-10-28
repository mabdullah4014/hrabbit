<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEtaToDriverTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_trips', function (Blueprint $table) {
            $table->text('driver_pickup_eta')->nullable();
            $table->text('driver_pickup_distance')->nullable();
            $table->text('pickup_drop_eta')->nullable();
            $table->text('pickup_drop_distance')->nullable();
            $table->decimal('points', 6, 2)->nullable();
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
            $table->dropColumn('driver_pickup_eta');
            $table->dropColumn('driver_pickup_distance');
            $table->dropColumn('pickup_drop_eta');
            $table->dropColumn('pickup_drop_distance');
            $table->dropColumn('points');
        });
    }
}
