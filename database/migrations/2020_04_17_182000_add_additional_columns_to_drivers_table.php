<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalColumnsToDriversTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('drivers', function (Blueprint $table) {
			$table->integer('vehicle_color_id')->nullable();
			$table->integer('vehicle_make_id')->nullable();
			$table->integer('vehicle_model_id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('drivers', function (Blueprint $table) {
			$table->dropColumn('vehicle_color_id');
			$table->dropColumn('vehicle_make_id');
			$table->dropColumn('vehicle_model_id');
		});
	}
}
