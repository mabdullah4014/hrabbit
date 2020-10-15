<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDropOffTimeToDriverTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_trips', function (Blueprint $table) {
            $table->dateTime('drop_off_time')->nullable();
            $table->decimal('advance_amount')->nullable();
            $table->decimal('estimated_distance', 6, 2)->nullable();
            $table->text('advance_transaction_id')->nullable();
            $table->text('transaction_id')->nullable();
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
            $table->dropColumn('drop_off_time');
            $table->dropColumn('advance_amount');
            $table->dropColumn('estimated_distance');
            $table->dropColumn('advance_transaction_id');
            $table->dropColumn('transaction_id');
        });
    }
}
