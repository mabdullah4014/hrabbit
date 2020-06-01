<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFurtherAdditionalColumnsToDriversTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('drivers', function (Blueprint $table) {
			$table->string('vin_num')->nullable();
			$table->string('img_car_photo')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('drivers', function (Blueprint $table) {
			$table->dropColumn('vin_num');
			$table->dropColumn('img_car_photo');
		});
	}
}
